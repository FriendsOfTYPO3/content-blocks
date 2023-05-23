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

namespace TYPO3\CMS\ContentBlocks\Definition;

use TYPO3\CMS\ContentBlocks\Definition\Struct\ProcessedContentType;
use TYPO3\CMS\ContentBlocks\Definition\Struct\ProcessedFieldsResult;
use TYPO3\CMS\ContentBlocks\Definition\Struct\ProcessedTableDefinition;
use TYPO3\CMS\ContentBlocks\Definition\Struct\ProcessingInput;
use TYPO3\CMS\ContentBlocks\Enumeration\FieldType;
use TYPO3\CMS\ContentBlocks\Enumeration\FlexFormSubType;
use TYPO3\CMS\ContentBlocks\Loader\ParsedContentBlock;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\ContentBlocks\Utility\UniqueNameUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @internal Not part of TYPO3's public API.
 */
final class TableDefinitionCollection implements \IteratorAggregate
{
    /** @var TableDefinition[] */
    private array $definitions = [];

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->definitions);
    }

    public function addTable(TableDefinition $tableDefinition): void
    {
        if (!$this->hasTable($tableDefinition->getTable())) {
            $this->definitions[$tableDefinition->getTable()] = $tableDefinition;
        }
    }

    public function getTable(string $table): TableDefinition
    {
        if ($this->hasTable($table)) {
            return $this->definitions[$table];
        }
        throw new \OutOfBoundsException('The table "' . $table . '" does not exist.', 1628925803);
    }

    public function hasTable(string $table): bool
    {
        return isset($this->definitions[$table]);
    }

    public function getContentElementDefinition(string $CType): ?ContentElementDefinition
    {
        if (!$this->hasTable('tt_content')) {
            return null;
        }
        foreach ($this->getTable('tt_content')->getTypeDefinitionCollection() as $typeDefinition) {
            if (!$typeDefinition instanceof ContentElementDefinition) {
                continue;
            }
            if ($typeDefinition->getTypeName() === $CType) {
                return $typeDefinition;
            }
        }
        return null;
    }

    /**
     * @param ParsedContentBlock[] $contentBlocks
     */
    public static function createFromArray(array $contentBlocks): TableDefinitionCollection
    {
        $tableDefinitionCollection = new self();
        $tableDefinitionList = [];
        foreach ($contentBlocks as $contentBlock) {
            $table = $contentBlock->getYaml()['table'] ?? 'tt_content';
            $tableDefinitionCollection->validateContentBlock($contentBlock->getYaml(), $contentBlock, $table);
            $languagePath = new LanguagePath('LLL:' . $contentBlock->getPath() . '/' . ContentBlockPathUtility::getLanguageFilePath());
            $processingInput = new ProcessingInput(
                yaml: $contentBlock->getYaml(),
                contentBlock: $contentBlock,
                table: $table,
                rootTable: $table,
                languagePath: $languagePath,
                tableDefinitionList: $tableDefinitionList,
            );
            $tableDefinitionList = $tableDefinitionCollection->processFields($processingInput);
        }
        $mergedTableDefinitionList = $tableDefinitionCollection->mergeProcessingResult($tableDefinitionList);
        foreach ($mergedTableDefinitionList as $table => $tableDefinition) {
            $tableDefinitionCollection->addTable(TableDefinition::createFromTableArray($table, $tableDefinition));
        }
        return $tableDefinitionCollection;
    }

    private function mergeProcessingResult(array $tableDefinitionList): array
    {
        $mergedResult = [];
        foreach ($tableDefinitionList as $table => $definition) {
            $mergedResult[$table] = array_replace_recursive(...$definition['tableDefinitions']);
            $mergedResult[$table]['elements'] = $definition['elements'];
        }
        return $mergedResult;
    }

    private function processFields(ProcessingInput $input): array
    {
        $result = $this->initializeResult($input);
        foreach ($input->yaml['fields'] as $rootField) {
            $rootFieldType = $this->resolveType($rootField, $input->table);
            $fields = match ($rootFieldType) {
                Fieldtype::PALETTE => $this->handlePalette($input, $result, $rootField),
                FieldType::TAB => $this->handleTab($input, $result, $rootField),
                default => $this->handleDefault($input, $result, $rootField)
            };
            foreach ($fields as $field) {
                $fieldType = $this->resolveType($field, $input->table);
                $input->languagePath->addPathSegment($field['identifier']);

                if ($fieldType === FieldType::FLEXFORM) {
                    $field = $this->processFlexForm($field, $input->getTypeField(), $input->getTypeName(), $input->languagePath);
                }

                // Recursive call for Collection (inline) fields.
                if ($fieldType === FieldType::COLLECTION) {
                    $inlineTable = $this->chooseInlineTableName($input->contentBlock, $field);
                    $field['properties']['foreign_table'] ??= $inlineTable;
                    $field['properties']['foreign_match_fields'] = [
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
                                tableDefinitionList: $result->tableDefinitionList
                            )
                        );
                    }
                }

                $field['languagePath'] = clone $input->languagePath;
                $uniqueIdentifier = $this->chooseIdentifier($input, $field);
                $fieldArray = [
                    'uniqueIdentifier' => $uniqueIdentifier,
                    'config' => $field,
                    'type' => $fieldType,
                ];
                $result->tableDefinition->fields[$uniqueIdentifier] = $fieldArray;
                $result->contentType->columns[] = $uniqueIdentifier;
                $result->contentType->overrideColumns[] = TcaFieldDefinition::createFromArray($fieldArray);

                $input->languagePath->popSegment();
            }
        }

        // Collect table definitions and content types and carry it over to the next stack.
        // This will be merged at the very end.
        $result->tableDefinitionList[$input->table]['tableDefinitions'][] = $this->createInputArrayForTableDefinition($result->tableDefinition);
        $result->tableDefinitionList[$input->table]['elements'][] = $this->createInputArrayForTypeDefinition($result->contentType);
        return $result->tableDefinitionList;
    }

    private function initializeResult(ProcessingInput $input): ProcessedFieldsResult
    {
        $result = new ProcessedFieldsResult();
        $result->tableDefinitionList = $input->tableDefinitionList;

        $result->contentType->contentBlock = $input->contentBlock;
        $result->contentType->typeName = $input->getTypeName();
        $result->contentType->table = $input->table;

        $result->tableDefinition->useAsLabel = $input->yaml['useAsLabel'] ?? '';
        $result->tableDefinition->typeField = $input->getTypeField();
        $result->tableDefinition->isRootTable = $input->isRootTable();
        $result->tableDefinition->isAggregateRoot = $input->yaml['aggregateRoot'] ?? null;
        return $result;
    }

    private function handleDefault(ProcessingInput $input, ProcessedFieldsResult $result, array $field): array
    {
        $result->contentType->showItems[] = $this->chooseIdentifier($input, $field);
        return [$field];
    }

    private function handlePalette(ProcessingInput $input, ProcessedFieldsResult $result, array $rootPalette): array
    {
        // Ignore empty Palettes.
        if (($rootPalette['fields'] ?? []) === []) {
            return [];
        }
        $fields = [];
        $paletteShowItems = [];
        foreach ($rootPalette['fields'] as $paletteField) {
            $paletteFieldType = $this->resolveType($paletteField, $input->table);
            if ($paletteFieldType === FieldType::LINEBREAK) {
                $paletteShowItems[] = '--linebreak--';
            } else {
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
        $field['properties']['ds_pointerField'] = $typeField;
        $field['properties']['ds'][$typeName] = GeneralUtility::array2xml($dataStructure, '', 0, 'T3FlexForms', 4);
        return $field;
    }

    private function resolveFlexFormField(LanguagePath $languagePath, array $flexFormField): array
    {
        $languagePath->addPathSegment($flexFormField['identifier']);
        $flexFormField['languagePath'] = clone $languagePath;
        $languagePath->popSegment();
        $flexFormFieldArray = [
            'uniqueIdentifier' => $flexFormField['identifier'],
            'config' => $flexFormField,
            'type' => FieldType::from($flexFormField['type']),
        ];
        $flexFormTcaDefinition = TcaFieldDefinition::createFromArray($flexFormFieldArray);
        $flexFormTca = $flexFormTcaDefinition->getTca();
        $flexFormTca['label'] = $flexFormTcaDefinition->getLanguagePath()->getCurrentPath() . '.label';
        $flexFormTca['description'] = $flexFormTcaDefinition->getLanguagePath()->getCurrentPath() . '.description';
        return $flexFormTca;
    }

    private function isPrefixEnabledForField(ParsedContentBlock $contentBlock, array $fieldConfiguration): bool
    {
        if (array_key_exists('useExistingField', $fieldConfiguration)) {
            return !$fieldConfiguration['useExistingField'];
        }
        if (array_key_exists('prefixField', $fieldConfiguration)) {
            return (bool)$fieldConfiguration['prefixField'];
        }
        return $contentBlock->prefixFields();
    }

    private function resolveType(array $field, string $table): FieldType
    {
        return ($field['useExistingField'] ?? false)
            ? TypeResolver::resolve($field['identifier'] ?? '', $table)
            : FieldType::from($field['type']);
    }

    private function chooseIdentifier(ProcessingInput $input, array $field): string
    {
        return $input->isRootTable() && $this->isPrefixEnabledForField($input->contentBlock, $field)
            ? UniqueNameUtility::createUniqueColumnNameFromContentBlockName($input->contentBlock->getName(), $field['identifier'])
            : $field['identifier'];
    }

    private function chooseInlineTableName(ParsedContentBlock $contentBlock, array $field): string
    {
        return $this->isPrefixEnabledForField($contentBlock, $field)
            ? UniqueNameUtility::createUniqueColumnNameFromContentBlockName($contentBlock->getName(), $field['identifier'])
            : $field['identifier'];
    }

    /**
     * @see TableDefinition
     */
    private function createInputArrayForTableDefinition(ProcessedTableDefinition $processedTableDefinition): array
    {
        $tableDefinition['palettes'] = $processedTableDefinition->palettes;
        $tableDefinition['fields'] = $processedTableDefinition->fields;
        $tableDefinition['useAsLabel'] = $processedTableDefinition->useAsLabel;
        $tableDefinition['typeField'] = $processedTableDefinition->typeField;
        $tableDefinition['isRootTable'] = $processedTableDefinition->isRootTable;
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
     * @see TypeDefinition
     */
    private function createInputArrayForTypeDefinition(ProcessedContentType $contentType): array
    {
        [$vendor, $package] = explode('/', $contentType->contentBlock->getName());
        $element = [
            'identifier' => $contentType->contentBlock->getName(),
            'columns' => $contentType->columns,
            'showItems' => $contentType->showItems,
            'overrideColumns' => $contentType->overrideColumns,
            'vendor' => $vendor,
            'package' => $package,
            'iconProvider' => $contentType->contentBlock->getIconProvider(),
            'typeName' => $contentType->typeName,
            'priority' => (int)($contentType->contentBlock->getYaml()['priority'] ?? 0),
        ];
        /** @see ContentElementDefinition */
        if ($contentType->table === 'tt_content') {
            $element['wizardGroup'] = $contentType->contentBlock->getYaml()['group'] ?? 'common';
            $element['icon'] = $contentType->contentBlock->getIcon();
        }
        return $element;
    }

    private function validateContentBlock(array $yaml, ParsedContentBlock $contentBlock, string $table): void
    {
        $uniqueIdentifiers = [];
        $uniquePaletteIdentifiers = [];
        $uniqueTabIdentifiers = [];
        foreach ($yaml['fields'] as $rootField) {
            $rootFieldType = $this->resolveType($rootField, $table);
            if ($rootFieldType === FieldType::LINEBREAK) {
                throw new \InvalidArgumentException(
                    'Linebreaks are only allowed within Palettes in content block "' . $contentBlock->getName() . '".',
                    1679224392
                );
            }
            if (!isset($rootField['identifier'])) {
                throw new \InvalidArgumentException(
                    'A field is missing the required "identifier" in content block "' . $contentBlock->getName() . '".',
                    1679226075
                );
            }
            $rootFieldIdentifier = $rootField['identifier'];
            if ($rootFieldType === FieldType::PALETTE) {
                // Ignore empty Palettes.
                if (($rootField['fields'] ?? []) === []) {
                    continue;
                }
                if (in_array($rootFieldIdentifier, $uniquePaletteIdentifiers, true)) {
                    throw new \InvalidArgumentException(
                        'The palette identifier "' . $rootFieldIdentifier . '" in content block "' . $contentBlock->getName() . '" does exist more than once. Please choose unique identifiers.',
                        1679168022
                    );
                }
                $uniquePaletteIdentifiers[] = $rootFieldIdentifier;
                $fields = [];
                foreach ($rootField['fields'] as $paletteField) {
                    $paletteFieldType = $this->resolveType($paletteField, $table);
                    if ($paletteFieldType === FieldType::PALETTE) {
                        throw new \InvalidArgumentException(
                            'Palette "' . $paletteField['identifier'] . '" is not allowed inside palette "' . $rootFieldIdentifier . '" in content block "' . $contentBlock->getName() . '".',
                            1679168602
                        );
                    }
                    if ($paletteFieldType === FieldType::TAB) {
                        throw new \InvalidArgumentException(
                            'Tab "' . $paletteField['identifier'] . '" is not allowed inside palette "' . $rootFieldIdentifier . '" in content block "' . $contentBlock->getName() . '".',
                            1679245193
                        );
                    }
                }
            } elseif ($rootFieldType === FieldType::TAB) {
                if (in_array($rootFieldIdentifier, $uniqueTabIdentifiers, true)) {
                    throw new \InvalidArgumentException(
                        'The tab identifier "' . $rootFieldIdentifier . '" in content block "' . $contentBlock->getName() . '" does exist more than once. Please choose unique identifiers.',
                        1679243686
                    );
                }
                $uniqueTabIdentifiers[] = $rootFieldIdentifier;
                continue;
            } else {
                $fields = [$rootField];
            }

            foreach ($fields as $field) {
                $identifier = $field['identifier'];
                $fieldType = $this->resolveType($field, $table);
                if (in_array($identifier, $uniqueIdentifiers, true)) {
                    throw new \InvalidArgumentException(
                        'The identifier "' . $identifier . '" in content block "' . $contentBlock->getName() . '" does exist more than once. Please choose unique identifiers.',
                        1677407942
                    );
                }
                $uniqueIdentifiers[] = $identifier;

                if ($fieldType === FieldType::FLEXFORM) {
                    // @todo validate FlexForm
                }

                // Recursive call for Collection (inline) fields.
                if ($fieldType === FieldType::COLLECTION && !empty($field['fields'])) {
                    $inlineTable = $this->chooseInlineTableName($contentBlock, $field);
                    $this->validateContentBlock($field, $contentBlock, $inlineTable);
                }
            }
        }
    }
}
