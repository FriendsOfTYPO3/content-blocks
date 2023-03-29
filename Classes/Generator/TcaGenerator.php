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
use TYPO3\CMS\ContentBlocks\Event\AfterContentBlocksTcaCompilationEvent;
use TYPO3\CMS\ContentBlocks\Loader\LoaderInterface;
use TYPO3\CMS\Core\Configuration\Event\AfterTcaCompilationEvent;
use TYPO3\CMS\Core\Preparations\TcaPreparation;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @internal Not part of TYPO3's public API.
 */
class TcaGenerator
{
    public function __construct(
        protected LoaderInterface $loader,
        protected EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function __invoke(AfterTcaCompilationEvent $event): void
    {
        $event->setTca(array_replace_recursive($event->getTca(), $this->generate()));
        $event->setTca(
            $this->eventDispatcher->dispatch(
                new AfterContentBlocksTcaCompilationEvent($event->getTca())
            )->getTca()
        );
    }

    public function generate(): array
    {
        $tableDefinitionCollection = $this->loader->load(false);
        $tca = [];
        foreach ($tableDefinitionCollection as $tableName => $tableDefinition) {
            if ($tableDefinitionCollection->isCustomTable($tableDefinition)) {
                $tca[$tableName] = $this->getCollectionTableStandardTca($tableDefinition);
            }
            foreach ($tableDefinition->getPaletteDefinitionCollection() as $paletteDefinition) {
                $tca[$tableName]['palettes'][$paletteDefinition->getIdentifier()] = $paletteDefinition->getTca();
            }
            foreach ($tableDefinition->getTcaColumnsDefinition() as $column) {
                if (!$column->useExistingField()) {
                    $tca[$tableName]['columns'][$column->getUniqueIdentifier()] = $column->getTca();
                }
            }
            foreach ($tableDefinition->getTypeDefinitionCollection() ?? [] as $typeDefinition) {
                $columnsOverrides = [];
                foreach ($typeDefinition->getOverrideColumns() as $overrideColumn) {
                    $overrideTca = $overrideColumn->getTca();
                    unset($overrideTca['config']['type']);
                    $columnsOverrides[$overrideColumn->getIdentifier()] = $overrideTca;
                }
                if ($typeDefinition instanceof ContentElementDefinition) {
                    $typeDefinitionArray = [
                        'previewRenderer' => PreviewRenderer::class,
                        'showitem' => $this->getTtContentStandardShowItem($typeDefinition->getShowItems()),
                    ];
                    if ($columnsOverrides !== []) {
                        $typeDefinitionArray['columnsOverrides'] = $columnsOverrides;
                    }
                    $tca['tt_content']['columns']['bodytext']['config']['search']['andWhere'] ??= $GLOBALS['TCA']['tt_content']['columns']['bodytext']['config']['search']['andWhere'];
                    $tca['tt_content']['columns']['bodytext']['config']['search']['andWhere'] .= $this->extendBodytextSearchAndWhere($typeDefinition);
                } else {
                    $typeDefinitionArray = [
                        'showitem' => $this->getGenericStandardShowItem($typeDefinition->getShowItems()),
                    ];
                }
                $tca[$tableName]['types'][$typeDefinition->getTypeName()] = $typeDefinitionArray;
                $tca[$tableName]['ctrl']['typeicon_classes'][$typeDefinition->getTypeName()] = $typeDefinition->getTypeIconIdentifier();
            }
            $tca[$tableName]['ctrl']['searchFields'] = $this->addSearchFields($tableDefinition);
        }

        return GeneralUtility::makeInstance(TcaPreparation::class)->prepare($tca);
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
            '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general',
            '--palette--;;general',
            implode(',', $showItems),
            '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language',
            '--palette--;;language',
            '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access',
            '--palette--;;hidden',
            '--palette--;;access',
        ];

        return implode(',', $parts);
    }

    protected function getCollectionTableStandardShowItem(array $showItems): string
    {
        $generalTab = '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,';
        $appendLanguageTab = ',--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,--palette--;;language';
        $appendAccessTab = ',--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,--palette--;;hidden,--palette--;;access';
        return $generalTab . implode(',', $showItems) . $appendLanguageTab . $appendAccessTab;
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

    public function extendBodytextSearchAndWhere(ContentElementDefinition $contentElementDefinition): string
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
            'hideTable' => !$tableDefinition->isRootTable(),
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
            'typeicon_classes' => [
                'default' => 'content-blocks',
            ],
            'security' => [
                'ignorePageTypeRestriction' => true,
            ],
        ];

        $types = [
            '1' => [
                'showitem' => $this->getCollectionTableStandardShowItem($tableDefinition->getShowItems()),
            ],
        ];

        $palettes = [];
        $palettes['language'] = [
            'showitem' => '
                        sys_language_uid,l18n_parent
                    ',
        ];
        $palettes['hidden'] = [
            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.visibility',
            'showitem' => 'hidden',
        ];
        $palettes['access'] = [
            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.access',
            'showitem' => '
                        starttime;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:starttime_formlabel,
                        endtime;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:endtime_formlabel,
                        --linebreak--,
                        fe_group;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:fe_group_formlabel,
                        --linebreak--,editlock
                    ',
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

        if (!$tableDefinition->isRootTable()) {
            $columns['foreign_table_parent_uid'] = [
                'config' => [
                    'type' => 'passthrough',
                ],
            ];
        }

        return [
            'ctrl' => $ctrl,
            'types' => $types,
            'palettes' => $palettes,
            'columns' => $columns,
        ];
    }
}
