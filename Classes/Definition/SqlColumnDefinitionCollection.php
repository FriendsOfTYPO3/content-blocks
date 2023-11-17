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

/**
 * @internal Not part of TYPO3's public API.
 */
final class SqlColumnDefinitionCollection implements \IteratorAggregate, \Countable
{
    private string $table = '';
    /** @var SqlColumnDefinition[] */
    private array $definitions = [];

    public function addColumn(SqlColumnDefinition $columnDefinition): void
    {
        if (!$this->hasColumn($columnDefinition->getColumn())) {
            $this->definitions[$columnDefinition->getColumn()] = $columnDefinition;
        }
    }

    public function hasColumn(string $column): bool
    {
        return isset($this->definitions[$column]);
    }

    public function getColumn(string $column): SqlColumnDefinition
    {
        if ($this->hasColumn($column)) {
            return $this->definitions[$column];
        }

        throw new \OutOfBoundsException('The column "' . $column . '" does not exist in table "' . $this->table . '".', 1629276303);
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public static function createFromArray(array $array, string $table): SqlColumnDefinitionCollection
    {
        $sqlDefinition = new self();
        $sqlDefinition->table = $table;
        foreach ($array as $columnDefinition) {
            if ($columnDefinition['config']['useExistingField'] ?? false) {
                continue;
            }
            $sqlDefinition->addColumn(new SqlColumnDefinition($columnDefinition));
        }
        return $sqlDefinition;
    }

    /**
     * @return \Iterator<SqlColumnDefinition>
     */
    public function getIterator(): \Iterator
    {
        return new \ArrayIterator($this->definitions);
    }

    public function count(): int
    {
        return count($this->definitions);
    }
}
