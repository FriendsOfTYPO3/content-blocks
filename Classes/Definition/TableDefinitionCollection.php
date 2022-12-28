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

use TYPO3\CMS\ContentBlocks\Converter\NamingConverter;
use TYPO3\CMS\ContentBlocks\Enumeration\FieldType;
use TYPO3\CMS\ContentBlocks\Service\ConfigurationService;
use TYPO3\CMS\ContentBlocks\Utility\AffixUtility;
use TYPO3\CMS\Core\SingletonInterface;

final class TableDefinitionCollection implements \IteratorAggregate, SingletonInterface
{
    /**
     * @var TableDefinition[]
     */
    private array $definitions = [];

    public function __clone()
    {
        $this->definitions = array_map(function (TableDefinition $tableDefinition) {
            return clone $tableDefinition;
        }, $this->definitions);
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

    public function toArray(): array
    {
        $tablesArray = array_merge([], ...$this->getTablesAsArray());
        return [
            'tables' => $tablesArray,
        ];
    }

    public function getTablesAsArray(): iterable
    {
        foreach ($this->definitions as $definition) {
            yield [$definition->getTable() => $definition->toArray()];
        }
    }

    public static function createFromArray(array $contentBlocks): TableDefinitionCollection
    {
        $tableDefinitionCollection = new self();
        $tableDefinition = [];

        // Since we need to sum up all lvl 0 ContentBlock fields to tt_content,
        // we have to handle the tt_content table a bit different from collection tables.
        foreach ($contentBlocks as $contentBlock) {
            $composerName = $contentBlock['composerJson']['name'];
            [$vendor, $package] = explode('/', $composerName);
            $cType = NamingConverter::composerNameToCType($composerName);
            $collectionTablePrefix = AffixUtility::prefixCollection($cType);
            $ttContentColumnPrefix = AffixUtility::prefixDbColumn($cType);
            $contentBlockPath = ConfigurationService::getContentBlockLegacyPath() . '/' . $package;

            // collect data for tt_content from each ContentBlock
            $columns = [];
            foreach ($contentBlock['yaml']['fields'] ?? [] as $ttContentField) {
                // unique tt_content column name
                $column = $ttContentColumnPrefix . '_' . $ttContentField['identifier'];
                $columns[] = $column;
                $languagePath = $contentBlockPath . '/' . ConfigurationService::getContentBlocksPrivatePath() . '/Language/Labels.xlf:' . $ttContentField['identifier'];

                $ttContentField = $tableDefinitionCollection->processCollections(
                    $ttContentField,
                    'tt_content',
                    $column,
                    $languagePath,
                    $collectionTablePrefix
                );

                // add to tt_content fields
                $tableDefinition['fields'][$column] = [
                    'identifier' => $column,
                    'config' => $ttContentField,
                ];
            }

            // elements for TypeDefinition
            $tableDefinition['elements'][] = [
                'composerName' => $contentBlock['composerJson']['name'],
                'identifier' => $contentBlock['composerJson']['name'],
                'columns' => $columns,
                'vendor' => $vendor,
                'package' => $package,
                'publicPath' => $contentBlockPath . '/' . ConfigurationService::getContentBlocksPublicPath() . '/',
                'privatePath' => $contentBlockPath . '/' . ConfigurationService::getContentBlocksPrivatePath() . '/',
                'wizardGroup' => ($contentBlock['yaml']['group'] ?? ''),
                'icon' => $contentBlock['icon'],
                'iconProvider' => $contentBlock['iconProvider'],
            ];

        }
        // add tt_content definition
        $tableDefinitionCollection->addTable(
            TableDefinition::createFromTableArray(
                'tt_content',
                $tableDefinition,
            )
        );
        return $tableDefinitionCollection;
    }

    private function processCollections(array $field, string $table, string $currentColumnName, string $languagePath, string $collectionTablePrefix = ''): array
    {
        if (FieldType::from($field['type']) !== FieldType::COLLECTION || empty($field['properties']['fields'])) {
            $field['languagePath'] = $languagePath;
            return $field;
        }

        // unique collection table name
        $collectionTableName = (($collectionTablePrefix !== '') ? $collectionTablePrefix . '_' : '') . (($table === 'tt_content') ? '' : $table . '_') . $field['identifier'];
        $collectionTableName = str_replace('-', '_', $collectionTableName);

        // enrich infos for inline relations
        // @todo move to TcaGenerator, foreign_field should be moved to a constant somewhere, as it is also used in SqlGenerator.
        $field['properties']['foreign_table'] = $collectionTableName; // The table name of the child records
        $field['properties']['foreign_field'] = 'foreign_parent_table_uid'; // the field of the child record pointing to the parent record. This defines where to store the uid of the parent record.

        $tableDefinition = [];

        // collect data for tt_content from each ContentBlock
        foreach ($field['properties']['fields'] as $collectionField) {
            $languagePath .= '.' . $collectionField['identifier'];
            // add to field to table
            $tableDefinition['fields'][$collectionField['identifier']] = [
                'identifier' => $collectionField['identifier'], // currentColumnName
                'config' => $this->processCollections(
                    $collectionField,
                    $collectionTableName,
                    $collectionField['identifier'], // currentColumnName
                    $languagePath,
                    $collectionTablePrefix
                ),
            ];
        }
        // @todo: find a better way to add this field for collections only
        $tableDefinition['fields']['foreign_parent_table_uid'] = [
            'identifier' => 'foreign_parent_table_uid',
            'config' => [
                'identifier' => 'foreign_parent_table_uid',
                'type' => 'Number',
                'languagePath' => $languagePath,
            ],
        ];
        $tableDefinition['elements'][] = [
            'identifier' => $collectionTableName,
            'columns' => array_keys($tableDefinition['fields']),
            'typeField' => 'inline',
        ];

        $this->addTable(
            TableDefinition::createFromTableArray(
                $collectionTableName,
                $tableDefinition,
            )
        );

        $field['languagePath'] = $languagePath;
        return $field;
    }

    /**
     * @return \Traversable|TableDefinition[]
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->definitions);
    }
}
