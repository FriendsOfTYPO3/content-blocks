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

use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentTypeDefinition;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentTypeIcon;
use TYPO3\CMS\ContentBlocks\Definition\Factory\Struct\ProcessedContentType;
use TYPO3\CMS\ContentBlocks\Definition\Factory\Struct\ProcessedFieldsResult;
use TYPO3\CMS\ContentBlocks\Definition\Factory\Struct\ProcessedTableDefinition;
use TYPO3\CMS\ContentBlocks\Definition\Factory\Struct\ProcessingInput;
use TYPO3\CMS\ContentBlocks\Definition\LabelCapability;
use TYPO3\CMS\ContentBlocks\Definition\LanguagePath;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinition;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\ContentBlocks\Definition\TcaFieldDefinition;
use TYPO3\CMS\ContentBlocks\Definition\TypeResolver;
use TYPO3\CMS\ContentBlocks\FieldConfiguration\FieldType;
use TYPO3\CMS\ContentBlocks\Loader\LoadedContentBlock;
use TYPO3\CMS\ContentBlocks\Service\ContentTypeIconResolver;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\ContentBlocks\Utility\UniqueNameUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @internal Not part of TYPO3's public API.
 */
class TableDefinitionCollectionFactory
{
    /**
     * @param LoadedContentBlock[] $contentBlocks
     */
    public function createFromLoadedContentBlocks(array $contentBlocks): TableDefinitionCollection
    {
        $tableDefinitionCollection = new TableDefinitionCollection();
        $tableDefinitionList = [];
        foreach ($contentBlocks as $contentBlock) {
            $table = $contentBlock->getYaml()['table'];
            $languagePath = new LanguagePath('LLL:' . $contentBlock->getPath() . '/' . ContentBlockPathUtility::getLanguageFilePath());
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
            $tableDefinitionCollection->addTable(TableDefinition::createFromTableArray($table, $tableDefinition));
        }
        return $tableDefinitionCollection;
    }

    private function mergeProcessingResult(array $tableDefinitionList): array
    {
        $mergedResult = [];
        foreach ($tableDefinitionList as $table => $definition) {
            $mergedResult[$table] = array_replace_recursive(...array_reverse($definition['tableDefinitions']));
            $mergedResult[$table]['elements'] = $definition['elements'];
        }
        return $mergedResult;
    }

    private function processFields(ProcessingInput $input): array
    {
        $result = $this->initializeResult($input);
        $yamlFields = $input->yaml['fields'];

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
                $this->assertUniqueFieldIdentifier($field['identifier'], $result, $input->contentBlock);
                $result->uniqueFieldIdentifiers[] = $field['identifier'];
                $fieldType = $this->resolveType($field, $input->table, $input);
                $input->languagePath->addPathSegment($field['identifier']);

                if ($fieldType === FieldType::FLEXFORM) {
                    $this->validateFlexFormHasOnlySheetsOrNoSheet($field, $input->contentBlock);
                    $this->validateFlexFormContainsValidFieldTypes($field, $input->contentBlock);
                    $field = $this->processFlexForm($field, $input->getTypeField(), $input->getTypeName(), $input->languagePath);
                }

                if ($fieldType === FieldType::COLLECTION) {
                    $inlineTable = $this->chooseIdentifier($input, $field);
                    $field['foreign_table'] ??= $inlineTable;
                    $field['foreign_match_fields'] = [
                        'fieldname' => $inlineTable,
                    ];
                    if (!empty($field['fields'])) {
                        $result->tableDefinitionList = $this->processFields(
                            new ProcessingInput(
                                yaml: $field,
                                contentBlock: $input->contentBlock,
                                table: $inlineTable,
                                rootTable: $input->rootTable,
                                languagePath: $input->languagePath,
                                contentType: ContentType::RECORD_TYPE,
                                tableDefinitionList: $result->tableDefinitionList
                            )
                        );
                    }
                }

                $field['languagePath'] = clone $input->languagePath;
                $uniqueIdentifier = $this->chooseIdentifier($input, $field);
                $this->prefixSortFieldIfNecessary($input, $result, $field['identifier'], $uniqueIdentifier);
                $this->prefixLabelFieldIfNecessary($input, $result, $field['identifier'], $uniqueIdentifier);
                $this->prefixFallbackLabelFieldsIfNecessary($input, $result, $field['identifier'], $uniqueIdentifier);
                $fieldArray = [
                    'uniqueIdentifier' => $uniqueIdentifier,
                    'config' => $field,
                    'type' => $fieldType,
                ];
                $result->tableDefinition->fields[$uniqueIdentifier] = $fieldArray;
                $result->contentType->columns[] = $uniqueIdentifier;
                if ($uniqueIdentifier !== $result->tableDefinition->typeField) {
                    $result->contentType->overrideColumns[] = TcaFieldDefinition::createFromArray($fieldArray);
                }

                $input->languagePath->popSegment();
            }
        }

        // Collect table definitions and content types and carry it over to the next stack.
        // This will be merged at the very end.
        $result->tableDefinitionList[$input->table]['tableDefinitions'][] = $this->createInputArrayForTableDefinition($result->tableDefinition);
        $result->tableDefinitionList[$input->table]['elements'][] = $this->createInputArrayForTypeDefinition($result->contentType, $input);
        return $result->tableDefinitionList;
    }

    private function initializeResult(ProcessingInput $input): ProcessedFieldsResult
    {
        $result = new ProcessedFieldsResult();
        $result->tableDefinitionList = $input->tableDefinitionList;

        $result->contentType->contentBlock = $input->contentBlock;
        $result->contentType->typeName = $input->getTypeName();
        $result->contentType->table = $input->table;

        $result->tableDefinition->typeField = $input->getTypeField();
        $result->tableDefinition->isRootTable = $input->isRootTable();
        $result->tableDefinition->isAggregateRoot = $input->yaml['aggregateRoot'] ?? null;
        $result->tableDefinition->raw = $input->yaml;
        $result->tableDefinition->contentType = $input->contentType;
        return $result;
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
        $this->assertUniquePaletteIdentifier($rootPalette['identifier'], $result, $input->contentBlock);
        $result->uniquePaletteIdentifiers[] = $rootPalette['identifier'];
        // Ignore empty Palettes.
        if (($rootPalette['fields'] ?? []) === []) {
            return [];
        }
        $fields = [];
        $paletteShowItems = [];
        foreach ($rootPalette['fields'] as $paletteField) {
            $paletteFieldType = $this->resolveType($paletteField, $input->table, $input);
            if ($paletteFieldType === FieldType::LINEBREAK) {
                $paletteShowItems[] = '--linebreak--';
            } else {
                $this->assertNoPaletteInPalette($paletteFieldType, $paletteField['identifier'], $rootPalette['identifier'], $input->contentBlock);
                $this->assertNoTabInPalette($paletteFieldType, $paletteField['identifier'], $rootPalette['identifier'], $input->contentBlock);
                $fields[] = $paletteField;
                $paletteShowItems[] = $this->chooseIdentifier($input, $paletteField);
            }
        }
        $input->languagePath->addPathSegment('palettes.' . $rootPalette['identifier']);
        $label = $rootPalette['label'] ?? '';
        $description = $rootPalette['description'] ?? '';
        $palette = [
            'label' => $label !== '' ? $label : $input->languagePath->getCurrentPath() . '.label',
            'description' => $description !== '' ? $description : $input->languagePath->getCurrentPath() . '.description',
            'showitem' => $paletteShowItems,
        ];
        $paletteIdentifier = $this->chooseIdentifier($input, $rootPalette);
        $result->tableDefinition->palettes[$paletteIdentifier] = $palette;
        $result->contentType->showItems[] = '--palette--;;' . $paletteIdentifier;
        $input->languagePath->popSegment();
        return $fields;
    }

    private function handleTab(ProcessingInput $input, ProcessedFieldsResult $result, array $field): array
    {
        $this->assertUniqueTabIdentifier($field['identifier'], $result, $input->contentBlock);
        $result->uniqueTabIdentifiers[] = $field['identifier'];
        $input->languagePath->addPathSegment('tabs.' . $field['identifier']);
        $label = ($field['label'] ?? '') !== '' ? $field['label'] : $input->languagePath->getCurrentPath();
        $result->contentType->showItems[] = '--div--;' . $label;
        $input->languagePath->popSegment();
        return [];
    }

    private function processFlexForm(array $field, string $typeField, string $typeName, LanguagePath $languagePath): array
    {
        $flexFormSheets = [];
        $sheetKey = 'sDEF';
        foreach ($field['fields'] ?? [] as $flexFormField) {
            if (FlexFormSubType::tryFrom($flexFormField['type']) === FlexFormSubType::SHEET) {
                $sheetKey = $flexFormField['identifier'];
                foreach ($flexFormField['fields'] ?? [] as $sheetField) {
                    $flexFormSheets[$sheetKey][$sheetField['identifier']] = $this->resolveFlexFormField($languagePath, $sheetField);
                }
                continue;
            }
            $flexFormSheets[$sheetKey][$flexFormField['identifier']] = $this->resolveFlexFormField($languagePath, $flexFormField);
        }
        $sheets = [];
        foreach ($flexFormSheets as $sheetIdentifier => $sheet) {
            $root = [
                'type' => 'array',
                'el' => $sheet,
            ];
            if (count($flexFormSheets) > 1) {
                $languagePath->addPathSegment('sheets.' . $sheetIdentifier);
                $root['sheetTitle'] = $languagePath->getCurrentPath() . '.label';
                $root['sheetDescription'] = $languagePath->getCurrentPath() . '.description';
                $root['sheetShortDescr'] = $languagePath->getCurrentPath() . '.linkTitle';
                $languagePath->popSegment();
            }
            $sheets[$sheetIdentifier] = [
                'ROOT' => $root,
            ];
        }
        $dataStructure['sheets'] = $sheets;
        $field['ds_pointerField'] = $typeField;
        $field['ds'][$typeName] = GeneralUtility::array2xml($dataStructure, '', 0, 'T3FlexForms', 4);
        return $field;
    }

    private function resolveFlexFormField(LanguagePath $languagePath, array $flexFormField): array
    {
        $languagePath->addPathSegment($flexFormField['identifier']);
        $flexFormField['languagePath'] = clone $languagePath;
        $languagePath->popSegment();

        if (FlexFormSubType::tryFrom($flexFormField['type']) === FlexFormSubType::SECTION) {
            return $this->processFlexFormSection($flexFormField, $languagePath);
        }

        $flexFormFieldArray = [
            'uniqueIdentifier' => $flexFormField['identifier'],
            'config' => $flexFormField,
            'type' => FieldType::from($flexFormField['type']),
        ];
        $flexFormTcaDefinition = TcaFieldDefinition::createFromArray($flexFormFieldArray);
        $flexFormTca = $flexFormTcaDefinition->getTca();
        // FlexForm child fields can't be excluded.
        unset($flexFormTca['exclude']);
        $flexFormTca['label'] = $flexFormTcaDefinition->getLanguagePath()->getCurrentPath() . '.label';
        $flexFormTca['description'] = $flexFormTcaDefinition->getLanguagePath()->getCurrentPath() . '.description';
        return $flexFormTca;
    }

    private function processFlexFormSection(array $section, LanguagePath $languagePath): array
    {
        $languagePath->addPathSegment('sections.' . $section['identifier']);
        $result = [
            'title' => $languagePath->getCurrentPath() . '.title',
            'type' => 'array',
            'section' => 1,
        ];
        $processedContainers = [];
        foreach ($section['container'] as $container) {
            $languagePath->addPathSegment('container.' . $container['identifier']);
            $containerResult = [
                'title' => $languagePath->getCurrentPath() . '.title',
                'type' => 'array',
            ];
            $processedContainerFields = [];
            foreach ($container['fields'] as $containerField) {
                $processedContainerFields[$containerField['identifier']] = $this->resolveFlexFormField($languagePath, $containerField);
            }
            $containerResult['el'] = $processedContainerFields;
            $processedContainers[$container['identifier']] = $containerResult;
            $languagePath->popSegment();
        }
        $result['el'] = $processedContainers;
        $languagePath->popSegment();
        return $result;
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
        if (array_key_exists('useExistingField', $fieldConfiguration)) {
            return !$fieldConfiguration['useExistingField'];
        }
        if (array_key_exists('prefixField', $fieldConfiguration)) {
            return (bool)$fieldConfiguration['prefixField'];
        }
        return $contentBlock->prefixFields();
    }

    private function resolveType(array $field, string $table, ProcessingInput $input): FieldType
    {
        $isExistingField = ($field['useExistingField'] ?? false);
        if ($isExistingField) {
            $this->assertIdentifierExists($field, $input);
            $fieldType = TypeResolver::resolve($field['identifier'], $table);
            return $fieldType;
        }
        $this->assertTypeExists($field, $input);
        $fieldType = FieldType::tryFrom($field['type']);
        if ($fieldType === null) {
            $validTypes = implode(', ', array_map(fn(FieldType $fieldType) => $fieldType->value, FieldType::cases()));
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
        return $input->isRootTable() && $this->isPrefixEnabledForField($input->contentBlock, $field)
            ? UniqueNameUtility::createUniqueColumnNameFromContentBlockName($input->contentBlock->getName(), $field['identifier'])
            : $field['identifier'];
    }

    /**
     * @see TableDefinition
     */
    private function createInputArrayForTableDefinition(ProcessedTableDefinition $processedTableDefinition): array
    {
        $tableDefinition['palettes'] = $processedTableDefinition->palettes;
        $tableDefinition['fields'] = $processedTableDefinition->fields;
        $tableDefinition['typeField'] = $processedTableDefinition->typeField;
        $tableDefinition['isRootTable'] = $processedTableDefinition->isRootTable;
        $tableDefinition['raw'] = $processedTableDefinition->raw;
        $tableDefinition['contentType'] = $processedTableDefinition->contentType;
        if ($processedTableDefinition->isRootTable) {
            if ($processedTableDefinition->isAggregateRoot !== null) {
                $tableDefinition['aggregateRoot'] = $processedTableDefinition->isAggregateRoot;
            }
        } else {
            $tableDefinition['aggregateRoot'] = false;
        }
        return $tableDefinition;
    }

    /**
     * @see ContentTypeDefinition
     */
    private function createInputArrayForTypeDefinition(ProcessedContentType $contentType, ProcessingInput $input): array
    {
        [$vendor, $package] = explode('/', $contentType->contentBlock->getName());
        $element = [
            'identifier' => $contentType->contentBlock->getName(),
            'columns' => $contentType->columns,
            'showItems' => $contentType->showItems,
            'overrideColumns' => $contentType->overrideColumns,
            'vendor' => $vendor,
            'package' => $package,
            'typeName' => $contentType->typeName,
        ];
        if ($input->isRootTable()) {
            $contentTypeIcon = new ContentTypeIcon();
            $contentTypeIcon->iconPath = $contentType->contentBlock->getIcon();
            $contentTypeIcon->iconProvider = $contentType->contentBlock->getIconProvider();
            $element['priority'] = (int)($contentType->contentBlock->getYaml()['priority'] ?? 0);
        } else {
            $absolutePath = GeneralUtility::getFileAbsFileName($contentType->contentBlock->getPath());
            $contentTypeIcon = ContentTypeIconResolver::resolve(
                $contentType->contentBlock->getName(),
                $absolutePath,
                $contentType->contentBlock->getPath(),
                $input->yaml['identifier']
            );
        }
        $element['typeIconPath'] = $contentTypeIcon->iconPath;
        $element['iconProvider'] = $contentTypeIcon->iconProvider;
        $element['typeIconIdentifier'] = $this->buildTypeIconIdentifier($contentType, $contentTypeIcon);
        if ($contentType->contentBlock->getContentType() === ContentType::CONTENT_ELEMENT) {
            $element['wizardGroup'] = $contentType->contentBlock->getYaml()['group'] ?? 'common';
        }
        return $element;
    }

    /**
     * We add a part of the md5 hash here in order to mitigate browser caching issues when changing the Content Block
     * Icon. Otherwise, the icon identifier would always be the same and stored in the local storage.
     */
    private function buildTypeIconIdentifier(ProcessedContentType $contentType, ContentTypeIcon $contentTypeIcon): string
    {
        $typeIconIdentifier = $contentType->table . '-' . $contentType->typeName;
        $absolutePath = GeneralUtility::getFileAbsFileName($contentTypeIcon->iconPath);
        if ($absolutePath !== '') {
            $contents = @file_get_contents($absolutePath);
            if ($contents === false) {
                throw new \RuntimeException(
                    'Unable to load resources of Content Block "' . $contentType->contentBlock->getName() . '".'
                    . ' If you have deleted this Content Block, please flush system caches and reload the page.',
                    1698430544,
                );
            }
            $hash = md5($contents);
            $hasSubString = substr($hash, 0, 7);
            $typeIconIdentifier .= '-' . $hasSubString;
        }
        return $typeIconIdentifier;
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
