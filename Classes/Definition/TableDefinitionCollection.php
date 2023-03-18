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
            $columns = [];
            $overrideColumns = [];
            foreach ($contentBlock->getYaml()['fields'] ?? [] as $field) {
                if (in_array($field['identifier'], $uniqueIdentifiers, true)) {
                    throw new \InvalidArgumentException(
                        'The identifier "' . $field['identifier'] . '" in package ' . $contentBlockName . ' does exist more than once. Please choose unique identifiers.',
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
                    languagePath: ['LLL:' . $contentBlock->getPackagePath() . '/' . ContentBlockPathUtility::getPathToDefaultLanguageFile() . ':' . $field['identifier']],
                    cbName: $contentBlockName,
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

            $tableDefinitionList[$table]['elements'][] = [
                'identifier' => $contentBlockName,
                'columns' => $columns,
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

    private function processCollections(array $field, string $table, array $languagePath, string $cbName, string $parentTable, string $rootTable): array
    {
        $field['languagePath'] = implode('.', $languagePath);
        if (FieldType::from($field['type']) !== FieldType::COLLECTION || empty($field['properties']['fields'])) {
            return $field;
        }

        $field['properties']['foreign_table'] = $table;
        $field['properties']['foreign_field'] = 'foreign_table_parent_uid';

        $uniqueIdentifiers = [];
        $tableDefinition = [];
        $tableDefinition['useAsLabel'] = $field['useAsLabel'] ?? '';
        foreach ($field['properties']['fields'] as $collectionField) {
            $identifier = $collectionField['identifier'];
            if (in_array($identifier, $uniqueIdentifiers, true)) {
                throw new \InvalidArgumentException(
                    'The identifier "' . $identifier . '" in package ' . $cbName . ' in Collection "' . $field['identifier'] . '" does exist more than once. Please choose unique identifiers.',
                    1677407942
                );
            }
            $uniqueIdentifiers[] = $identifier;
            $languagePath[] = $identifier;
            $childField = $this->processCollections(
                field: $collectionField,
                table: UniqueNameUtility::createUniqueColumnNameFromContentBlockName($cbName, $identifier),
                languagePath: $languagePath,
                cbName: $cbName,
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
