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
use TYPO3\CMS\ContentBlocks\Utility\LanguagePathUtility;
use TYPO3\CMS\ContentBlocks\Utility\UniqueNameUtility;
use TYPO3\CMS\Core\SingletonInterface;

final class TableDefinitionCollection implements \IteratorAggregate, SingletonInterface
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

    public static function createFromArray(array $contentBlocks): TableDefinitionCollection
    {
        $tableDefinitionCollection = new self();
        $tableDefinitionList = [];
        foreach ($contentBlocks as $contentBlock) {
            $table = $contentBlock['yaml']['table'] ?? 'tt_content';
            $composerName = $contentBlock['composerJson']['name'];
            [$vendor, $package] = explode('/', $composerName);

            $columns = [];
            foreach ($contentBlock['yaml']['fields'] ?? [] as $field) {
                if (isset($field['useExistingField']) &&
                    $field['useExistingField'] === true
                ) {
                    $uniqueColumnName = $field['identifier'];
                } else {
                    $uniqueColumnName = UniqueNameUtility::createUniqueColumnName($composerName, $field['identifier']);
                }
                $columns[] = $uniqueColumnName;

                $field = $tableDefinitionCollection->processCollections(
                    field: $field,
                    table: $uniqueColumnName,
                    languagePath: [LanguagePathUtility::getPartialLanguageIdentifierPath($package, $vendor, $field['identifier'])],
                    composerName: $composerName
                );
                $tableDefinitionList[$table]['fields'][$uniqueColumnName] = [
                    'uniqueIdentifier' => $uniqueColumnName,
                    'config' => $field,
                ];
            }

            $tableDefinitionList[$table]['elements'][] = [
                'identifier' => $composerName,
                'columns' => $columns,
                'vendor' => $vendor,
                'package' => $package,
                'wizardGroup' => $contentBlock['yaml']['group'] ?? null,
                'icon' => $contentBlock['icon'] ?? '',
                'iconProvider' => $contentBlock['iconProvider'] ?? '',
                'typeField' => $contentBlock['yaml']['typeField'] ?? 'CType',
                'typeName' => $contentBlock['yaml']['typeName'] ?? UniqueNameUtility::composerNameToTypeIdentifier($composerName),
            ];
        }

        foreach ($tableDefinitionList as $table => $tableDefinition) {
            $tableDefinitionCollection->addTable(TableDefinition::createFromTableArray($table, $tableDefinition));
        }
        return $tableDefinitionCollection;
    }

    private function processCollections(array $field, string $table, array $languagePath, string $composerName): array
    {
        $field['languagePath'] = implode('.', $languagePath);
        if (FieldType::from($field['type']) !== FieldType::COLLECTION || empty($field['properties']['fields'])) {
            return $field;
        }

        $field['properties']['foreign_table'] = $table;
        $field['properties']['foreign_field'] = 'foreign_table_parent_uid';

        $tableDefinition = [];
        foreach ($field['properties']['fields'] as $collectionField) {
            $identifier = $collectionField['identifier'];
            $languagePath[] = $identifier;
            $childField = $this->processCollections(
                field: $collectionField,
                table: UniqueNameUtility::createUniqueColumnName($composerName, $identifier),
                languagePath: $languagePath,
                composerName: $composerName
            );
            $tableDefinition['fields'][$identifier] = [
                'uniqueIdentifier' => $identifier,
                'config' => $childField,
            ];
            array_pop($languagePath);
        }

        if ($this->hasTable($table)) {
            throw new \InvalidArgumentException('A Collection field with the identifier "' . $field['identifier'] . '" exists more than once. Please choose another name.', 1672449082);
        }
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

    /**
     * @return \Traversable<TableDefinition>
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->definitions);
    }
}
