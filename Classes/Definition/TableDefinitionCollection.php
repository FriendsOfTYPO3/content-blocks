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

use TYPO3\CMS\ContentBlocks\Enumeration\FieldType;
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

    /**
     * @param ParsedContentBlock[] $contentBlocks
     */
    public static function createFromArray(array $contentBlocks): TableDefinitionCollection
    {
        $tableDefinitionCollection = new self();
        $tableDefinitionList = [];
        foreach ($contentBlocks as $contentBlock) {
            $table = $contentBlock->getYaml()['table'] ?? 'tt_content';
            $tableDefinitionList = $tableDefinitionCollection->processContentBlock(
                yaml: $contentBlock->getYaml(),
                contentBlock: $contentBlock,
                table: $table,
                rootTable: $table,
                tableDefinitionList: $tableDefinitionList,
            );
        }
        foreach ($tableDefinitionList as $table => $tableDefinition) {
            $tableDefinitionCollection->addTable(TableDefinition::createFromTableArray($table, $tableDefinition));
        }
        return $tableDefinitionCollection;
    }

    private function processContentBlock(array $yaml, ParsedContentBlock $contentBlock, string $table, string $rootTable, array $tableDefinitionList, ?LanguagePath $languagePath = null): array
    {
        $languagePath ??= new LanguagePath('LLL:' . $contentBlock->getPath() . '/' . ContentBlockPathUtility::getLanguageFilePath());
        $uniqueIdentifiers = [];
        $uniquePaletteIdentifiers = [];
        $uniqueTabIdentifiers = [];
        $columns = [];
        $showItems = [];
        $overrideColumns = [];
        $typeField = $contentBlock->getYaml()['typeField'] ?? $GLOBALS['TCA'][$table]['ctrl']['type'] ?? null;
        $typeName = $typeField === null
            ? '1'
            : $contentBlock->getYaml()['typeName'] ?? UniqueNameUtility::contentBlockNameToTypeIdentifier($contentBlock->getName());
        $tableDefinition = [];
        $tableDefinition['useAsLabel'] = $yaml['useAsLabel'] ?? '';
        $tableDefinition['typeField'] = $typeField;
        $isRootTable = $table === $rootTable;
        $isExistingTable = isset($GLOBALS['TCA'][$table]);
        $shouldCreateNewTable = !$isRootTable || !$isExistingTable;
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
                $paletteShowItems = [];
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
                $palette = [
                    'label' => $languagePath->getCurrentPath() . '.label',
                    'description' => $languagePath->getCurrentPath() . '.description',
                    'showitem' => $paletteShowItems,
                ];
                $paletteIdentifier = $isRootTable && $this->isPrefixEnabledForField($contentBlock, $rootField)
                    ? UniqueNameUtility::createUniqueColumnNameFromContentBlockName($contentBlock->getName(), $rootFieldIdentifier)
                    : $rootFieldIdentifier;
                $tableDefinition['palettes'][$paletteIdentifier] = $palette;
                $showItems[] = '--palette--;;' . $paletteIdentifier;
                $languagePath->popSegment();
            } elseif ($rootFieldType === FieldType::TAB) {
                if (in_array($rootFieldIdentifier, $uniqueTabIdentifiers, true)) {
                    throw new \InvalidArgumentException(
                        'The tab identifier "' . $rootFieldIdentifier . '" in content block "' . $contentBlock->getName() . '" does exist more than once. Please choose unique identifiers.',
                        1679243686
                    );
                }
                $uniqueTabIdentifiers[] = $rootFieldIdentifier;
                $languagePath->addPathSegment('tabs.' . $rootFieldIdentifier);
                $showItems[] = '--div--;' . $languagePath->getCurrentPath();
                $languagePath->popSegment();
                continue;
            } else {
                $showItems[] = $isRootTable && $this->isPrefixEnabledForField($contentBlock, $rootField)
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
                if (in_array($identifier, $uniqueIdentifiers, true)) {
                    throw new \InvalidArgumentException(
                        'The identifier "' . $identifier . '" in content block "' . $contentBlock->getName() . '" does exist more than once. Please choose unique identifiers.',
                        1677407942
                    );
                }
                $uniqueIdentifiers[] = $identifier;

                // Process FlexForm
                if ($fieldType === FieldType::FLEXFORM) {
                    $field['properties']['ds_pointerField'] = $typeField;
                    $flexFormElements = [];
                    foreach ($field['fields'] ?? [] as $flexFormField) {
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
                        $flexFormElements[$flexFormField['identifier']] = $flexFormTca;
                    }
                    $dataStructure = [
                        'sheets' => [
                            'sDEF' => [
                                'ROOT' => [
                                    'sheetTitle' => 'Content Blocks Standard Sheet',
                                    'type' => 'array',
                                    'el' => $flexFormElements,
                                ],
                            ],
                        ],
                    ];
                    $field['properties']['ds'][$typeName] = GeneralUtility::array2xml($dataStructure, '', 0, 'T3FlexForms', 4);
                }

                // Recursive call for Collection (inline) fields.
                if ($fieldType === FieldType::COLLECTION && !empty($field['fields'])) {
                    $inlineTable = $this->isPrefixEnabledForField($contentBlock, $field)
                        ? UniqueNameUtility::createUniqueColumnNameFromContentBlockName($contentBlock->getName(), $identifier)
                        : $identifier;
                    $field['properties']['foreign_table'] = $inlineTable;
                    $field['properties']['foreign_field'] = 'foreign_table_parent_uid';
                    $field['properties']['foreign_table_field'] = 'tablenames';
                    $field['properties']['foreign_match_fields'] = [
                        'fieldname' => $inlineTable,
                    ];
                    $tableDefinitionList = $this->processContentBlock(
                        yaml: $field,
                        contentBlock: $contentBlock,
                        table: $inlineTable,
                        rootTable: $rootTable,
                        tableDefinitionList: $tableDefinitionList,
                        languagePath: $languagePath,
                    );
                }

                $field['languagePath'] = clone $languagePath;
                if ($isRootTable) {
                    $uniqueColumnName = $this->isPrefixEnabledForField($contentBlock, $field)
                        ? UniqueNameUtility::createUniqueColumnNameFromContentBlockName($contentBlock->getName(), $identifier)
                        : $identifier;
                    $columns[] = $uniqueColumnName;
                    $fieldArray = [
                        'uniqueIdentifier' => $uniqueColumnName,
                        'config' => $field,
                        'type' => $fieldType,
                    ];
                    $tableDefinition['fields'][$uniqueColumnName] = $fieldArray;
                    $overrideColumns[] = TcaFieldDefinition::createFromArray($fieldArray);
                }

                $tableDefinition['isRootTable'] = $isRootTable;
                if ($shouldCreateNewTable) {
                    $uniqueColumnName = $isRootTable && $this->isPrefixEnabledForField($contentBlock, $field)
                        ? UniqueNameUtility::createUniqueColumnNameFromContentBlockName($contentBlock->getName(), $identifier)
                        : $identifier;
                    $tableDefinition['isCustomTable'] = true;
                    $tableDefinition['fields'][$uniqueColumnName] = [
                        'uniqueIdentifier' => $uniqueColumnName,
                        'config' => $field,
                        'type' => $fieldType,
                    ];
                }
                $languagePath->popSegment();
            }
            $tableDefinition['showItems'] = $showItems;
        }

        // If this is the root table, we add a new content type to the list of elements.
        [$vendor, $package] = explode('/', $contentBlock->getName());
        $elements = $tableDefinitionList[$table]['elements'] ?? [];
        $element = [
            'identifier' => $contentBlock->getName(),
            'columns' => $columns,
            'showItems' => $showItems,
            'overrideColumns' => $overrideColumns,
            'vendor' => $vendor,
            'package' => $package,
            'iconProvider' => $contentBlock->getIconProvider(),
            'typeName' => $typeName,
            'priority' => (int)($contentBlock->getYaml()['priority'] ?? 0),
        ];
        if ($table === 'tt_content') {
            $element['wizardGroup'] = $contentBlock->getYaml()['group'] ?? 'common';
            $element['icon'] = $contentBlock->getIcon();
        }
        $elements[] = $element;
        $tableDefinition['elements'] = $elements;

        // Collection fields are unique and require always an own table definition, which can't be shared across other
        // content blocks, so they can be added here directly.
        if ($shouldCreateNewTable && !$isRootTable) {
            $this->addTable(TableDefinition::createFromTableArray($table, $tableDefinition));
        } else {
            // Add / merge table definition to the list, so the combined result can be added later to the definition collection.
            $tableDefinitionList[$table] ??= $tableDefinition;
            $tableDefinitionList[$table] = array_replace_recursive($tableDefinitionList[$table], $tableDefinition);
        }

        return $tableDefinitionList;
    }

    protected function isPrefixEnabledForField(ParsedContentBlock $contentBlock, array $fieldConfiguration): bool
    {
        if (array_key_exists('useExistingField', $fieldConfiguration)) {
            return !$fieldConfiguration['useExistingField'];
        }
        if (array_key_exists('prefixField', $fieldConfiguration)) {
            return (bool)$fieldConfiguration['prefixField'];
        }
        return $contentBlock->prefixFields();
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

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->definitions);
    }
}
