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

use TYPO3\CMS\ContentBlocks\Loader\LoaderInterface;
use TYPO3\CMS\Core\Database\Event\AlterTableDefinitionStatementsEvent;

/**
 * @internal Not part of TYPO3's public API.
 */
class SqlGenerator
{
    public function __construct(
        protected readonly LoaderInterface $loader
    ) {
    }

    public function __invoke(AlterTableDefinitionStatementsEvent $event): void
    {
        $event->setSqlData(array_merge($event->getSqlData(), $this->generate()));
    }

    public function generate(): array
    {
        $tableDefinitionCollection = $this->loader->load(false);
        $sql = [];
        foreach ($tableDefinitionCollection as $tableDefinition) {
            foreach ($tableDefinition->getSqlDefinition() as $column) {
                if ($column->getSql() === '') {
                    continue;
                }
                $sql[] = 'CREATE TABLE `' . $tableDefinition->getTable() . '`' . '(' . $column->getSql() . ');';
            }
            if ($tableDefinitionCollection->isCustomTable($tableDefinition)) {
                $sql[] = 'CREATE TABLE `' . $tableDefinition->getTable() . '`(`foreign_table_parent_uid` int(11) DEFAULT \'0\' NOT NULL, KEY parent_uid (foreign_table_parent_uid));';
            }
        }
        return $sql;
    }
}
