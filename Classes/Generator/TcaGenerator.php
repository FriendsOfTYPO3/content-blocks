<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace TYPO3\CMS\ContentBlocks\Generator;

use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\ContentBlocks\Backend\Preview\PreviewRenderer;
use TYPO3\CMS\ContentBlocks\Definition\ContentElementDefinition;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinition;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\ContentBlocks\Definition\TcaFieldDefinition;
use TYPO3\CMS\ContentBlocks\Event\AfterContentBlocksTcaCompilationEvent;
use TYPO3\CMS\ContentBlocks\Loader\LoaderInterface;
use TYPO3\CMS\ContentBlocks\Registry\LanguageFileRegistryInterface;
use TYPO3\CMS\ContentBlocks\Service\TypeDefinitionLabelService;
use TYPO3\CMS\Core\Configuration\Event\AfterTcaCompilationEvent;
use TYPO3\CMS\Core\Preparations\TcaPreparation;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @internal Not part of TYPO3's public API.
 */
class TcaGenerator
{
    /**
     * These fields are required for automatic default SQL schema generation
     * or have to be otherwise the same for each field. Thus, these fields
     * can't be overridden through type overrides.
     *
     * @var string[]
     */
    protected array $nonOverridableOptions = [
        'type',
        'relationship',
        'dbType',
        'nullable',
        'MM',
        'MM_opposite_field',
        'MM_hasUidField',
        'MM_oppositeUsage',
        'allowed',
        'foreign_table',
        'foreign_field',
        'foreign_table_field',
        'foreign_match_fields',
        'ds',
        'ds_pointerField',
    ];

    /**
     * Associative arrays, which can be extended without the need to
     * use columnsOverrides.
     *
     * @var string[]
     */
    protected array $extensibleOptions = [
        'ds',
    ];

    public function __construct(
        protected readonly LoaderInterface $loader,
        protected readonly EventDispatcherInterface $eventDispatcher,
        protected readonly TypeDefinitionLabelService $typeDefinitionLabelService,
        protected readonly LanguageFileRegistryInterface $languageFileRegistry,
        protected readonly TcaPreparation $tcaPreparation,
    ) {
    }

    public function __invoke(AfterTcaCompilationEvent $event): void
    {
        $tableDefinitionCollection = $this->loader->load(false);

        // Use helper methods to add group "Content Blocks" and add the content block to the type item list.
        // Store backup of current TCA, as the helper methods operate on the global array.
        $tcaBackup = $GLOBALS['TCA'];
        $GLOBALS['TCA'] = $event->getTca();
        foreach ($tableDefinitionCollection as $tableDefinition) {
            foreach ($tableDefinition->getTypeDefinitionCollection() ?? [] as $typeDefinition) {
                // This definition has only one type (the default type "1"). There is no type select to add it to.
                if ($tableDefinition->getTypeField() === null) {
                    continue;
                }
                if ($typeDefinition instanceof ContentElementDefinition) {
                    ExtensionManagementUtility::addTcaSelectItemGroup(
                        table: $typeDefinition->getTable(),
                        field: $tableDefinition->getTypeField(),
                        groupId: 'content_blocks',
                        groupLabel: 'LLL:EXT:content_blocks/Resources/Private/Language/locallang.xlf:content-blocks',
                        position: 'after:default',
                    );
                }
                ExtensionManagementUtility::addTcaSelectItem(
                    table: $typeDefinition->getTable(),
                    field: $tableDefinition->getTypeField(),
                    item: [
                        'label' => $this->typeDefinitionLabelService->getLLLPathForTitle($typeDefinition),
                        'value' => $typeDefinition->getTypeName(),
                        'icon' => $typeDefinition instanceof ContentElementDefinition ? $typeDefinition->getWizardIconIdentifier() : '',
                        'group' => $typeDefinition instanceof ContentElementDefinition ? 'content_blocks' : '',
                    ]
                );
            }
        }
        $event->setTca($GLOBALS['TCA']);
        // Restore backup, see comment above.
        $GLOBALS['TCA'] = $tcaBackup;

        $event->setTca(array_replace_recursive($event->getTca(), $this->generate($tableDefinitionCollection)));
        $event->setTca($this->eventDispatcher->dispatch(new AfterContentBlocksTcaCompilationEvent($event->getTca()))->getTca());
    }

    public function generate(TableDefinitionCollection $tableDefinitionCollection): array
    {
        $tca = [];
        foreach ($tableDefinitionCollection as $tableName => $tableDefinition) {
            if (!isset($GLOBALS['TCA'][$tableName])) {
                $tca[$tableName] = $this->getCollectionTableStandardTca($tableDefinition);
            }
            foreach ($tableDefinition->getPaletteDefinitionCollection() as $paletteDefinition) {
                $tca[$tableName]['palettes'][$paletteDefinition->getIdentifier()] = $paletteDefinition->getTca();
            }
            foreach ($tableDefinition->getTcaColumnsDefinition() as $column) {
                // Fields on root tables are defined with minimal setup. Actual configuration goes into type overrides.
                // But only, if a custom typeField is defined.
                if ($tableDefinition->isRootTable() && $tableDefinition->getTypeField() !== null) {
                    $iterateOptions = $column->useExistingField() ? $this->extensibleOptions : $this->nonOverridableOptions;
                    foreach ($iterateOptions as $option) {
                        if (array_key_exists($option, $column->getTca()['config'])) {
                            $configuration = $column->getTca()['config'][$option];
                            // Support for existing flexForm fields.
                            if ($column->useExistingField() && $option === 'ds') {
                                $configuration = $this->processExistingFlexForm($column, $tableDefinition);
                                if ($configuration === null) {
                                    continue;
                                }
                            }
                            $tca[$tableName]['columns'][$column->getUniqueIdentifier()]['config'][$option] = $configuration;
                        }
                    }
                }
                // Non-root tables should not be able to reuse fields. They can only be reused as a whole.
                // Also, root tables which didn't define a custom typeField get the full TCA.
                if (!$tableDefinition->isRootTable() || $tableDefinition->getTypeField() === null) {
                    $tca[$tableName]['columns'][$column->getUniqueIdentifier()] = $column->getTca();
                    $tca[$tableName]['columns'][$column->getUniqueIdentifier()]['label'] ??= $column->getLanguagePath()->getCurrentPath() . '.label';
                    $tca[$tableName]['columns'][$column->getUniqueIdentifier()]['description'] ??= $column->getLanguagePath()->getCurrentPath() . '.description';
                }
                // Newly created fields are enabled to be configured in user permissions by default.
                if (!$column->useExistingField()) {
                    $tca[$tableName]['columns'][$column->getUniqueIdentifier()]['exclude'] = true;
                }
            }
            foreach ($tableDefinition->getTypeDefinitionCollection() ?? [] as $typeDefinition) {
                $columnsOverrides = [];
                foreach ($typeDefinition->getOverrideColumns() as $overrideColumn) {
                    $overrideTca = $overrideColumn->getTca();
                    foreach ($this->nonOverridableOptions as $option) {
                        unset($overrideTca['config'][$option]);
                    }
                    $columnsOverrides[$overrideColumn->getUniqueIdentifier()] = $overrideTca;
                    // Label and description overrides. For core fields, fall back to standard translation.
                    // For content block fields, fall back to identifier.
                    $languagePath = $overrideColumn->getLanguagePath();
                    if (!isset($columnsOverrides[$overrideColumn->getUniqueIdentifier()]['label'])) {
                        $labelPath = '.label';
                        if ($this->languageFileRegistry->isset($typeDefinition->getName(), $languagePath->getPathWithoutBase() . $labelPath)) {
                            $columnsOverrides[$overrideColumn->getUniqueIdentifier()]['label'] = $languagePath->getCurrentPath() . $labelPath;
                        } elseif (!$overrideColumn->useExistingField()) {
                            $columnsOverrides[$overrideColumn->getUniqueIdentifier()]['label'] = $overrideColumn->getIdentifier();
                        }
                    }
                    if (!isset($columnsOverrides[$overrideColumn->getUniqueIdentifier()]['description'])) {
                        $descriptionPath = '.description';
                        if ($this->languageFileRegistry->isset($typeDefinition->getName(), $languagePath->getPathWithoutBase() . $descriptionPath)) {
                            $columnsOverrides[$overrideColumn->getUniqueIdentifier()]['description'] = $languagePath->getCurrentPath() . $descriptionPath;
                        }
                    }
                }
                if ($typeDefinition instanceof ContentElementDefinition) {
                    $typeDefinitionArray = [
                        'previewRenderer' => PreviewRenderer::class,
                        'showitem' => $this->getTtContentStandardShowItem($typeDefinition->getShowItems()),
                    ];
                    if ($columnsOverrides !== []) {
                        $typeDefinitionArray['columnsOverrides'] = $columnsOverrides;
                    }
                    if ($typeDefinition->hasColumn('bodytext')) {
                        $tca['tt_content']['columns']['bodytext']['config']['search']['andWhere'] ??= $GLOBALS['TCA']['tt_content']['columns']['bodytext']['config']['search']['andWhere'] ?? '';
                        $tca['tt_content']['columns']['bodytext']['config']['search']['andWhere'] .= $this->extendBodytextSearchAndWhere($typeDefinition);
                    }
                } else {
                    $typeDefinitionArray = [
                        'showitem' => $this->getGenericStandardShowItem($typeDefinition->getShowItems()),
                    ];
                    $tca[$tableName]['ctrl']['typeicon_classes']['default'] = 'content-blocks';
                }
                $tca[$tableName]['types'][$typeDefinition->getTypeName()] = $typeDefinitionArray;
                if ($tableDefinition->getTypeField() !== null) {
                    $tca[$tableName]['ctrl']['typeicon_classes'][$typeDefinition->getTypeName()] = $typeDefinition->getTypeIconIdentifier();
                }
            }
            $tca[$tableName]['ctrl']['searchFields'] = $this->addSearchFields($tableDefinition);
        }

        return $this->tcaPreparation->prepare($tca);
    }

    /**
     * To be compatible with existing flexForm fields, the type field has to be present inside `ds_pointerField`.
     * If this is not the case, the flexForm field cannot be reused.
     *
     * Furthermore, this method handles the adjustment for multiple pointer fields. The most prominent example would be
     * `pi_flexform`, which points to `list_type` and `CType`. Content Blocks only uses CType by default for Content
     * Elements. Hence, the identifier needs to be prepended by '*,' to match any `list_type`.
     *
     * Example:
     *
     *     // Default only one pointer field
     *     'ds' => [
     *         'example_flexfield' => '...'
     *     ]
     *
     *     // Core config for pi_flexform with CType at the second position
     *     'ds_pointerField' => 'list_type,CType'
     *
     *     // "*," prepended to match anything at position 1
     *     'ds' => [
     *         '*,example_flexfield' => '...'
     *     ]
     */
    protected function processExistingFlexForm(TcaFieldDefinition $column, TableDefinition $tableDefinition): ?array
    {
        $existingDsPointerField = $GLOBALS['TCA'][$tableDefinition->getTable()]['columns'][$column->getUniqueIdentifier()]['config']['ds_pointerField'];
        $existingDsPointerFieldArray = GeneralUtility::trimExplode(',', $existingDsPointerField);
        $dsConfiguration = $column->getTca()['config']['ds'];
        $typeSwitchField = $tableDefinition->getTypeField();
        $fieldPositionInDsPointerFields = array_search($typeSwitchField, $existingDsPointerFieldArray);
        // type field is not compatible.
        if ($fieldPositionInDsPointerFields === false) {
            return null;
        }
        $pointerFieldsCount = count($existingDsPointerFieldArray);
        // If only one valid field exists, no need to add wildcards.
        if ($pointerFieldsCount === 1) {
            return $dsConfiguration;
        }
        $newDsConfiguration = [];
        foreach (array_keys($dsConfiguration) as $dsKey) {
            $dsKeys = [];
            foreach (range(0, $pointerFieldsCount - 1) as $index) {
                if ($index === $fieldPositionInDsPointerFields) {
                    $dsKeys[] = $dsKey;
                    continue;
                }
                $dsKeys[] = '*';
            }
            $newDsConfiguration[implode(',', $dsKeys)] = $dsConfiguration[$dsKey];
        }
        return $newDsConfiguration;
    }

    protected function resolveLabelField(TableDefinition $tableDefinition): string
    {
        $labelFallback = '';
        if ($tableDefinition->hasUseAsLabel()) {
            $labelFallback = $tableDefinition->getUseAsLabel();
        } else {
            // If there is no user-defined label field, use first field as label.
            foreach ($tableDefinition->getTcaColumnsDefinition() as $columnFieldDefinition) {
                $labelFallback = $columnFieldDefinition->getUniqueIdentifier();
                break;
            }
        }
        return $labelFallback;
    }

    protected function getTtContentStandardShowItem(array $columns): string
    {
        $parts = [
            '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general',
            '--palette--;;general',
            implode(',', $columns),
            '--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance',
            '--palette--;;frames',
            '--palette--;;appearanceLinks',
            '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language',
            '--palette--;;language',
            '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access',
            '--palette--;;hidden',
            '--palette--;;access',
            '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories',
            'categories',
            '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes',
            'rowDescription',
            '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended',
        ];

        return implode(',', $parts);
    }

    protected function getGenericStandardShowItem(array $showItems): string
    {
        $parts = [
            implode(',', $showItems),
            '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language',
            '--palette--;;language',
            '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access',
            '--palette--;;hidden',
            '--palette--;;access',
        ];

        return implode(',', $parts);
    }

    /**
     * Add search fields to find content elements
     */
    public function addSearchFields(TableDefinition $tableDefinition): string
    {
        $searchFieldsString = $GLOBALS['TCA'][$tableDefinition->getTable()]['ctrl']['searchFields'] ?? '';
        $searchFields = GeneralUtility::trimExplode(',', $searchFieldsString, true);

        foreach ($tableDefinition->getTcaColumnsDefinition() as $field) {
            if ($field->getFieldType()->isSearchable() && !in_array($field->getUniqueIdentifier(), $searchFields, true)) {
                $searchFields[] = $field->getUniqueIdentifier();
            }
        }

        if ($searchFields === []) {
            return '';
        }

        return implode(',', $searchFields);
    }

    protected function extendBodytextSearchAndWhere(ContentElementDefinition $contentElementDefinition): string
    {
        $andWhere = '';
        if ($contentElementDefinition->hasColumn('bodytext')) {
            $andWhere .= ' OR {#CType}=\'' . $contentElementDefinition->getTypeName() . '\'';
        }

        return $andWhere;
    }

    protected function getCollectionTableStandardTca(TableDefinition $tableDefinition): array
    {
        $ctrl = [
            'title' => $tableDefinition->getTable(),
            'label' => $this->resolveLabelField($tableDefinition),
            'sortby' => 'sorting',
            'tstamp' => 'tstamp',
            'crdate' => 'crdate',
            'delete' => 'deleted',
            'editlock' => 'editlock',
            'versioningWS' => true,
            'origUid' => 't3_origuid',
            'hideTable' => !$tableDefinition->isRootTable() || !$tableDefinition->isAggregateRoot(),
            'transOrigPointerField' => 'l10n_parent',
            'translationSource' => 'l10n_source',
            'transOrigDiffSourceField' => 'l10n_diffsource',
            'languageField' => 'sys_language_uid',
            'enablecolumns' => [
                'disabled' => 'hidden',
                'starttime' => 'starttime',
                'endtime' => 'endtime',
                'fe_group' => 'fe_group',
            ],
            'security' => [
                'ignorePageTypeRestriction' => true,
            ],
        ];

        $palettes = [];
        $palettes['language'] = [
            'showitem' => 'sys_language_uid,l18n_parent',
        ];
        $palettes['hidden'] = [
            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.visibility',
            'showitem' => 'hidden',
        ];
        $palettes['access'] = [
            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.access',
            'showitem' => implode(',', [
                'starttime;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:starttime_formlabel',
                'endtime;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:endtime_formlabel',
                '--linebreak--',
                'fe_group;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:fe_group_formlabel',
                '--linebreak--',
                'editlock',
            ]),
        ];

        $columns = [];
        $columns['editlock'] = [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:editlock',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
            ],
        ];
        $columns['hidden'] = [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.disable',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
            ],
        ];
        $columns['fe_group'] = [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.fe_group',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'size' => 5,
                'maxitems' => 20,
                'items' => [
                    [
                        'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hide_at_login',
                        'value' => -1,
                    ],
                    [
                        'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.any_login',
                        'value' => -2,
                    ],
                    [
                        'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.usergroups',
                        'value' => '--div--',
                    ],
                ],
                'exclusiveKeys' => '-1,-2',
                'foreign_table' => 'fe_groups',
            ],
        ];
        $columns['starttime'] = [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'datetime',
                'default' => 0,
            ],
            'l10n_mode' => 'exclude',
            'l10n_display' => 'defaultAsReadonly',
        ];
        $columns['endtime'] = [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'datetime',
                'default' => 0,
                'range' => [
                    'upper' => mktime(0, 0, 0, 1, 1, 2038),
                ],
            ],
            'l10n_mode' => 'exclude',
            'l10n_display' => 'defaultAsReadonly',
        ];
        $columns['sys_language_uid'] = [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'language',
            ],
        ];
        $columns['l10n_parent'] = [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'label' => '',
                        'value' => 0,
                    ],
                ],
                'foreign_table' => $tableDefinition->getTable(),
                'foreign_table_where' => 'AND ' . $tableDefinition->getTable() . '.pid=###CURRENT_PID### AND ' . $tableDefinition->getTable() . '.sys_language_uid IN (-1,0)',
                'default' => 0,
            ],
        ];
        $columns['l10n_diffsource'] = [
            'config' => [
                'type' => 'passthrough',
            ],
        ];
        $columns['sorting'] = [
            'config' => [
                'type' => 'passthrough',
            ],
        ];

        if (!$tableDefinition->isRootTable() || !$tableDefinition->isAggregateRoot()) {
            $columns['foreign_table_parent_uid'] = [
                'config' => [
                    'type' => 'passthrough',
                ],
            ];
            $columns['tablenames'] = [
                'config' => [
                    'type' => 'passthrough',
                ],
            ];
            $columns['fieldname'] = [
                'config' => [
                    'type' => 'passthrough',
                ],
            ];
        }

        return [
            'ctrl' => $ctrl,
            'palettes' => $palettes,
            'columns' => $columns,
        ];
    }
}
