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
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentTypeInterface;
use TYPO3\CMS\ContentBlocks\Definition\RootLevelType;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinition;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\ContentBlocks\Definition\TcaFieldDefinition;
use TYPO3\CMS\ContentBlocks\FieldConfiguration\FieldType;
use TYPO3\CMS\ContentBlocks\FieldConfiguration\FlexFormFieldConfiguration;
use TYPO3\CMS\ContentBlocks\Registry\LanguageFileRegistryInterface;
use TYPO3\CMS\ContentBlocks\Service\SystemExtensionAvailabilityInterface;
use TYPO3\CMS\ContentBlocks\Service\TypeDefinitionLabelService;
use TYPO3\CMS\Core\Configuration\Event\AfterTcaCompilationEvent;
// @todo changed namespace in v13
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
        protected readonly TypeDefinitionLabelService $typeDefinitionLabelService,
        protected readonly LanguageFileRegistryInterface $languageFileRegistry,
        protected readonly TcaPreparation $tcaPreparation,
        protected readonly SystemExtensionAvailabilityInterface $systemExtensionAvailability,
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
            if (!isset($GLOBALS['TCA'][$tableDefinition->getTable()])) {
                $tca[$tableDefinition->getTable()] = $this->generateTableTca($tableDefinition);
            }
            foreach ($tableDefinition->getPaletteDefinitionCollection() as $paletteDefinition) {
                $tca[$tableDefinition->getTable()]['palettes'][$paletteDefinition->getIdentifier()] = $paletteDefinition->getTca();
            }
            $isRootTableWithTypeField = $tableDefinition->isRootTable() && $tableDefinition->getTypeField() !== null;
            foreach ($tableDefinition->getTcaColumnsDefinition() as $column) {
                if ($column->getFieldConfiguration() instanceof FlexFormFieldConfiguration) {
                    $fieldConfiguration = $column->getFieldConfiguration();
                    $dataStructure = [];
                    foreach ($fieldConfiguration->getFlexFormDefinitions() as $flexFormDefinition) {
                        $dataStructure[$flexFormDefinition->getTypeName()] = $this->flexFormGenerator->generate($flexFormDefinition);
                    }
                    $fieldConfiguration->setDataStructure($dataStructure);
                }
                if ($isRootTableWithTypeField) {
                    $tca = $this->getTcaForRootTableWithTypeField($tableDefinition, $column, $tca);
                } else {
                    $tca = $this->getTcaForNonRootTableOrWithoutTypeField($tableDefinition, $column, $tca);
                }
                if (!isset($GLOBALS['TCA'][$tableDefinition->getTable()]['columns'][$column->getUniqueIdentifier()]['label'])) {
                    $tca[$tableDefinition->getTable()]['columns'][$column->getUniqueIdentifier()]['label'] ??= $column->getIdentifier();
                }
            }
            foreach ($tableDefinition->getTypeDefinitionCollection() ?? [] as $typeDefinition) {
                $tca = $this->processTypeDefinition($typeDefinition, $tableDefinition, $tca);
            }
            $tca[$tableDefinition->getTable()]['ctrl']['searchFields'] = $this->addSearchFields($tableDefinition);
        }

        return $this->tcaPreparation->prepare($tca);
    }

    protected function fillTypeFieldSelectItems(): void
    {
        foreach ($this->tableDefinitionCollection as $tableDefinition) {
            // This definition has only one type (the default type "1"). There is no type select to add it to.
            if ($tableDefinition->getTypeField() === null) {
                continue;
            }
            foreach ($tableDefinition->getTypeDefinitionCollection() ?? [] as $typeDefinition) {
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
                // @todo hard-coded "default" group for pages. Make target group configurable.
                $group = match ($tableDefinition->getContentType()) {
                    ContentType::CONTENT_ELEMENT => 'content_blocks',
                    ContentType::PAGE_TYPE => 'default',
                    ContentType::RECORD_TYPE => '',
                };
                $label = $this->typeDefinitionLabelService->getLLLPathForTitle($typeDefinition);
                $key = $this->typeDefinitionLabelService->buildTitleKey($typeDefinition);
                if (!$this->languageFileRegistry->isset($typeDefinition->getName(), $key)) {
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

    protected function processTypeDefinition(ContentTypeInterface $typeDefinition, TableDefinition $tableDefinition, array $tca): array
    {
        $columnsOverrides = $this->getColumnsOverrides($typeDefinition);
        $tca = match ($tableDefinition->getContentType()) {
            ContentType::CONTENT_ELEMENT => $this->processContentElement($typeDefinition, $columnsOverrides, $tca),
            ContentType::PAGE_TYPE => $this->processPageType($typeDefinition, $columnsOverrides, $tca),
            ContentType::RECORD_TYPE => $this->processRecordType($typeDefinition, $columnsOverrides, $tca, $tableDefinition),
        };
        if ($tableDefinition->getTypeField() !== null) {
            $tca[$typeDefinition->getTable()]['ctrl']['typeicon_classes'][$typeDefinition->getTypeName()] = $typeDefinition->getTypeIconIdentifier();
        }
        return $tca;
    }

    protected function processContentElement(ContentTypeInterface $typeDefinition, array $columnsOverrides, array $tca): array
    {
        $typeDefinitionArray = [
            'previewRenderer' => PreviewRenderer::class,
            'showitem' => $this->getContentElementStandardShowItem($typeDefinition->getShowItems()),
        ];
        if ($columnsOverrides !== []) {
            $typeDefinitionArray['columnsOverrides'] = $columnsOverrides;
        }
        if ($typeDefinition->hasColumn('bodytext')) {
            $tca[$typeDefinition->getTable()]['columns']['bodytext']['config']['search']['andWhere'] ??= $GLOBALS['TCA'][$typeDefinition->getTable()]['columns']['bodytext']['config']['search']['andWhere'] ?? '';
            $tca[$typeDefinition->getTable()]['columns']['bodytext']['config']['search']['andWhere'] .= $this->extendBodyTextSearchAndWhere($typeDefinition);
        }
        $tca[$typeDefinition->getTable()]['types'][$typeDefinition->getTypeName()] = $typeDefinitionArray;
        return $tca;
    }

    protected function processPageType(ContentTypeInterface $typeDefinition, array $columnsOverrides, array $tca): array
    {
        $typeDefinitionArray = [
            'showitem' => $this->getPageTypeStandardShowItem($typeDefinition->getShowItems()),
        ];
        if ($columnsOverrides !== []) {
            $typeDefinitionArray['columnsOverrides'] = $columnsOverrides;
        }
        $tca[$typeDefinition->getTable()]['types'][$typeDefinition->getTypeName()] = $typeDefinitionArray;
        return $tca;
    }

    protected function processRecordType(ContentTypeInterface $typeDefinition, array $columnsOverrides, array $tca, TableDefinition $tableDefinition): array
    {
        $typeDefinitionArray = [
            'showitem' => $this->getRecordTypeStandardShowItem($typeDefinition->getShowItems(), $tableDefinition),
        ];
        if ($tableDefinition->getTypeField() !== null) {
            $tca[$typeDefinition->getTable()]['ctrl']['typeicon_column'] = $tableDefinition->getTypeField();
        }
        $tca[$typeDefinition->getTable()]['ctrl']['typeicon_classes']['default'] ??= $typeDefinition->getTypeIconIdentifier();

        if ($tableDefinition->getTypeField() !== null && $columnsOverrides !== []) {
            $typeDefinitionArray['columnsOverrides'] = $columnsOverrides;
        }
        $tca[$typeDefinition->getTable()]['types'][$typeDefinition->getTypeName()] = $typeDefinitionArray;
        return $tca;
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
     * Fields on root tables are defined with minimal setup. Actual configuration goes into type overrides.
     * But only, if a custom typeField is defined.
     */
    protected function getTcaForRootTableWithTypeField(TableDefinition $tableDefinition, TcaFieldDefinition $column, array $tca): array
    {
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

                $tca[$tableDefinition->getTable()]['columns'][$column->getUniqueIdentifier()]['config'][$optionKey] = $configuration;
            }
            if (array_key_exists($optionKey, $column->getTca())) {
                $tca[$tableDefinition->getTable()]['columns'][$column->getUniqueIdentifier()][$optionKey] = $column->getTca()[$optionKey];
            }
        }
        // Add TCA for automatically added typeField.
        if ($tableDefinition->getTypeField() === $column->getIdentifier()) {
            $tca[$tableDefinition->getTable()]['columns'][$column->getUniqueIdentifier()] = $column->getTca();
        }
        return $tca;
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
     * Non-root tables should not be able to reuse fields. They can only be reused as a whole.
     * Also, root tables which didn't define a custom typeField get the full TCA.
     */
    protected function getTcaForNonRootTableOrWithoutTypeField(TableDefinition $tableDefinition, TcaFieldDefinition $column, array $tca): array
    {
        $standardTypeDefinition = $tableDefinition->getTypeDefinitionCollection()->getFirst();
        $columnTca = $this->determineLabelAndDescription($standardTypeDefinition, $column, $column->getTca());
        $tca[$tableDefinition->getTable()]['columns'][$column->getUniqueIdentifier()] = $columnTca;
        return $tca;
    }

    /**
     * Label and description overrides. For core fields, fall back to standard translation.
     * For content block fields, fall back to identifier.
     */
    protected function determineLabelAndDescription(ContentTypeInterface $typeDefinition, TcaFieldDefinition $overrideColumn, array $column): array
    {
        $labelPath = $overrideColumn->getLabelPath();
        if (!isset($column['label'])) {
            if ($this->languageFileRegistry->isset($typeDefinition->getName(), $labelPath)) {
                $column['label'] = $labelPath;
            } elseif (!$overrideColumn->useExistingField()) {
                $column['label'] = $overrideColumn->getIdentifier();
            }
        }
        $descriptionPath = $overrideColumn->getDescriptionPath();
        if (!isset($column['description'])) {
            if ($this->languageFileRegistry->isset($typeDefinition->getName(), $descriptionPath)) {
                $column['description'] = $descriptionPath;
            }
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
            $labelField = $tableDefinition->getTcaColumnsDefinition()->getField($labelFieldIdentifier);
        }
        // If there is no user-defined label field, use first field as label.
        if (!$labelField?->getFieldType()->isSearchable()) {
            foreach ($tableDefinition->getTcaColumnsDefinition() as $columnFieldDefinition) {
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

    protected function getContentElementStandardShowItem(array $showItems): string
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
            '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes',
            'rowDescription',
        ];

        return implode(',', $parts);
    }

    protected function getRecordTypeStandardShowItem(array $showItems, TableDefinition $tableDefinition): string
    {
        $capability = $tableDefinition->getCapability();
        $parts[] = implode(',', $showItems);
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

    protected function getPageTypeStandardShowItem(array $showItems): string
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
        $parts[] = $showItems;
        $parts[] = $metaTab;
        if ($this->systemExtensionAvailability->isAvailable('seo')) {
            $parts[] = $seoTab;
        }
        $parts[] = $systemTabs;

        $showItem = implode(',', array_merge([], ...$parts));
        return $showItem;
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

    protected function extendBodyTextSearchAndWhere(ContentTypeInterface $contentTypeDefinition): string
    {
        $andWhere = '';
        if ($contentTypeDefinition->hasColumn('bodytext')) {
            $andWhere .= ' OR {#CType}=\'' . $contentTypeDefinition->getTypeName() . '\'';
        }

        return $andWhere;
    }

    protected function generateTableTca(TableDefinition $tableDefinition): array
    {
        $defaultTypeDefinition = $tableDefinition->getDefaultTypeDefinition();
        $capability = $tableDefinition->getCapability();
        $palettes = [];
        $columns = [];
        // @todo Right now, for inline Collections, the label of the parent Content Block is used for the title, as the
        // @todo vendor and name are inherited from it.
        // @todo The correct title should be the according field label. This information is not available here, though.
        $title = $this->typeDefinitionLabelService->getLLLPathForTitle($defaultTypeDefinition);
        $key = $this->typeDefinitionLabelService->buildTitleKey($defaultTypeDefinition);
        if (!$this->languageFileRegistry->isset($defaultTypeDefinition->getName(), $key)) {
            $title = $defaultTypeDefinition->getName();
        }
        $ctrl = [
            'title' => $title,
            'label' => $this->resolveLabelField($tableDefinition),
            'hideTable' => !$tableDefinition->isRootTable() || !$tableDefinition->isAggregateRoot(),
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
