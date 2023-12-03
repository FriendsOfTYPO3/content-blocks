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

namespace TYPO3\CMS\ContentBlocks\Definition\Factory;

use TYPO3\CMS\ContentBlocks\Definition\Capability\LabelCapability;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;
use TYPO3\CMS\ContentBlocks\Definition\Factory\Processing\ProcessedFieldsResult;
use TYPO3\CMS\ContentBlocks\Definition\Factory\Processing\ProcessingInput;
use TYPO3\CMS\ContentBlocks\Definition\FlexForm\ContainerDefinition;
use TYPO3\CMS\ContentBlocks\Definition\FlexForm\FlexFormDefinition;
use TYPO3\CMS\ContentBlocks\Definition\FlexForm\SectionDefinition;
use TYPO3\CMS\ContentBlocks\Definition\FlexForm\SheetDefinition;
use TYPO3\CMS\ContentBlocks\Definition\PaletteDefinition;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinition;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\ContentBlocks\Definition\TCA\LinebreakDefinition;
use TYPO3\CMS\ContentBlocks\Definition\TCA\TabDefinition;
use TYPO3\CMS\ContentBlocks\Definition\TcaFieldDefinition;
use TYPO3\CMS\ContentBlocks\Definition\TcaFieldDefinitionCollection;
use TYPO3\CMS\ContentBlocks\FieldConfiguration\FieldType;
use TYPO3\CMS\ContentBlocks\Loader\LoadedContentBlock;
use TYPO3\CMS\ContentBlocks\Registry\AutomaticLanguageKeysRegistry;
use TYPO3\CMS\ContentBlocks\Registry\AutomaticLanguageSource;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;

/**
 * @internal Not part of TYPO3's public API.
 */
final class TableDefinitionCollectionFactory
{
    public function __construct(protected readonly AutomaticLanguageKeysRegistry $automaticLanguageKeysRegistry) {}

    /**
     * This property tracks Collection foreign_table parent references.
     * It is needed, because there can be a references to a Content Block,
     * which isn't processed yet. Thus, it needs to be collected throughout the run.
     *
     * @var array<string, array>
     */
    private array $parentReferences = [];

    public function createFromLoadedContentBlocks(ContentBlockRegistry $contentBlockRegistry): TableDefinitionCollection
    {
        $tableDefinitionCollection = new TableDefinitionCollection();
        $tableDefinitionList = [];
        foreach ($contentBlockRegistry->getAll() as $contentBlock) {
            $table = $contentBlock->getYaml()['table'];
            $languagePath = new LanguagePath('LLL:' . $contentBlock->getExtPath() . '/' . ContentBlockPathUtility::getLanguageFilePath());
            $processingInput = new ProcessingInput(
                yaml: $contentBlock->getYaml(),
                contentBlock: $contentBlock,
                table: $table,
                rootTable: $table,
                languagePath: $languagePath,
                contentType: $contentBlock->getContentType(),
                tableDefinitionList: $tableDefinitionList,
            );
            $tableDefinitionList = $this->processFields($processingInput);
        }
        $mergedTableDefinitionList = $this->mergeProcessingResult($tableDefinitionList);
        foreach ($mergedTableDefinitionList as $table => $tableDefinition) {
            $newTableDefinition = TableDefinition::createFromTableArray($table, $tableDefinition);
            // Enrich Collections with parent reference information.
            if (isset($this->parentReferences[$newTableDefinition->getTable()])) {
                $references = $this->parentReferences[$newTableDefinition->getTable()];
                $tcaFieldDefinitionCollection = TcaFieldDefinitionCollection::createFromArray($references, $newTableDefinition->getTable());
                $newTableDefinition = $this->enrichTableDefinition($tcaFieldDefinitionCollection, $newTableDefinition);
            }
            $tableDefinitionCollection->addTable($newTableDefinition);
        }
        // Reset state.
        $this->parentReferences = [];
        return $tableDefinitionCollection;
    }

    private function mergeProcessingResult(array $tableDefinitionList): array
    {
        $mergedResult = [];
        foreach ($tableDefinitionList as $table => $definition) {
            $mergedResult[$table] = array_replace_recursive(...array_reverse($definition['tableDefinitions']));
            $mergedResult[$table]['typeDefinitions'] = $definition['typeDefinitions'];
        }
        return $mergedResult;
    }

    private function enrichTableDefinition(
        TcaFieldDefinitionCollection $references,
        TableDefinition $newTableDefinition,
    ): TableDefinition {
        $newTableDefinition = $newTableDefinition->withParentReferences($references);
        // If root Content Type is a Content Element, allow the external table to be put in standard pages.
        foreach ($references as $reference) {
            if ($reference->getParentContentType() === ContentType::CONTENT_ELEMENT) {
                $capability = $newTableDefinition->getCapability();
                $capability = $capability->withIgnorePageTypeRestriction(true);
                $newTableDefinition = $newTableDefinition->withCapability($capability);
            }
        }
        return $newTableDefinition;
    }

    private function processFields(ProcessingInput $input): array
    {
        $result = new ProcessedFieldsResult($input);
        if ($input->isRootTable()) {
            $languagePathTitle = 'title';
            $languagePathDescription = 'description';
        } else {
            $languagePathTitle = '.label';
            $languagePathDescription = '.description';
        }
        $languagePathTitle = $input->languagePath->getCurrentPath() . $languagePathTitle;
        $languagePathDescription = $input->languagePath->getCurrentPath() . $languagePathDescription;
        if ($input->isRootTable()) {
            $label = (string)($input->yaml['label'] ?? '');
            $label = $label !== '' ? $label : $input->contentBlock->getName();
            $description = $input->yaml['description'] ?? '';
            $description = $description !== '' ? $description : 'Description for ' . $input->contentType->getHumanReadable() . ' ' . $label;
            $languagePathSource = new AutomaticLanguageSource($languagePathTitle, $label);
            $descriptionPathSource = new AutomaticLanguageSource($languagePathDescription, $description);
            $this->automaticLanguageKeysRegistry->addKey($input->contentBlock, $languagePathSource);
            $this->automaticLanguageKeysRegistry->addKey($input->contentBlock, $descriptionPathSource);
        }
        $result->contentType->languagePathTitle = $languagePathTitle;
        $result->contentType->languagePathDescription = $languagePathDescription;

        $yamlFields = $input->yaml['fields'] ?? [];

        // Automatically add a `type` field for record types.
        if (
            $result->tableDefinition->contentType === ContentType::RECORD_TYPE
            && $result->tableDefinition->typeField !== null
        ) {
            $yamlFields = $this->prependTypeFieldForRecordType($yamlFields, $result);
        }
        if (
            $result->tableDefinition->contentType === ContentType::RECORD_TYPE
            && ($input->yaml['internalDescription'] ?? false)
        ) {
            $yamlFields = $this->appendInternalDescription($yamlFields);
        }
        foreach ($yamlFields as $rootField) {
            $rootFieldType = $this->resolveType($rootField, $input->table, $input);
            $this->assertNoLinebreakOutsideOfPalette($rootFieldType, $input->contentBlock);
            $fields = match ($rootFieldType) {
                Fieldtype::PALETTE => $this->handlePalette($input, $result, $rootField),
                FieldType::TAB => $this->handleTab($input, $result, $rootField),
                default => $this->handleDefault($input, $result, $rootField)
            };
            foreach ($fields as $field) {
                $identifier = $field['identifier'];
                $this->assertUniqueFieldIdentifier($identifier, $result, $input->contentBlock);
                $result->uniqueFieldIdentifiers[] = $identifier;
                $fieldType = $this->resolveType($field, $input->table, $input);

                $uniqueIdentifier = $this->chooseIdentifier($input, $field);
                $this->prefixSortFieldIfNecessary($input, $result, $identifier, $uniqueIdentifier);
                $this->prefixLabelFieldIfNecessary($input, $result, $identifier, $uniqueIdentifier);
                $this->prefixFallbackLabelFieldsIfNecessary($input, $result, $identifier, $uniqueIdentifier);

                $input->languagePath->addPathSegment($identifier);
                $labelPath = $input->languagePath->getCurrentPath() . '.label';
                $descriptionPath = $input->languagePath->getCurrentPath() . '.description';
                $label = (string)($field['label'] ?? '');
                // Never fall back to identifiers for existing fields. They have their standard translation.
                $label = ($label !== '' || $this->isExistingField($field)) ? $label : $identifier;
                $field['label'] = $label;
                $description = $field['description'] ?? '';
                $labelPathSource = new AutomaticLanguageSource($labelPath, $label);
                $descriptionPathSource = new AutomaticLanguageSource($descriptionPath, $description);
                $this->automaticLanguageKeysRegistry->addKey($input->contentBlock, $labelPathSource);
                $this->automaticLanguageKeysRegistry->addKey($input->contentBlock, $descriptionPathSource);

                if ($fieldType === FieldType::FLEXFORM) {
                    $this->validateFlexFormHasOnlySheetsOrNoSheet($field, $input->contentBlock);
                    $this->validateFlexFormContainsValidFieldTypes($field, $input->contentBlock);
                    $field = $this->processFlexForm($field, $input);
                }

                $tcaFieldDefinition = [
                    'parentTable' => $input->contentBlock->getContentType()->getTable(),
                    'uniqueIdentifier' => $uniqueIdentifier,
                    'config' => $field,
                    'type' => $fieldType,
                    'labelPath' => $input->languagePath->getCurrentPath() . '.label',
                    'descriptionPath' => $input->languagePath->getCurrentPath() . '.description',
                ];

                if ($fieldType === FieldType::COLLECTION) {
                    $isExternalCollection = array_key_exists('foreign_table', $field);
                    $tcaFieldDefinition['config']['foreign_field'] ??= 'foreign_table_parent_uid';
                    if ($isExternalCollection) {
                        if ($field['shareAcrossTables'] ?? false) {
                            $tcaFieldDefinition['config']['foreign_table_field'] ??= 'tablenames';
                        }
                        if ($field['shareAcrossFields'] ?? false) {
                            $tcaFieldDefinition['config']['foreign_match_fields']['fieldname'] = $uniqueIdentifier;
                        }
                    } else {
                        $tcaFieldDefinition['config']['foreign_table'] = $field['table'] ?? $uniqueIdentifier;
                    }
                    $foreignTable = $tcaFieldDefinition['config']['foreign_table'];
                    $this->parentReferences[$foreignTable][] = $tcaFieldDefinition;
                    if (!empty($field['fields'])) {
                        $result->tableDefinitionList = $this->processFields(
                            new ProcessingInput(
                                yaml: $field,
                                contentBlock: $input->contentBlock,
                                table: $foreignTable,
                                rootTable: $input->rootTable,
                                languagePath: $input->languagePath,
                                contentType: ContentType::RECORD_TYPE,
                                tableDefinitionList: $result->tableDefinitionList,
                            )
                        );
                    }
                }

                $result->tableDefinition->fields[$uniqueIdentifier] = $tcaFieldDefinition;
                $result->contentType->columns[] = $uniqueIdentifier;
                if ($uniqueIdentifier !== $result->tableDefinition->typeField) {
                    $result->contentType->overrideColumns[] = TcaFieldDefinition::createFromArray($tcaFieldDefinition);
                }

                $input->languagePath->popSegment();
            }
        }

        // Collect table definitions and content types and carry it over to the next stack.
        // This will be merged at the very end.
        $result->tableDefinitionList[$input->table]['tableDefinitions'][] = $result->tableDefinition->toArray();
        $typeDefinition = $result->contentType->toArray($input->isRootTable(), $input->yaml['identifier'] ?? '');
        $result->tableDefinitionList[$input->table]['typeDefinitions'][] = $typeDefinition;
        return $result->tableDefinitionList;
    }

    private function prependTypeFieldForRecordType(array $yamlFields, ProcessedFieldsResult $result): array
    {
        array_unshift($yamlFields, [
            'identifier' => $result->tableDefinition->typeField,
            'type' => FieldType::SELECT->value,
            'renderType' => 'selectSingle',
            'prefixField' => false,
            'default' => $result->contentType->typeName,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.type',
            'items' => [],
        ]);
        return $yamlFields;
    }

    private function appendInternalDescription(array $yamlFields): array
    {
        $tab = [
            'identifier' => 'internal_description_tab',
            'type' => 'Tab',
            'label' => 'LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes',
        ];
        $internalDescription = [
            'identifier' => 'internal_description',
            'type' => 'Textarea',
            'prefixField' => false,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.description',
            'rows' => 5,
            'cols' => 30,
        ];
        $yamlFields[] = $tab;
        $yamlFields[] = $internalDescription;
        return $yamlFields;
    }

    private function handleDefault(ProcessingInput $input, ProcessedFieldsResult $result, array $field): array
    {
        $result->contentType->showItems[] = $this->chooseIdentifier($input, $field);
        return [$field];
    }

    private function handlePalette(ProcessingInput $input, ProcessedFieldsResult $result, array $rootPalette): array
    {
        $rootPaletteIdentifier = $rootPalette['identifier'];
        $this->assertUniquePaletteIdentifier($rootPaletteIdentifier, $result, $input->contentBlock);
        $result->uniquePaletteIdentifiers[] = $rootPaletteIdentifier;
        // Ignore empty Palettes.
        if (($rootPalette['fields'] ?? []) === []) {
            return [];
        }
        $fields = [];
        $paletteItems = [];
        foreach ($rootPalette['fields'] as $paletteField) {
            $paletteFieldType = $this->resolveType($paletteField, $input->table, $input);
            if ($paletteFieldType === FieldType::LINEBREAK) {
                $paletteItems[] = new LinebreakDefinition();
            } else {
                $this->assertNoPaletteInPalette($paletteFieldType, $paletteField['identifier'], $rootPaletteIdentifier, $input->contentBlock);
                $this->assertNoTabInPalette($paletteFieldType, $paletteField['identifier'], $rootPaletteIdentifier, $input->contentBlock);
                $fields[] = $paletteField;
                $paletteItems[] = $this->chooseIdentifier($input, $paletteField);
            }
        }

        $input->languagePath->addPathSegment('palettes.' . $rootPaletteIdentifier);
        $label = (string)($rootPalette['label'] ?? '');
        $description = $rootPalette['description'] ?? '';
        $languagePathLabel = $input->languagePath->getCurrentPath() . '.label';
        $languagePathDescription = $input->languagePath->getCurrentPath() . '.description';
        $labelPathSource = new AutomaticLanguageSource($languagePathLabel, $label);
        $descriptionPathSource = new AutomaticLanguageSource($languagePathDescription, $description);
        $this->automaticLanguageKeysRegistry->addKey($input->contentBlock, $labelPathSource);
        $this->automaticLanguageKeysRegistry->addKey($input->contentBlock, $descriptionPathSource);
        $input->languagePath->popSegment();

        $paletteIdentifier = $this->chooseIdentifier($input, $rootPalette);
        $palette = [
            'contentBlockName' => $input->contentBlock->getName(),
            'identifier' => $paletteIdentifier,
            'label' => $label,
            'description' => $description,
            'languagePathLabel' => $languagePathLabel,
            'languagePathDescription' => $languagePathDescription,
            'items' => $paletteItems,
        ];
        $result->tableDefinition->palettes[$paletteIdentifier] = $palette;
        $result->contentType->showItems[] = PaletteDefinition::createFromArray($palette);
        return $fields;
    }

    private function handleTab(ProcessingInput $input, ProcessedFieldsResult $result, array $field): array
    {
        $identifier = $field['identifier'];
        $this->assertUniqueTabIdentifier($identifier, $result, $input->contentBlock);
        $result->uniqueTabIdentifiers[] = $identifier;

        $input->languagePath->addPathSegment('tabs.' . $identifier);
        $label = (string)($field['label'] ?? '');
        $label = $label !== '' ? $label : $identifier;
        $languagePathLabel = $input->languagePath->getCurrentPath();
        $languagePathSource = new AutomaticLanguageSource($languagePathLabel, $label);
        $this->automaticLanguageKeysRegistry->addKey($input->contentBlock, $languagePathSource);
        $input->languagePath->popSegment();

        $tabDefinitionArray = [
            'identifier' => $identifier,
            'contentBlockName' => $input->contentBlock->getName(),
            'label' => $label,
            'languagePathLabel' => $languagePathLabel,
        ];
        $tabDefinition = TabDefinition::createFromArray($tabDefinitionArray);
        $result->contentType->showItems[] = $tabDefinition;
        return [];
    }

    private function processFlexForm(array $field, ProcessingInput $input): array
    {
        $flexFormDefinition = new FlexFormDefinition();
        $flexFormTypeName = $input->getTypeField() !== null ? $input->getTypeName() : 'default';
        $flexFormDefinition->setTypeName($flexFormTypeName);
        $flexFormDefinition->setContentBlockName($input->contentBlock->getName());
        $sheetDefinition = new SheetDefinition();
        $fields = $field['fields'] ?? [];
        if ($this->flexFormDefinitionHasDefaultSheet($fields)) {
            foreach ($fields as $flexFormField) {
                $sheetDefinition->addFieldOrSection($this->resolveFlexFormField($input, $flexFormField));
            }
            $flexFormDefinition->addSheet($sheetDefinition);
        } else {
            foreach ($fields as $sheet) {
                $sheetDefinition = new SheetDefinition();
                $identifier = $sheet['identifier'];
                $sheetDefinition->setIdentifier($identifier);

                $input->languagePath->addPathSegment('sheets.' . $sheetDefinition->getIdentifier());
                $languagePathLabel = $input->languagePath->getCurrentPath() . '.label';
                $descriptionPathLabel = $input->languagePath->getCurrentPath() . '.description';
                $linkTitlePathLabel = $input->languagePath->getCurrentPath() . '.linkTitle';
                $sheetDefinition->setLanguagePathLabel($languagePathLabel);
                $sheetDefinition->setLanguagePathDescription($descriptionPathLabel);
                $sheetDefinition->setLanguagePathLinkTitle($linkTitlePathLabel);
                $label = (string)($sheet['label'] ?? '');
                $label = $label !== '' ? $label : $identifier;
                $description = $sheet['description'] ?? '';
                $linkTitle = $sheet['linkTitle'] ?? '';
                $sheetDefinition->setLabel($label);
                $sheetDefinition->setDescription($description);
                $sheetDefinition->setLinkTitle($linkTitle);
                $languagePathSource = new AutomaticLanguageSource($languagePathLabel, $label);
                $descriptionPathSource = new AutomaticLanguageSource($descriptionPathLabel, $description);
                $linkTitlePathSource = new AutomaticLanguageSource($linkTitlePathLabel, $linkTitle);
                $this->automaticLanguageKeysRegistry->addKey($input->contentBlock, $languagePathSource);
                $this->automaticLanguageKeysRegistry->addKey($input->contentBlock, $descriptionPathSource);
                $this->automaticLanguageKeysRegistry->addKey($input->contentBlock, $linkTitlePathSource);
                $input->languagePath->popSegment();

                foreach ($sheet['fields'] ?? [] as $sheetField) {
                    $sheetDefinition->addFieldOrSection($this->resolveFlexFormField($input, $sheetField));
                }
                $flexFormDefinition->addSheet($sheetDefinition);
            }
        }

        if ($input->getTypeField() !== null) {
            $field['ds_pointerField'] = $input->getTypeField();
        }
        $field['flexFormDefinitions'][$flexFormDefinition->getTypeName()] = $flexFormDefinition;
        return $field;
    }

    private function flexFormDefinitionHasDefaultSheet(array $fields): bool
    {
        foreach ($fields as $flexFormField) {
            return FlexFormSubType::tryFrom($flexFormField['type']) !== FlexFormSubType::SHEET;
        }
        return true;
    }

    private function resolveFlexFormField(ProcessingInput $input, array $flexFormField): TcaFieldDefinition|SectionDefinition
    {
        if (FlexFormSubType::tryFrom($flexFormField['type']) === FlexFormSubType::SECTION) {
            return $this->processFlexFormSection($input, $flexFormField);
        }
        $identifier = $flexFormField['identifier'];

        $input->languagePath->addPathSegment($identifier);
        $labelPath = $input->languagePath->getCurrentPath() . '.label';
        $descriptionPath = $input->languagePath->getCurrentPath() . '.description';
        $label = (string)($flexFormField['label'] ?? '');
        $label = $label !== '' ? $label : $identifier;
        $flexFormField['label'] = $label;
        $description = $flexFormField['description'] ?? '';
        $languagePathSource = new AutomaticLanguageSource($labelPath, $label);
        $descriptionPathSource = new AutomaticLanguageSource($descriptionPath, $description);
        $this->automaticLanguageKeysRegistry->addKey($input->contentBlock, $languagePathSource);
        $this->automaticLanguageKeysRegistry->addKey($input->contentBlock, $descriptionPathSource);

        $flexFormFieldArray = [
            'uniqueIdentifier' => $identifier,
            'config' => $flexFormField,
            'type' => FieldType::from($flexFormField['type']),
            'labelPath' => $labelPath,
            'descriptionPath' => $descriptionPath,
        ];
        $input->languagePath->popSegment();
        $flexFormTcaDefinition = TcaFieldDefinition::createFromArray($flexFormFieldArray);
        return $flexFormTcaDefinition;
    }

    private function processFlexFormSection(ProcessingInput $input, array $section): SectionDefinition
    {
        $sectionDefinition = new SectionDefinition();
        $sectionIdentifier = $section['identifier'];
        $sectionDefinition->setIdentifier($sectionIdentifier);

        $input->languagePath->addPathSegment('sections.' . $sectionDefinition->getIdentifier());
        $sectionTitle = $input->languagePath->getCurrentPath() . '.title';
        $label = (string)($section['label'] ?? '');
        $label = $label !== '' ? $label : $sectionIdentifier;
        $sectionDefinition->setLanguagePathLabel($sectionTitle);
        $sectionDefinition->setLabel($label);
        $labelPathSource = new AutomaticLanguageSource($sectionTitle, $label);
        $this->automaticLanguageKeysRegistry->addKey($input->contentBlock, $labelPathSource);

        foreach ($section['container'] as $container) {
            $containerIdentifier = $container['identifier'];
            $containerDefinition = new ContainerDefinition();
            $containerDefinition->setIdentifier($containerIdentifier);

            $input->languagePath->addPathSegment('container.' . $containerDefinition->getIdentifier());
            $containerTitle = $input->languagePath->getCurrentPath() . '.title';
            $label = (string)($container['label'] ?? '');
            $label = $label !== '' ? $label : $containerIdentifier;
            $containerDefinition->setLanguagePathLabel($containerTitle);
            $containerDefinition->setLabel($label);
            $labelPathSource = new AutomaticLanguageSource($containerTitle, $label);
            $this->automaticLanguageKeysRegistry->addKey($input->contentBlock, $labelPathSource);

            foreach ($container['fields'] as $containerField) {
                $containerDefinition->addField($this->resolveFlexFormField($input, $containerField));
            }
            $sectionDefinition->addContainer($containerDefinition);
            $input->languagePath->popSegment();
        }
        $input->languagePath->popSegment();
        return $sectionDefinition;
    }

    public function prefixSortFieldIfNecessary(
        ProcessingInput $input,
        ProcessedFieldsResult $result,
        string $identifier,
        string $uniqueIdentifier,
    ): void {
        $sortField = $input->yaml['sortField'] ?? null;
        if ($sortField === null) {
            return;
        }
        $sortFieldArray = [];
        if (is_string($sortField)) {
            $sortFieldArray = [['identifier' => $sortField]];
            if ($sortField !== $identifier) {
                return;
            }
            $result->tableDefinition->raw['sortField'] = [];
        }
        if (is_array($sortField)) {
            $sortFieldArray = $sortField;
        }
        if ($sortFieldArray === []) {
            return;
        }
        for ($i = 0; $i < count($sortFieldArray); $i++) {
            $sortFieldItem = $sortFieldArray[$i];
            $sortFieldIdentifier = $sortFieldItem['identifier'];
            $order = '';
            if (array_key_exists('order', $sortFieldItem)) {
                $order = strtolower((string)$sortFieldItem['order']);
                if (!in_array($order, ['asc', 'desc'], true)) {
                    throw new \InvalidArgumentException('order for sortField.order must be one of "asc" or "desc". "' . $order . '" provided.', 1694014891);
                }
            }
            if ($sortFieldIdentifier !== '' && $sortFieldIdentifier === $identifier) {
                $result->tableDefinition->raw['sortField'][$i]['identifier'] = $uniqueIdentifier;
                $result->tableDefinition->raw['sortField'][$i]['order'] = strtoupper($order);
            }
        }
    }

    private function prefixLabelFieldIfNecessary(
        ProcessingInput $input,
        ProcessedFieldsResult $result,
        string $identifier,
        string $uniqueIdentifier,
    ): void {
        $labelCapability = LabelCapability::createFromArray($input->yaml);
        if (!$labelCapability->hasLabelField()) {
            return;
        }
        $labelFields = $labelCapability->getLabelFieldsAsArray();
        for ($i = 0; $i < count($labelFields); $i++) {
            $currentLabelField = $labelFields[$i];
            if ($currentLabelField === $identifier) {
                if (is_string($result->tableDefinition->raw['labelField'])) {
                    $result->tableDefinition->raw['labelField'] = [];
                }
                $result->tableDefinition->raw['labelField'][$i] = $uniqueIdentifier;
            }
        }
    }

    private function prefixFallbackLabelFieldsIfNecessary(
        ProcessingInput $input,
        ProcessedFieldsResult $result,
        mixed $identifier,
        string $uniqueIdentifier
    ): void {
        $labelCapability = LabelCapability::createFromArray($input->yaml);
        if (!$labelCapability->hasFallbackLabelFields()) {
            return;
        }
        $fallbackLabelFields = $labelCapability->getFallbackLabelFields();
        for ($i = 0; $i < count($fallbackLabelFields); $i++) {
            $currentLabelField = $fallbackLabelFields[$i];
            if ($currentLabelField === $identifier) {
                $result->tableDefinition->raw['fallbackLabelFields'][$i] = $uniqueIdentifier;
            }
        }
    }

    private function isPrefixEnabledForField(LoadedContentBlock $contentBlock, array $fieldConfiguration): bool
    {
        if ($this->isExistingField($fieldConfiguration)) {
            return false;
        }
        if (array_key_exists('prefixField', $fieldConfiguration)) {
            return (bool)$fieldConfiguration['prefixField'];
        }
        return $contentBlock->prefixFields();
    }

    private function isExistingField(array $fieldConfiguration): bool
    {
        return (bool)($fieldConfiguration['useExistingField'] ?? false);
    }

    private function getPrefixType(LoadedContentBlock $contentBlock, array $fieldConfiguration): PrefixType
    {
        if (array_key_exists('prefixType', $fieldConfiguration)) {
            return PrefixType::from($fieldConfiguration['prefixType']);
        }
        return $contentBlock->getPrefixType();
    }

    private function resolveType(array $field, string $table, ProcessingInput $input): FieldType
    {
        $isExistingField = $this->isExistingField($field);
        if ($isExistingField) {
            $this->assertIdentifierExists($field, $input);
            $identifier = $field['identifier'];
            // Check if the field is defined as a "base" TCA field (NOT defined in TCA/Overrides).
            if (($GLOBALS['TCA'][$table]['columns'][$identifier] ?? []) !== []) {
                $fieldType = TypeResolver::resolve($field['identifier'], $table);
                return $fieldType;
            }
        }
        $this->assertTypeExists($field, $input);
        $fieldType = FieldType::tryFrom($field['type']);
        if ($fieldType === null) {
            $validTypesList = array_map(fn(FieldType $fieldType) => $fieldType->value, FieldType::cases());
            $validTypes = implode(', ', $validTypesList);
            throw new \InvalidArgumentException(
                'The type "' . $field['type'] . '" is not a valid type in Content Block "' . $input->contentBlock->getName() . '". Valid types are: ' . $validTypes . '.',
                1697625849
            );
        }
        if ($fieldType !== FieldType::LINEBREAK) {
            $this->assertIdentifierExists($field, $input);
        }
        return $fieldType;
    }

    private function chooseIdentifier(ProcessingInput $input, array $field): string
    {
        if (!$input->isRootTable()) {
            return $field['identifier'];
        }
        $prefixEnabled = $this->isPrefixEnabledForField($input->contentBlock, $field);
        if (!$prefixEnabled) {
            return $field['identifier'];
        }
        $prefixType = $this->getPrefixType($input->contentBlock, $field);
        $uniqueIdentifier = UniqueIdentifierCreator::prefixIdentifier($input->contentBlock, $prefixType, $field['identifier']);
        return $uniqueIdentifier;
    }

    private function assertNoLinebreakOutsideOfPalette(FieldType $fieldType, LoadedContentBlock $contentBlock): void
    {
        if ($fieldType === FieldType::LINEBREAK) {
            throw new \InvalidArgumentException(
                'Linebreaks are only allowed within Palettes in content block "' . $contentBlock->getName() . '".',
                1679224392
            );
        }
    }

    private function assertIdentifierExists(array $field, ProcessingInput $input): void
    {
        if (!isset($field['identifier'])) {
            throw new \InvalidArgumentException(
                'A field is missing the required "identifier" in content block "' . $input->contentBlock->getName() . '".',
                1679226075
            );
        }
    }

    private function assertTypeExists(array $field, ProcessingInput $input): void
    {
        if (!isset($field['type'])) {
            throw new \InvalidArgumentException(
                'The field "' . ($field['identifier'] ?? '') . '" is missing the required "type" in content block "' . $input->contentBlock->getName() . '".',
                1694768937
            );
        }
    }

    private function assertUniquePaletteIdentifier(string $identifier, ProcessedFieldsResult $result, LoadedContentBlock $contentBlock): void
    {
        if (in_array($identifier, $result->uniquePaletteIdentifiers, true)) {
            throw new \InvalidArgumentException(
                'The palette identifier "' . $identifier . '" in content block "' . $contentBlock->getName() . '" does exist more than once. Please choose unique identifiers.',
                1679168022
            );
        }
    }

    private function assertNoPaletteInPalette(FieldType $fieldType, string $identifier, string $rootFieldIdentifier, LoadedContentBlock $contentBlock): void
    {
        if ($fieldType === FieldType::PALETTE) {
            throw new \InvalidArgumentException(
                'Palette "' . $identifier . '" is not allowed inside palette "' . $rootFieldIdentifier . '" in content block "' . $contentBlock->getName() . '".',
                1679168602
            );
        }
    }

    private function assertNoTabInPalette(FieldType $fieldType, string $identifier, string $rootFieldIdentifier, LoadedContentBlock $contentBlock): void
    {
        if ($fieldType === FieldType::TAB) {
            throw new \InvalidArgumentException(
                'Tab "' . $identifier . '" is not allowed inside palette "' . $rootFieldIdentifier . '" in content block "' . $contentBlock->getName() . '".',
                1679245193
            );
        }
    }

    private function assertUniqueTabIdentifier(string $identifier, ProcessedFieldsResult $result, LoadedContentBlock $contentBlock): void
    {
        if (in_array($identifier, $result->uniqueTabIdentifiers, true)) {
            throw new \InvalidArgumentException(
                'The tab identifier "' . $identifier . '" in content block "' . $contentBlock->getName() . '" does exist more than once. Please choose unique identifiers.',
                1679243686
            );
        }
    }

    private function assertUniqueFieldIdentifier(string $identifier, ProcessedFieldsResult $result, LoadedContentBlock $contentBlock): void
    {
        if (in_array($identifier, $result->uniqueFieldIdentifiers, true)) {
            throw new \InvalidArgumentException(
                'The identifier "' . $identifier . '" in content block "' . $contentBlock->getName() . '" does exist more than once. Please choose unique identifiers.',
                1677407942
            );
        }
    }

    private function validateFlexFormHasOnlySheetsOrNoSheet(array $field, LoadedContentBlock $contentBlock): void
    {
        foreach ($field['fields'] ?? [] as $flexField) {
            $flexFormType = FlexFormSubType::tryFrom($flexField['type']);
            if ($flexFormType !== FlexFormSubType::SHEET) {
                $flexFormType = 'nonSheet';
            }
            $currentType ??= $flexFormType;
            $isValid = $currentType === $flexFormType;
            if (!$isValid) {
                throw new \InvalidArgumentException(
                    'You must not mix Sheets with normal fields inside the FlexForm definition "' . $field['identifier'] . '" in content block "' . $contentBlock->getName() . '".',
                    1685217163
                );
            }
            $currentType = $flexFormType;
        }
    }

    private function validateFlexFormContainsValidFieldTypes(array $field, LoadedContentBlock $contentBlock): void
    {
        foreach ($field['fields'] ?? [] as $flexField) {
            if (FlexFormSubType::tryFrom($flexField['type']) === FlexFormSubType::SHEET) {
                $this->validateFlexFormContainsValidFieldTypes($flexField, $contentBlock);
                continue;
            }
            if (FlexFormSubType::tryFrom($flexField['type']) === FlexFormSubType::SECTION) {
                if (empty($flexField['container'])) {
                    throw new \InvalidArgumentException(
                        'FlexForm field "' . $field['identifier'] . '" has a Section "' . $flexField['identifier'] . '" without "container" defined. This is invalid, please add at least one item to "container" in Content Block "' . $contentBlock->getName() . '".',
                        1686330220
                    );
                }
                foreach ($flexField['container'] as $container) {
                    if (empty($container['fields'])) {
                        throw new \InvalidArgumentException(
                            'FlexForm field "' . $field['identifier'] . '" has a Container in Section "' . $flexField['identifier'] . '" without "fields" defined. This is invalid, please add at least one field to "fields" in Content Block "' . $contentBlock->getName() . '".',
                            1686331469
                        );
                    }
                    foreach ($container['fields'] as $containerField) {
                        $containerType = FieldType::from($containerField['type']);
                        if (!FieldType::isValidFlexFormField($containerType)) {
                            throw new \InvalidArgumentException(
                                'FlexForm field "' . $field['identifier'] . '" has an invalid field of type "' . $containerType->value . '" inside of a "container" item. Please use valid field types in Content Block "' . $contentBlock->getName() . '".',
                                1686330594
                            );
                        }
                    }
                }
                continue;
            }
            $type = FieldType::from($flexField['type']);
            if (!FieldType::isValidFlexFormField($type)) {
                throw new \InvalidArgumentException(
                    'Field type "' . $type->value . '" with identifier "' . $flexField['identifier'] . '" is not allowed inside FlexForm in Content Block "' . $contentBlock->getName() . '".',
                    1685220309
                );
            }
        }
    }
}
