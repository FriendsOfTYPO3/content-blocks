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
use TYPO3\CMS\ContentBlocks\Definition\Capability\RootLevelType;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentTypeInterface;
use TYPO3\CMS\ContentBlocks\Definition\PaletteDefinition;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinition;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\ContentBlocks\Definition\TCA\LinebreakDefinition;
use TYPO3\CMS\ContentBlocks\Definition\TCA\TabDefinition;
use TYPO3\CMS\ContentBlocks\Definition\TcaFieldDefinition;
use TYPO3\CMS\ContentBlocks\FieldConfiguration\FieldType;
use TYPO3\CMS\ContentBlocks\FieldConfiguration\FlexFormFieldConfiguration;
use TYPO3\CMS\ContentBlocks\Registry\LanguageFileRegistry;
use TYPO3\CMS\ContentBlocks\Service\SystemExtensionAvailability;
use TYPO3\CMS\Core\Configuration\Event\AfterTcaCompilationEvent;
use TYPO3\CMS\Core\Preparations\TcaPreparation;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

// @todo changed namespace in v13

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
     * @var string[]|array{type: string, option: string}
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
        [
            'type' => 'Relation',
            'option' => 'allowed',
        ],
        'foreign_table',
        'foreign_field',
        'foreign_table_field',
        'foreign_match_fields',
        'ds',
        'ds_pointerField',
        'exclude',
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
        protected readonly TableDefinitionCollection $tableDefinitionCollection,
        protected readonly EventDispatcherInterface $eventDispatcher,
        protected readonly LanguageFileRegistry $languageFileRegistry,
        protected readonly TcaPreparation $tcaPreparation,
        protected readonly SystemExtensionAvailability $systemExtensionAvailability,
        protected readonly FlexFormGenerator $flexFormGenerator,
    ) {}

    // @todo this is unused in v12. Replace with BeforeTcaOverridesEvent in v13.
    public function __invoke(AfterTcaCompilationEvent $event): void
    {
        $event->setTca(array_replace_recursive($event->getTca(), $this->generate()));

        // Store backup of current TCA, as the helper methods in `fillTypeFieldSelectItems` operate on the global array.
        $tcaBackup = $GLOBALS['TCA'];
        $GLOBALS['TCA'] = $event->getTca();
        $this->fillTypeFieldSelectItems();
        $event->setTca($GLOBALS['TCA']);
        $GLOBALS['TCA'] = $tcaBackup;
    }

    // @todo Remove in v13.
    public function generateTcaOverrides(): array
    {
        $tca = array_replace_recursive($GLOBALS['TCA'], $this->generate());

        // Store backup of current TCA, as the helper methods in `fillTypeFieldSelectItems` operate on the global array.
        $tcaBackup = $GLOBALS['TCA'];
        $GLOBALS['TCA'] = $tca;
        $this->fillTypeFieldSelectItems();
        $tca = $GLOBALS['TCA'];
        $GLOBALS['TCA'] = $tcaBackup;

        return $tca;
    }

    public function generate(): array
    {
        $tca = [];
        foreach ($this->tableDefinitionCollection as $tableDefinition) {
            $tca[$tableDefinition->getTable()] = $this->generateTableTca($tableDefinition);
        }
        $tca = $this->tcaPreparation->prepare($tca);
        return $tca;
    }

    protected function generateTableTca(TableDefinition $tableDefinition): array
    {
        $tca = [];
        if (!isset($GLOBALS['TCA'][$tableDefinition->getTable()])) {
            $tca = $this->generateBaseTableTca($tableDefinition);
        }
        $currentPalettesTca = $tca['palettes'] ?? [];
        $tca['palettes'] = $currentPalettesTca + $this->generatePalettesTca($tableDefinition);
        foreach ($tableDefinition->getTcaFieldDefinitionCollection() as $column) {
            $fieldConfiguration = $column->getFieldConfiguration();
            if ($fieldConfiguration instanceof FlexFormFieldConfiguration) {
                $dataStructure = [];
                foreach ($fieldConfiguration->getFlexFormDefinitions() as $flexFormDefinition) {
                    $dataStructure[$flexFormDefinition->getTypeName()] = $this->flexFormGenerator->generate($flexFormDefinition);
                }
                $fieldConfiguration->setDataStructure($dataStructure);
            }
            if ($tableDefinition->hasTypeField()) {
                $tca['columns'][$column->getUniqueIdentifier()] = $this->getColumnTcaForTableWithTypeField($tableDefinition, $column);
            } else {
                $tca['columns'][$column->getUniqueIdentifier()] = $this->getColumnTcaForTableWithoutTypeField($tableDefinition, $column);
            }
            if (!isset($GLOBALS['TCA'][$tableDefinition->getTable()]['columns'][$column->getUniqueIdentifier()]['label'])) {
                $tca['columns'][$column->getUniqueIdentifier()]['label'] ??= $column->getIdentifier();
            }
        }
        foreach ($tableDefinition->getContentTypeDefinitionCollection() ?? [] as $typeDefinition) {
            $tca['types'][$typeDefinition->getTypeName()] = $this->processTypeDefinition($typeDefinition, $tableDefinition);
            if ($tableDefinition->hasTypeField()) {
                $tca['ctrl']['typeicon_classes'][$typeDefinition->getTypeName()] = $typeDefinition->getTypeIconIdentifier();
            }
            if ($tableDefinition->getContentType() === ContentType::RECORD_TYPE) {
                $tca['ctrl']['typeicon_classes']['default'] ??= $typeDefinition->getTypeIconIdentifier();
                if ($tableDefinition->hasTypeField()) {
                    $tca['ctrl']['typeicon_column'] = $tableDefinition->getTypeField();
                }
            }
            if ($tableDefinition->getContentType() === ContentType::CONTENT_ELEMENT && $typeDefinition->hasColumn('bodytext')) {
                $tca['columns']['bodytext']['config']['search']['andWhere'] ??= $GLOBALS['TCA'][$typeDefinition->getTable()]['columns']['bodytext']['config']['search']['andWhere'] ?? '';
                $tca['columns']['bodytext']['config']['search']['andWhere'] .= $this->extendBodyTextSearchAndWhere($typeDefinition);
            }
        }
        $tca['ctrl']['searchFields'] = $this->generateSearchFields($tableDefinition);
        $tca = $this->cleanTableTca($tca);
        return $tca;
    }

    protected function generatePalettesTca(TableDefinition $tableDefinition): array
    {
        $palettes = [];
        foreach ($tableDefinition->getPaletteDefinitionCollection() as $paletteDefinition) {
            $paletteTca = $this->generatePalettesTcaSingle($paletteDefinition);
            $palettes[$paletteDefinition->getIdentifier()] = $paletteTca;
        }
        return $palettes;
    }

    protected function generatePalettesTcaSingle(PaletteDefinition $paletteDefinition): array
    {
        $paletteTca = [
            'showitem' => $this->generatePaletteShowItem($paletteDefinition),
        ];
        if ($this->languageFileRegistry->isset($paletteDefinition->getContentBlockName(), $paletteDefinition->getLanguagePathLabel())) {
            $paletteTca['label'] = $paletteDefinition->getLanguagePathLabel();
        } elseif ($paletteDefinition->hasLabel()) {
            $paletteTca['label'] = $paletteDefinition->getLabel();
        }
        if ($this->languageFileRegistry->isset($paletteDefinition->getContentBlockName(), $paletteDefinition->getLanguagePathDescription())) {
            $paletteTca['description'] = $paletteDefinition->getLanguagePathDescription();
        } elseif ($paletteDefinition->hasDescription()) {
            $paletteTca['description'] = $paletteDefinition->getDescription();
        }
        return $paletteTca;
    }

    protected function generatePaletteShowItem(PaletteDefinition $paletteDefinition): string
    {
        $showItem = [];
        foreach ($paletteDefinition->getItems() as $fieldIdentifier) {
            if ($fieldIdentifier instanceof LinebreakDefinition) {
                $showItem[] = '--linebreak--';
            } else {
                $showItem[] = $fieldIdentifier;
            }
        }
        $showItemString = implode(',', $showItem);
        return $showItemString;
    }

    protected function fillTypeFieldSelectItems(): void
    {
        foreach ($this->tableDefinitionCollection as $tableDefinition) {
            // This definition has only one type (the default type "1"). There is no type select to add it to.
            if ($tableDefinition->getTypeField() === null) {
                continue;
            }
            foreach ($tableDefinition->getContentTypeDefinitionCollection() ?? [] as $typeDefinition) {
                // @todo Right now we hard-code a new group for the type select of content elements.
                // @todo The default destination should be made configurable, so e.g. the standard
                // @todo group could be chosen.
                if ($tableDefinition->getContentType() === ContentType::CONTENT_ELEMENT) {
                    ExtensionManagementUtility::addTcaSelectItemGroup(
                        table: $typeDefinition->getTable(),
                        field: $tableDefinition->getTypeField(),
                        groupId: 'content_blocks',
                        groupLabel: 'LLL:EXT:content_blocks/Resources/Private/Language/locallang.xlf:content-blocks',
                        position: 'after:default',
                    );
                }
                $group = match ($tableDefinition->getContentType()) {
                    ContentType::CONTENT_ELEMENT => 'content_blocks',
                    // @todo hard-coded "default" group for pages. Make target group configurable.
                    ContentType::PAGE_TYPE => 'default',
                    // @todo Type select grouping is not possible right now for Record Types.
                    ContentType::RECORD_TYPE => '',
                };
                $label = $typeDefinition->getLanguagePathTitle();
                if (!$this->languageFileRegistry->isset($typeDefinition->getName(), $label)) {
                    $label = $typeDefinition->getName();
                }
                ExtensionManagementUtility::addTcaSelectItem(
                    table: $typeDefinition->getTable(),
                    field: $tableDefinition->getTypeField(),
                    item: [
                        'label' => $label,
                        'value' => $typeDefinition->getTypeName(),
                        'icon' => $typeDefinition->getTypeIconIdentifier(),
                        'group' => $group,
                    ]
                );
            }
        }
    }

    protected function processTypeDefinition(ContentTypeInterface $typeDefinition, TableDefinition $tableDefinition): array
    {
        $columnsOverrides = $this->getColumnsOverrides($typeDefinition);
        $tca = match ($tableDefinition->getContentType()) {
            ContentType::CONTENT_ELEMENT => $this->processContentElement($typeDefinition, $columnsOverrides),
            ContentType::PAGE_TYPE => $this->processPageType($typeDefinition, $columnsOverrides),
            ContentType::RECORD_TYPE => $this->processRecordType($typeDefinition, $columnsOverrides, $tableDefinition),
        };
        return $tca;
    }

    protected function processContentElement(ContentTypeInterface $typeDefinition, array $columnsOverrides): array
    {
        $showItem = $this->processShowItem($typeDefinition->getShowItems());
        $typeDefinitionArray = [
            'previewRenderer' => PreviewRenderer::class,
            'showitem' => $this->getContentElementStandardShowItem($showItem),
        ];
        if ($columnsOverrides !== []) {
            $typeDefinitionArray['columnsOverrides'] = $columnsOverrides;
        }
        return $typeDefinitionArray;
    }

    protected function processPageType(ContentTypeInterface $typeDefinition, array $columnsOverrides): array
    {
        $showItem = $this->processShowItem($typeDefinition->getShowItems());
        $typeDefinitionArray = [
            'showitem' => $this->getPageTypeStandardShowItem($showItem),
        ];
        if ($columnsOverrides !== []) {
            $typeDefinitionArray['columnsOverrides'] = $columnsOverrides;
        }
        return $typeDefinitionArray;
    }

    protected function processRecordType(ContentTypeInterface $typeDefinition, array $columnsOverrides, TableDefinition $tableDefinition): array
    {
        $showItem = $this->processShowItem($typeDefinition->getShowItems());
        $typeDefinitionArray = [
            'showitem' => $this->getRecordTypeStandardShowItem($showItem, $tableDefinition),
        ];
        if ($tableDefinition->hasTypeField() && $columnsOverrides !== []) {
            $typeDefinitionArray['columnsOverrides'] = $columnsOverrides;
        }
        return $typeDefinitionArray;
    }

    /**
     * @param array<string|PaletteDefinition|TabDefinition> $showItemInput
     */
    protected function processShowItem(array $showItemInput): string
    {
        $showItem = [];
        foreach ($showItemInput as $inputItem) {
            if ($inputItem instanceof PaletteDefinition) {
                $showItem[] = '--palette--;;' . $inputItem->getIdentifier();
            } elseif ($inputItem instanceof TabDefinition) {
                $tab = '--div--;';
                $languagePathLabel = $inputItem->getLanguagePathLabel();
                if ($this->languageFileRegistry->isset($inputItem->getContentBlockName(), $languagePathLabel)) {
                    $tab .= $languagePathLabel;
                } else {
                    if ($inputItem->hasLabel()) {
                        $tab .= $inputItem->getLabel();
                    } else {
                        $tab .= $inputItem->getIdentifier();
                    }
                }
                $showItem[] = $tab;
            } else {
                $showItem[] = $inputItem;
            }
        }
        $showItemString = implode(',', $showItem);
        return $showItemString;
    }

    protected function getColumnsOverrides(ContentTypeInterface $typeDefinition): array
    {
        $columnsOverrides = [];
        foreach ($typeDefinition->getOverrideColumns() as $overrideColumn) {
            $overrideTca = $overrideColumn->getTca();
            foreach ($this->nonOverridableOptions as $option) {
                $optionKey = $this->getOptionKey($option, $overrideColumn);
                if ($optionKey === null) {
                    continue;
                }
                unset($overrideTca['config'][$optionKey]);
                unset($overrideTca[$optionKey]);
            }
            $columnsOverrides[$overrideColumn->getUniqueIdentifier()] = $this->determineLabelAndDescription(
                $typeDefinition,
                $overrideColumn,
                $overrideTca,
            );
        }
        return $columnsOverrides;
    }

    /**
     * Record Types which didn't define a custom typeField or Collections get their full TCA in their columns section.
     */
    protected function getColumnTcaForTableWithoutTypeField(TableDefinition $tableDefinition, TcaFieldDefinition $column): array
    {
        $standardTypeDefinition = $tableDefinition->getDefaultTypeDefinition();
        $columnTca = $this->determineLabelAndDescription($standardTypeDefinition, $column, $column->getTca());
        return $columnTca;
    }

    /**
     * Content Elements, Page Types and Record Types with defined typeField only get minimal (non-shareable) TCA in
     * their columns section. The actual config goes into columnsOverrides for the related type.
     */
    protected function getColumnTcaForTableWithTypeField(TableDefinition $tableDefinition, TcaFieldDefinition $column): array
    {
        $columnTca = [];
        $iterateOptions = $column->useExistingField() ? $this->extensibleOptions : $this->nonOverridableOptions;
        foreach ($iterateOptions as $option) {
            $optionKey = $this->getOptionKey($option, $column);
            if ($optionKey === null) {
                continue;
            }
            if (array_key_exists($optionKey, $column->getTca()['config'])) {
                $configuration = $column->getTca()['config'][$optionKey];
                // Support for existing flexForm fields.
                if ($optionKey === 'ds') {
                    if ($column->useExistingField()) {
                        $configuration = $this->processExistingFlexForm($column, $tableDefinition);
                        if ($configuration === null) {
                            continue;
                        }
                    } else {
                        // Add default FlexForm definition. This is needed for translation purposes, as special FlexForm
                        // handling is performed even on elements, which didn't define this field in their show items.
                        $configuration['default'] = $this->getDefaultFlexFormDefinition();
                    }
                }

                $columnTca['config'][$optionKey] = $configuration;
            }
            if (array_key_exists($optionKey, $column->getTca())) {
                $columnTca[$optionKey] = $column->getTca()[$optionKey];
            }
        }
        // Add TCA for automatically added typeField.
        if ($tableDefinition->getTypeField() === $column->getIdentifier()) {
            $columnTca = $column->getTca();
        }
        return $columnTca;
    }

    protected function getDefaultFlexFormDefinition(): string
    {
        return '<T3DataStructure>
  <ROOT>
    <type>array</type>
    <el>
      <xmlTitle>
        <label>The Title:</label>
        <config>
            <type>input</type>
            <size>48</size>
        </config>
      </xmlTitle>
    </el>
  </ROOT>
</T3DataStructure>';
    }

    /**
     * Some TCA option have the same key, but have completely different meanings.
     * One example is "allowed" for type group and for type file.
     * In this case, only the one for type group should be ignored for overrides.
     * This is done here by comparing the current field type.
     * Returns null, if this is the option of another type.
     *
     * @param string[]|array{type: string, option: string} $option
     */
    protected function getOptionKey(string|array $option, TcaFieldDefinition $tcaFieldDefinition): ?string
    {
        if (is_string($option)) {
            return $option;
        }
        $fieldType = FieldType::from($option['type']);
        if ($fieldType === $tcaFieldDefinition->getFieldType()) {
            return $option['option'];
        }
        return null;
    }

    /**
     * Label and description overrides. For core fields, fall back to standard translation.
     * For content block fields, fall back to identifier.
     */
    protected function determineLabelAndDescription(ContentTypeInterface $typeDefinition, TcaFieldDefinition $overrideColumn, array $column): array
    {
        $labelPath = $overrideColumn->getLabelPath();
        if ($this->languageFileRegistry->isset($typeDefinition->getName(), $labelPath)) {
            $column['label'] = $labelPath;
        } elseif (($column['label'] ?? '') === '' && !$overrideColumn->useExistingField()) {
            $column['label'] = $overrideColumn->getIdentifier();
        }
        $descriptionPath = $overrideColumn->getDescriptionPath();
        if ($this->languageFileRegistry->isset($typeDefinition->getName(), $descriptionPath)) {
            $column['description'] = $descriptionPath;
        }
        return $column;
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
        $labelCapability = $tableDefinition->getCapability()->getLabelCapability();
        $labelField = null;
        if ($labelCapability->hasLabelField()) {
            $labelFieldIdentifier = $labelCapability->getPrimaryLabelField();
            $labelField = $tableDefinition->getTcaFieldDefinitionCollection()->getField($labelFieldIdentifier);
        }
        // If there is no user-defined label field, use first field as label.
        if (!$labelField?->getFieldType()->isSearchable()) {
            foreach ($tableDefinition->getTcaFieldDefinitionCollection() as $columnFieldDefinition) {
                // Ignore fields for label, which can't be searched properly.
                if (!$columnFieldDefinition->getFieldType()->isSearchable()) {
                    continue;
                }
                $labelField = $columnFieldDefinition;
                break;
            }
        }

        if ($labelField === null) {
            throw new \InvalidArgumentException(
                'Option "labelField" is missing for custom table "' . $tableDefinition->getTable() . '" and no field could be automatically determined.',
                1700157578,
            );
        }

        return $labelField->getUniqueIdentifier();
    }

    protected function getContentElementStandardShowItem(string $showItem): string
    {
        $parts = [
            '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general',
            '--palette--;;general',
            $showItem,
            '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language',
            '--palette--;;language',
            '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access',
            '--palette--;;hidden',
            '--palette--;;access',
            '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes',
            'rowDescription',
        ];

        return implode(',', $parts);
    }

    protected function getRecordTypeStandardShowItem(string $showItem, TableDefinition $tableDefinition): string
    {
        $capability = $tableDefinition->getCapability();
        $parts[] = $showItem;
        if ($capability->isLanguageAware()) {
            $parts[] = '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language';
            $parts[] = '--palette--;;language';
        }
        if ($capability->hasDisabledRestriction() || $capability->hasAccessPalette()) {
            $parts[] = '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access';
            if ($capability->hasDisabledRestriction()) {
                $parts[] = '--palette--;;hidden';
            }
            if ($capability->hasAccessPalette()) {
                $parts[] = '--palette--;;access';
            }
        }
        $showItem = implode(',', $parts);
        return $showItem;
    }

    protected function getPageTypeStandardShowItem(string $showItem): string
    {
        $general = [
            '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general',
            '--palette--;;standard',
            '--palette--;;titleonly',
            'nav_title;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.nav_title_formlabel',
        ];

        $metaTab = [
            '--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.tabs.metadata',
            '--palette--;;metatags',
        ];

        $systemTabs = [
            '--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.tabs.appearance',
            '--palette--;;backend_layout',
            '--palette--;;replace',
            '--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.tabs.behaviour',
            '--palette--;;links',
            '--palette--;;caching',
            '--palette--;;miscellaneous',
            '--palette--;;module',
            '--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.tabs.resources',
            '--palette--;;config',
            '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language',
            '--palette--;;language',
            '--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.tabs.access',
            '--palette--;;visibility',
            '--palette--;;access',
            '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes',
            'rowDescription',
        ];

        $seoTab = [
            '--div--;LLL:EXT:seo/Resources/Private/Language/locallang_tca.xlf:pages.tabs.seo',
            '--palette--;;seo',
            '--palette--;;robots',
            '--palette--;;canonical',
            '--palette--;;sitemap',
            '--div--;LLL:EXT:seo/Resources/Private/Language/locallang_tca.xlf:pages.tabs.socialmedia',
            '--palette--;;opengraph',
            '--palette--;;twittercards',
        ];

        $parts[] = $general;
        if ($showItem !== '') {
            $parts[] = [$showItem];
        }
        $parts[] = $metaTab;
        if ($this->systemExtensionAvailability->isAvailable('seo')) {
            $parts[] = $seoTab;
        }
        $parts[] = $systemTabs;

        $showItem = implode(',', array_merge([], ...$parts));
        return $showItem;
    }

    /**
     * Generate search fields in order to find content elements in global backend search.
     */
    public function generateSearchFields(TableDefinition $tableDefinition): string
    {
        $searchFieldsString = $GLOBALS['TCA'][$tableDefinition->getTable()]['ctrl']['searchFields'] ?? '';
        $searchFields = GeneralUtility::trimExplode(',', $searchFieldsString, true);

        foreach ($tableDefinition->getTcaFieldDefinitionCollection() as $field) {
            if ($field->getFieldType()->isSearchable() && !in_array($field->getUniqueIdentifier(), $searchFields, true)) {
                $searchFields[] = $field->getUniqueIdentifier();
            }
        }

        if ($searchFields === []) {
            return '';
        }
        $searchFieldsCommaSeparated = implode(',', $searchFields);
        return $searchFieldsCommaSeparated;
    }

    protected function extendBodyTextSearchAndWhere(ContentTypeInterface $contentTypeDefinition): string
    {
        $andWhere = '';
        if ($contentTypeDefinition->hasColumn('bodytext')) {
            $andWhere .= ' OR {#CType}=\'' . $contentTypeDefinition->getTypeName() . '\'';
        }

        return $andWhere;
    }

    protected function generateBaseTableTca(TableDefinition $tableDefinition): array
    {
        $defaultTypeDefinition = $tableDefinition->getDefaultTypeDefinition();
        $capability = $tableDefinition->getCapability();
        $palettes = [];
        $columns = [];
        $title = $defaultTypeDefinition->getLanguagePathTitle();
        if (!$this->languageFileRegistry->isset($defaultTypeDefinition->getName(), $title)) {
            $title = $defaultTypeDefinition->getTable();
        }
        $ctrl = [
            'title' => $title,
            'label' => $this->resolveLabelField($tableDefinition),
            'hideTable' => !$tableDefinition->isAggregateRoot(),
            'enablecolumns' => $capability->buildRestrictionsTca(),
        ];

        $labelCapability = $tableDefinition->getCapability()->getLabelCapability();
        if ($labelCapability->hasAdditionalLabelFields()) {
            $ctrl['label_alt'] = $labelCapability->getAdditionalLabelFieldsAsString();
            $ctrl['label_alt_force'] = true;
        }
        if ($labelCapability->hasFallbackLabelFields()) {
            $ctrl['label_alt'] = $labelCapability->getFallbackLabelFieldsAsString();
        }
        if ($tableDefinition->getTypeField() !== null) {
            $ctrl['type'] = $tableDefinition->getTypeField();
        }
        if ($capability->shallTrackAncestorReference()) {
            $ctrl['origUid'] = 't3_origuid';
        }
        if ($capability->isEditLockingEnabled()) {
            $ctrl['editlock'] = 'editlock';
        }
        if ($capability->hasSoftDelete()) {
            $ctrl['delete'] = 'deleted';
        }
        if ($capability->shallTrackCreationDate()) {
            $ctrl['crdate'] = 'crdate';
        }
        if ($capability->shallTrackUpdateDate()) {
            $ctrl['tstamp'] = 'tstamp';
        }
        if ($capability->isWorkspaceAware()) {
            $ctrl['versioningWS'] = true;
        }
        if ($capability->hasInternalDescription()) {
            $ctrl['descriptionColumn'] = 'internal_description';
        }
        if ($capability->hasSortField()) {
            $ctrl['default_sortby'] = $capability->getSortFieldAsString();
        } elseif ($capability->isSortable()) {
            $ctrl['sortby'] = 'sorting';
            $columns['sorting'] = [
                'config' => [
                    'type' => 'passthrough',
                ],
            ];
        }

        $rootLevelCapability = $capability->getRootLevelCapability();
        if ($rootLevelCapability->getRootLevelType() !== RootLevelType::ONLY_ON_PAGES) {
            $ctrl['rootLevel'] = $rootLevelCapability->getRootLevelType()->getTcaValue();
        }
        if ($rootLevelCapability->shallIgnoreRootLevelRestriction()) {
            $ctrl['security']['ignoreRootLevelRestriction'] = true;
        }
        if ($capability->isIgnoreWebMountRestriction()) {
            $ctrl['security']['ignoreWebMountRestriction'] = true;
        }
        if (!$tableDefinition->isAggregateRoot() || $capability->isIgnorePageTypeRestriction()) {
            $ctrl['security']['ignorePageTypeRestriction'] = true;
        }
        if ($capability->isReadOnly()) {
            $ctrl['readOnly'] = true;
        }
        if ($capability->isAdminOnly()) {
            $ctrl['adminOnly'] = true;
        }
        if ($capability->shallBeHiddenAtCopy()) {
            $ctrl['hideAtCopy'] = true;
        }
        if ($capability->hasAppendLabelAtCopy()) {
            $ctrl['prependAtCopy'] = $capability->getAppendLabelAtCopy();
        }
        if ($capability->isLanguageAware()) {
            $ctrl += [
                'transOrigPointerField' => 'l10n_parent',
                'translationSource' => 'l10n_source',
                'transOrigDiffSourceField' => 'l10n_diffsource',
                'languageField' => 'sys_language_uid',
            ];
        }

        if ($capability->isLanguageAware()) {
            $palettes['language'] = [
                'showitem' => 'sys_language_uid,l10n_parent',
            ];
        }
        if ($capability->hasDisabledRestriction()) {
            $palettes['hidden'] = [
                'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.visibility',
                'showitem' => 'hidden',
            ];
        }
        $access = $capability->buildAccessShowItemTca();
        if ($access !== '') {
            $palettes['access'] = [
                'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.access',
                'showitem' => $access,
            ];
        }

        if ($capability->isEditLockingEnabled()) {
            $columns['editlock'] = [
                'exclude' => true,
                'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:editlock',
                'config' => [
                    'type' => 'check',
                    'renderType' => 'checkboxToggle',
                ],
            ];
        }
        if ($capability->hasDisabledRestriction()) {
            $columns['hidden'] = [
                'exclude' => true,
                'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.disable',
                'config' => [
                    'type' => 'check',
                    'renderType' => 'checkboxToggle',
                ],
            ];
        }
        if ($capability->hasUserGroupRestriction()) {
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
        }
        if ($capability->hasStartTimeRestriction()) {
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
        }
        if ($capability->hasEndTimeRestriction()) {
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
        }
        if ($capability->isLanguageAware()) {
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
        }

        // This is a child table and can only be created by the parent.
        if (!$tableDefinition->isAggregateRoot()) {
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

    protected function cleanTableTca(array $tca): array
    {
        if (isset($tca['palettes']) && $tca['palettes'] === []) {
            unset($tca['palettes']);
        }
        foreach ($tca['columns'] ?? [] as $identifier => $column) {
            if ($tca['columns'][$identifier] === []) {
                unset($tca['columns'][$identifier]);
            }
        }
        return $tca;
    }
}
