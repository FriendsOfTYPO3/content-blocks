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

use TYPO3\CMS\ContentBlocks\Definition\SqlColumnDefinition;
use TYPO3\CMS\ContentBlocks\Definition\SqlDefinition;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\Core\Database\Event\AlterTableDefinitionStatementsEvent;
use TYPO3\CMS\Core\SingletonInterface;

class SqlGenerator implements SingletonInterface
{
    public function __construct(
        protected readonly TableDefinitionCollection $tableDefinitionCollection
    ) {
    }

    /**
     * returns sql statements of all elements
     */
    protected function getSqlByConfiguration(): array
    {
        $sql = [];
        foreach ($this->tableDefinitionCollection as $tableDefinition) {
            /** @var SqlDefinition $sqlDefinition */
            $sqlDefinition = $tableDefinition->getSqlDefinition();
            $sqlString = '';
            /** @var SqlColumnDefinition $column */
            foreach($sqlDefinition as $column) {
                $sqlString .= (($sqlString === '') ? '' : ', ') . $column->getSqlDefinition();
            }
            if (strlen($sqlString) > 2) {
                $sqlString = 'CREATE TABLE `' . $sqlDefinition->getTable() . '` ' . '  ( ' . $sqlString . " );\n";
                $sql[] = $sqlString;
            }
        }

        return $sql;
    }

    /**
     * Adds the SQL for all elements to the psr-14 AlterTableDefinitionStatementsEvent event.
     */
    public function __invoke(AlterTableDefinitionStatementsEvent $event): void
    {
        $event->setSqlData(array_merge($event->getSqlData(), $this->getSqlByConfiguration()));
    }
}
