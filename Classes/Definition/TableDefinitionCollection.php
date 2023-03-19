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

/**
 * @internal Not part of TYPO3's public API.
 */
final class TableDefinitionCollection implements \IteratorAggregate
{
    /** @var TableDefinition[] */
    private array $definitions = [];
    /** @var list<string> */
    private array $customTables = [];

    public function addTable(TableDefinition $tableDefinition, $isCustomTable = false): void
    {
        if (!$this->hasTable($tableDefinition->getTable())) {
            $this->definitions[$tableDefinition->getTable()] = $tableDefinition;
            if ($isCustomTable) {
                $this->customTables[] = $tableDefinition->getTable();
            }
        }
    }

    public function isCustomTable(TableDefinition $tableDefinition): bool
    {
        return in_array($tableDefinition->getTable(), $this->customTables, true);
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
     * @param array<ParsedContentBlock> $contentBlocks
     */
    public static function createFromArray(array $contentBlocks): TableDefinitionCollection
    {
        $tableDefinitionCollection = new self();
        $tableDefinitionList = [];
        foreach ($contentBlocks as $contentBlock) {
            $table = $contentBlock->getYaml()['table'] ?? 'tt_content';
            $contentBlockName = $contentBlock->getName();
            [$vendor, $package] = explode('/', $contentBlockName);

            $uniqueIdentifiers = [];
            $uniquePaletteIdentifiers = [];
            $uniqueTabIdentifiers = [];
            $columns = [];
            $showItems = [];
            $overrideColumns = [];
            foreach ($contentBlock->getYaml()['fields'] ?? [] as $rootField) {
                $fieldType = FieldType::from($rootField['type']);
                if ($fieldType === FieldType::LINEBREAK) {
                    throw new \InvalidArgumentException(
                        'Linebreaks are only allowed within Palettes in content block "' . $contentBlockName . '".',
                        1679224094
                    );
                }
                if (!isset($rootField['identifier'])) {
                    throw new \InvalidArgumentException(
                        'A field is missing the required "identifier" in content block "' . $contentBlockName . '".',
                        1679225969
                    );
                }
                $uniqueRootColumnName = UniqueNameUtility::createUniqueColumnNameFromContentBlockName($contentBlockName, $rootField['identifier']);
                if ($fieldType === FieldType::PALETTE) {
                    // Ignore empty Palettes.
                    if (($rootField['fields'] ?? []) === []) {
                        continue;
                    }
                    if (in_array($rootField['identifier'], $uniquePaletteIdentifiers, true)) {
                        throw new \InvalidArgumentException(
                            'The palette identifier "' . $rootField['identifier'] . '" in content block "' . $contentBlockName . '" does exist more than once. Please choose unique identifiers.',
                            1679161623
                        );
                    }
                    $uniquePaletteIdentifiers[] = $rootField['identifier'];
                    $showItems[] = '--palette--;;' . $uniqueRootColumnName;
                    $fields = [];
                    $baseLanguagePath = 'LLL:' . $contentBlock->getPath() . '/' . ContentBlockPathUtility::getLanguageFilePath() . ':palettes.' . $rootField['identifier'];
                    $paletteShowItems = [];
                    foreach ($rootField['fields'] as $paletteField) {
                        $paletteFieldType = FieldType::from($paletteField['type']);
                        if ($paletteFieldType === FieldType::PALETTE) {
                            throw new \InvalidArgumentException(
                                'Palette "' . $paletteField['identifier'] . '" is not allowed inside palette "' . $rootField['identifier'] . '" in content block "' . $contentBlockName . '".',
                                1679167139
                            );
                        }
                        if ($paletteFieldType === FieldType::TAB) {
                            throw new \InvalidArgumentException(
                                'Tab "' . $paletteField['identifier'] . '" is not allowed inside palette "' . $rootField['identifier'] . '" in content block "' . $contentBlockName . '".',
                                1679245227
                            );
                        }
                        if ($paletteFieldType === FieldType::LINEBREAK) {
                            $paletteShowItems[] = '--linebreak--';
                        } else {
                            $fields[] = $paletteField;
                            $paletteShowItems[] = ($paletteField['useExistingField'] ?? false) ? $paletteField['identifier'] : UniqueNameUtility::createUniqueColumnNameFromContentBlockName($contentBlockName, $paletteField['identifier']);
                        }
                    }
                    $tableDefinitionList[$table]['palettes'][$uniqueRootColumnName] = [
                        'label' => $baseLanguagePath . '.label',
                        'description' => $baseLanguagePath . '.description',
                        'showitem' => $paletteShowItems,
                    ];
                } elseif ($fieldType === FieldType::TAB) {
                    if (in_array($rootField['identifier'], $uniqueTabIdentifiers, true)) {
                        throw new \InvalidArgumentException(
                            'The tab identifier "' . $rootField['identifier'] . '" in content block "' . $contentBlockName . '" does exist more than once. Please choose unique identifiers.',
                            1679244116
                        );
                    }
                    $showItems[] = '--div--;' . 'LLL:' . $contentBlock->getPath() . '/' . ContentBlockPathUtility::getLanguageFilePath() . ':tabs.' . $rootField['identifier'];
                    $uniqueTabIdentifiers[] = $rootField['identifier'];
                    continue;
                } else {
                    $showItems[] = ($rootField['useExistingField'] ?? false) ? $rootField['identifier'] : $uniqueRootColumnName;
                    $fields = [$rootField];
                }
                foreach ($fields as $field) {
                    if (in_array($field['identifier'], $uniqueIdentifiers, true)) {
                        throw new \InvalidArgumentException(
                            'The identifier "' . $field['identifier'] . '" in content block ' . $contentBlockName . ' does exist more than once. Please choose unique identifiers.',
                            1677407941
                        );
                    }
                    $uniqueIdentifiers[] = $field['identifier'];

                    $useExistingField = false;
                    if ($field['useExistingField'] ?? false) {
                        $uniqueColumnName = $field['identifier'];
                        $useExistingField = true;
                    } else {
                        $uniqueColumnName = UniqueNameUtility::createUniqueColumnNameFromContentBlockName($contentBlockName, $field['identifier']);
                        // Prevent reusing not allowed fields (e.g. system fields).
                        $field['useExistingField'] = false;
                    }
                    $columns[] = $uniqueColumnName;

                    $processedField = $tableDefinitionCollection->processCollections(
                        field: $field,
                        table: $uniqueColumnName,
                        languagePath: ['LLL:' . $contentBlock->getPath() . '/' . ContentBlockPathUtility::getLanguageFilePath() . ':' . $field['identifier']],
                        contentBlockName: $contentBlockName,
                        parentTable: $table,
                        rootTable: $table,
                    );
                    $fieldArray = [
                        'uniqueIdentifier' => $uniqueColumnName,
                        'config' => $processedField,
                    ];
                    $tableDefinitionList[$table]['fields'][$uniqueColumnName] = $fieldArray;
                    if ($useExistingField) {
                        $overrideColumns[] = TcaFieldDefinition::createFromArray($fieldArray);
                    }
                }
            }

            $tableDefinitionList[$table]['elements'][] = [
                'identifier' => $contentBlockName,
                'columns' => $columns,
                'showItems' => $showItems,
                'overrideColumns' => $overrideColumns,
                'vendor' => $vendor,
                'package' => $package,
                'wizardGroup' => $contentBlock->getYaml()['group'] ?? null,
                'icon' => $contentBlock->getIcon(),
                'iconProvider' => $contentBlock->getIconProvider(),
                'typeField' => $contentBlock->getYaml()['typeField'] ?? 'CType',
                'typeName' => $contentBlock->getYaml()['typeName'] ?? UniqueNameUtility::contentBlockNameToTypeIdentifier($contentBlockName),
                'priority' => (int)($contentBlock->getYaml()['priority'] ?? 0),
            ];
        }

        foreach ($tableDefinitionList as $table => $tableDefinition) {
            $tableDefinitionCollection->addTable(TableDefinition::createFromTableArray($table, $tableDefinition));
        }
        return $tableDefinitionCollection;
    }

    private function processCollections(array $field, string $table, array $languagePath, string $contentBlockName, string $parentTable, string $rootTable): array
    {
        $field['languagePath'] = implode('.', $languagePath);
        if (FieldType::from($field['type']) !== FieldType::COLLECTION || empty($field['properties']['fields'])) {
            return $field;
        }

        $field['properties']['foreign_table'] = $table;
        $field['properties']['foreign_field'] = 'foreign_table_parent_uid';

        $uniqueIdentifiers = [];
        $uniquePaletteIdentifiers = [];
        $uniqueTabIdentifiers = [];
        $showItems = [];
        $tableDefinition = [];
        $tableDefinition['useAsLabel'] = $field['useAsLabel'] ?? '';
        foreach ($field['properties']['fields'] as $collectionRootField) {
            $collectionRootFieldType = FieldType::from($collectionRootField['type']);
            if ($collectionRootFieldType === FieldType::LINEBREAK) {
                throw new \InvalidArgumentException(
                    'Linebreaks are only allowed within Palettes in Collection "' . $field['identifier'] . '" in content block "' . $contentBlockName . '".',
                    1679224392
                );
            }
            if (!isset($collectionRootField['identifier'])) {
                throw new \InvalidArgumentException(
                    'A field is missing the required "identifier" in Collection "' . $field['identifier'] . '" in content block "' . $contentBlockName . '".',
                    1679226075
                );
            }
            if ($collectionRootFieldType === FieldType::PALETTE) {
                // Ignore empty Palettes.
                if (($collectionRootField['fields'] ?? []) === []) {
                    continue;
                }
                if (in_array($collectionRootField['identifier'], $uniquePaletteIdentifiers, true)) {
                    throw new \InvalidArgumentException(
                        'The palette identifier "' . $collectionRootField['identifier'] . '" in Collection "' . $field['identifier'] . '" in content block ' . $contentBlockName . ' does exist more than once. Please choose unique identifiers.',
                        1679168022
                    );
                }
                $uniquePaletteIdentifiers[] = $collectionRootField['identifier'];
                $fields = [];
                $paletteShowItems = [];
                foreach ($collectionRootField['fields'] as $collectionRootPaletteField) {
                    $paletteFieldType = FieldType::from($collectionRootPaletteField['type']);
                    if ($paletteFieldType === FieldType::PALETTE) {
                        throw new \InvalidArgumentException(
                            'Palette "' . $collectionRootPaletteField['identifier'] . '" is not allowed inside palette "' . $collectionRootField['identifier'] . '" in Collection "' . $field['identifier'] . '" in content block "' . $contentBlockName . '".',
                            1679168602
                        );
                    }
                    if ($paletteFieldType === FieldType::TAB) {
                        throw new \InvalidArgumentException(
                            'Tab "' . $collectionRootPaletteField['identifier'] . '" is not allowed inside palette "' . $collectionRootField['identifier'] . '" in Collection "' . $field['identifier'] . '" in content block "' . $contentBlockName . '".',
                            1679245193
                        );
                    }
                    if ($paletteFieldType === FieldType::LINEBREAK) {
                        $paletteShowItems[] = '--linebreak--';
                    } else {
                        $fields[] = $collectionRootPaletteField;
                        $paletteShowItems[] = $collectionRootPaletteField['identifier'];
                    }
                }
                $tableDefinition['palettes'][$collectionRootField['identifier']] = [
                    'label' => $field['languagePath'] . '.palettes.' . $collectionRootField['identifier'] . '.label',
                    'description' => $field['languagePath'] . '.palettes.' . $collectionRootField['identifier'] . '.description',
                    'showitem' => $paletteShowItems,
                ];
                $showItems[] = '--palette--;;' . $collectionRootField['identifier'];
            } elseif ($collectionRootFieldType === FieldType::TAB) {
                if (in_array($collectionRootField['identifier'], $uniqueTabIdentifiers, true)) {
                    throw new \InvalidArgumentException(
                        'The tab identifier "' . $collectionRootField['identifier'] . '" in Collection "' . $field['identifier'] . '" in content block ' . $contentBlockName . ' does exist more than once. Please choose unique identifiers.',
                        1679243686
                    );
                }
                $uniqueTabIdentifiers[] = $collectionRootField['identifier'];
                $showItems[] = '--div--;' . $field['languagePath'] . '.tabs.' . $collectionRootField['identifier'];
                continue;
            } else {
                $showItems[] = $collectionRootField['identifier'];
                $fields = [$collectionRootField];
            }

            foreach ($fields as $collectionField) {
                $identifier = $collectionField['identifier'];
                if (in_array($identifier, $uniqueIdentifiers, true)) {
                    throw new \InvalidArgumentException(
                        'The identifier "' . $identifier . '" in content block ' . $contentBlockName . ' in Collection "' . $field['identifier'] . '" does exist more than once. Please choose unique identifiers.',
                        1677407942
                    );
                }
                $uniqueIdentifiers[] = $identifier;
                $languagePath[] = $identifier;
                $childField = $this->processCollections(
                    field: $collectionField,
                    table: UniqueNameUtility::createUniqueColumnNameFromContentBlockName($contentBlockName, $identifier),
                    languagePath: $languagePath,
                    contentBlockName: $contentBlockName,
                    parentTable: $table,
                    rootTable: $rootTable
                );
                // Since we can't check TCA and collection tables are individual tables
                // the useExistingField is not allowed on collections
                $childField['useExistingField'] = false;

                $tableDefinition['fields'][$identifier] = [
                    'uniqueIdentifier' => $identifier,
                    'config' => $childField,
                ];

                array_pop($languagePath);
            }
            $tableDefinition['showItems'] = $showItems;
        }

        if ($this->hasTable($table)) {
            throw new \InvalidArgumentException('A Collection field with the identifier "' . $field['identifier'] . '" exists more than once. Please choose another name.', 1672449082);
        }

        // Add parent table information.
        $tableDefinition['parentTable'] = $parentTable;
        // The reason we check for the root table is that only custom (child) tables have the prefixed identifier.
        $tableDefinition['parentField'] = $rootTable === $parentTable ? $table : $field['identifier'];
        $this->addTable(
            tableDefinition: TableDefinition::createFromTableArray($table, $tableDefinition),
            isCustomTable: true
        );
        return $field;
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
