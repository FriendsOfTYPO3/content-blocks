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
            $tableDefinitionList = $tableDefinitionCollection->processContentBlock(
                yaml: $contentBlock->getYaml(),
                contentBlock: $contentBlock,
                table: $table,
                rootTable: $table,
                tableDefinitionList: $tableDefinitionList,
                languagePath: $languagePath
            );
        }
        foreach ($tableDefinitionList as $table => $tableDefinition) {
            $tableDefinitionCollection->addTable(TableDefinition::createFromTableArray($table, $tableDefinition));
        }
        return $tableDefinitionCollection;
    }

    private function processContentBlock(array $yaml, ParsedContentBlock $contentBlock, string $table, string $rootTable, array $tableDefinitionList, LanguagePath $languagePath): array
    {
        $processedFieldsResult = $this->processRootFields($yaml, $contentBlock, $table, $rootTable, $tableDefinitionList, $languagePath);

        // Merge table definition to the list, so the combined result can be added later to the definition collection.
        $tableDefinition = $this->createNewTableDefinition($processedFieldsResult->tableDefinition);
        $processedFieldsResult->tableDefinitionList[$table] ??= [];
        $processedFieldsResult->tableDefinitionList[$table]['elements'][] = $this->createNewContentType($processedFieldsResult->contentType);
        $processedFieldsResult->tableDefinitionList[$table] = array_replace_recursive($processedFieldsResult->tableDefinitionList[$table], $tableDefinition);
        return $processedFieldsResult->tableDefinitionList;
    }

    private function processRootFields(array $yaml, ParsedContentBlock $contentBlock, string $table, string $rootTable, array $tableDefinitionList, LanguagePath $languagePath): ProcessedFieldsResult
    {
        $isRootTable = $table === $rootTable;
        $typeField = $yaml['typeField'] ?? $GLOBALS['TCA'][$table]['ctrl']['type'] ?? null;
        $typeName = $typeField === null
            ? '1'
            : $yaml['typeName'] ?? UniqueNameUtility::contentBlockNameToTypeIdentifier($contentBlock->getName());
        $result = new ProcessedFieldsResult();
        $result->tableDefinitionList = $tableDefinitionList;
        $result->contentType = new ProcessedContentType();
        $result->contentType->contentBlock = $contentBlock;
        $result->contentType->typeName = $typeName;
        $result->contentType->table = $table;
        $result->tableDefinition = new ProcessedTableDefinition();
        $result->tableDefinition->useAsLabel = $yaml['useAsLabel'] ?? '';
        $result->tableDefinition->typeField = $typeField;
        $result->tableDefinition->isRootTable = $isRootTable;
        $result->tableDefinition->isAggregateRoot = $yaml['aggregateRoot'] ?? null;
        foreach ($yaml['fields'] as $rootField) {
            $rootFieldType = ($rootField['useExistingField'] ?? false)
                ? TypeResolver::resolve($rootField['identifier'] ?? '', $table)
                : FieldType::from($rootField['type']);
            $rootFieldIdentifier = $rootField['identifier'];
            if ($rootFieldType === FieldType::PALETTE) {
                // Ignore empty Palettes.
                if (($rootField['fields'] ?? []) === []) {
                    continue;
                }
                $fields = [];
                $paletteShowItems = [];
                foreach ($rootField['fields'] as $paletteField) {
                    $paletteFieldType = ($paletteField['useExistingField'] ?? false)
                        ? TypeResolver::resolve($paletteField['identifier'] ?? '', $table)
                        : FieldType::from($paletteField['type']);
                    if ($paletteFieldType === FieldType::LINEBREAK) {
                        $paletteShowItems[] = '--linebreak--';
                    } else {
                        $fields[] = $paletteField;
                        $paletteShowItems[] = $isRootTable && $this->isPrefixEnabledForField($contentBlock, $paletteField)
                            ? UniqueNameUtility::createUniqueColumnNameFromContentBlockName($contentBlock->getName(), $paletteField['identifier'])
                            : $paletteField['identifier'];
                    }
                }
                $languagePath->addPathSegment('palettes.' . $rootFieldIdentifier);
                $label = $rootField['label'] ?? '';
                $description = $rootField['description'] ?? '';
                $palette = [
                    'label' => $label !== '' ? $label : $languagePath->getCurrentPath() . '.label',
                    'description' => $description !== '' ? $description : $languagePath->getCurrentPath() . '.description',
                    'showitem' => $paletteShowItems,
                ];
                $paletteIdentifier = $isRootTable && $this->isPrefixEnabledForField($contentBlock, $rootField)
                    ? UniqueNameUtility::createUniqueColumnNameFromContentBlockName($contentBlock->getName(), $rootFieldIdentifier)
                    : $rootFieldIdentifier;
                $result->tableDefinition->palettes[$paletteIdentifier] = $palette;
                $result->contentType->showItems[] = '--palette--;;' . $paletteIdentifier;
                $languagePath->popSegment();
            } elseif ($rootFieldType === FieldType::TAB) {
                $languagePath->addPathSegment('tabs.' . $rootFieldIdentifier);
                $label = ($rootField['label'] ?? '') !== '' ? $rootField['label'] : $languagePath->getCurrentPath();
                $result->contentType->showItems[] = '--div--;' . $label;
                $languagePath->popSegment();
                continue;
            } else {
                $result->contentType->showItems[] = $isRootTable && $this->isPrefixEnabledForField($contentBlock, $rootField)
                    ? UniqueNameUtility::createUniqueColumnNameFromContentBlockName($contentBlock->getName(), $rootFieldIdentifier)
                    : $rootFieldIdentifier;
                $fields = [$rootField];
            }

            foreach ($fields as $field) {
                $identifier = $field['identifier'];
                $fieldType = ($field['useExistingField'] ?? false)
                    ? TypeResolver::resolve($identifier, $table)
                    : FieldType::from($field['type']);
                $languagePath->addPathSegment($identifier);

                if ($fieldType === FieldType::FLEXFORM) {
                    $field = $this->processFlexForm($field, $typeField, $typeName, $languagePath);
                }

                // Recursive call for Collection (inline) fields.
                if ($fieldType === FieldType::COLLECTION) {
                    $inlineTable = $this->isPrefixEnabledForField($contentBlock, $field)
                        ? UniqueNameUtility::createUniqueColumnNameFromContentBlockName($contentBlock->getName(), $identifier)
                        : $identifier;
                    $field['properties']['foreign_table'] ??= $inlineTable;
                    $field['properties']['foreign_match_fields'] = [
                        'fieldname' => $inlineTable,
                    ];
                    if (!empty($field['fields'])) {
                        $result->tableDefinitionList = $this->processContentBlock(
                            yaml: $field,
                            contentBlock: $contentBlock,
                            table: $inlineTable,
                            rootTable: $rootTable,
                            tableDefinitionList: $result->tableDefinitionList,
                            languagePath: $languagePath,
                        );
                    }
                }

                $field['languagePath'] = clone $languagePath;

                $uniqueColumnName = $isRootTable && $this->isPrefixEnabledForField($contentBlock, $field)
                    ? UniqueNameUtility::createUniqueColumnNameFromContentBlockName($contentBlock->getName(), $identifier)
                    : $identifier;
                $fieldArray = [
                    'uniqueIdentifier' => $uniqueColumnName,
                    'config' => $field,
                    'type' => $fieldType,
                ];
                $result->tableDefinition->fields[$uniqueColumnName] = $fieldArray;

                if ($isRootTable) {
                    $result->contentType->columns[] = $uniqueColumnName;
                    $result->contentType->overrideColumns[] = TcaFieldDefinition::createFromArray($fieldArray);
                }
                $languagePath->popSegment();
            }
        }
        return $result;
    }

    private function createNewTableDefinition(ProcessedTableDefinition $processedTableDefinition): array
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

    private function createNewContentType(ProcessedContentType $contentType): array
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
        // Only Content Elements (=tt_content) have a "New Content Element Wizard".
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
            $rootFieldType = ($rootField['useExistingField'] ?? false)
                ? TypeResolver::resolve($rootField['identifier'] ?? '', $table)
                : FieldType::from($rootField['type']);
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
                    $paletteFieldType = ($paletteField['useExistingField'] ?? false)
                        ? TypeResolver::resolve($paletteField['identifier'] ?? '', $table)
                        : FieldType::from($paletteField['type']);
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
                $fieldType = ($field['useExistingField'] ?? false)
                    ? TypeResolver::resolve($identifier, $table)
                    : FieldType::from($field['type']);
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
                    $inlineTable = $this->isPrefixEnabledForField($contentBlock, $field)
                        ? UniqueNameUtility::createUniqueColumnNameFromContentBlockName($contentBlock->getName(), $identifier)
                        : $identifier;
                    $this->validateContentBlock($field, $contentBlock, $inlineTable);
                }
            }
        }
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
}
