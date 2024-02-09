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

use TYPO3\CMS\ContentBlocks\Definition\Factory\TableDefinitionCollectionFactory;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinition;
use TYPO3\CMS\ContentBlocks\Loader\ContentBlockLoader;
use TYPO3\CMS\Core\Database\Event\AlterTableDefinitionStatementsEvent;

/**
 * @internal Not part of TYPO3's public API.
 */
class SqlGenerator
{
    public function __construct(
        protected readonly ContentBlockLoader $contentBlockLoader,
        protected readonly TableDefinitionCollectionFactory $tableDefinitionCollectionFactory,
    ) {}

    public function __invoke(AlterTableDefinitionStatementsEvent $event): void
    {
        $contentBlocksSqlData = $this->generate();
        $mergedSqlData = array_merge($contentBlocksSqlData, $event->getSqlData());
        $event->setSqlData($mergedSqlData);
    }

    public function generate(): array
    {
        $contentBlockRegistry = $this->contentBlockLoader->loadUncached();
        $tableDefinitionCollection = $this->tableDefinitionCollectionFactory->createUncached($contentBlockRegistry);
        $sql = [];
        foreach ($tableDefinitionCollection as $tableDefinition) {
            foreach ($tableDefinition->getSqlColumnDefinitionCollection() as $column) {
                if ($column->getSql() === '') {
                    continue;
                }
                $sql[] = 'CREATE TABLE `' . $tableDefinition->getTable() . '`' . '(' . $column->getSql() . ');';
            }
            $resultSql = $this->handleParentReferences($tableDefinition);
            $sql = array_merge($sql, $resultSql);
        }
        return $sql;
    }

    protected function handleParentReferences(TableDefinition $tableDefinition): array
    {
        $sql = [];
        foreach ($tableDefinition->getParentReferences() ?? [] as $parentReference) {
            $parentTcaConfig = $parentReference->getTca()['config'];
            if (isset($parentTcaConfig['foreign_field'])) {
                $foreignField = $parentTcaConfig['foreign_field'];
                $sqlStatement = 'CREATE TABLE `' . $tableDefinition->getTable() . '`(`' . $foreignField . '` int(11) DEFAULT \'0\' NOT NULL, KEY parent_uid (' . $foreignField . '));';
                if (!in_array($sqlStatement, $sql, true)) {
                    $sql[] = $sqlStatement;
                }
            }
            if (isset($parentTcaConfig['foreign_table_field'])) {
                $foreignTableField = $parentTcaConfig['foreign_table_field'];
                $sqlStatement = 'CREATE TABLE `' . $tableDefinition->getTable() . '`(`' . $foreignTableField . '` varchar(255) DEFAULT \'\' NOT NULL);';
                if (!in_array($sqlStatement, $sql, true)) {
                    $sql[] = $sqlStatement;
                }
            }
            foreach ($parentTcaConfig['foreign_match_fields'] ?? [] as $foreignMatchField => $foreignMatchValue) {
                $sqlStatement = 'CREATE TABLE `' . $tableDefinition->getTable() . '`(`' . $foreignMatchField . '` varchar(255) DEFAULT \'\' NOT NULL);';
                if (!in_array($sqlStatement, $sql, true)) {
                    $sql[] = $sqlStatement;
                }
            }
        }
        return $sql;
    }
}
