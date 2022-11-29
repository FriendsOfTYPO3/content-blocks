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

final class TableDefinition
{
    private string $table = '';
    private ?TypeDefinitionCollection $typeDefinitionCollection = null;
    private ?SqlDefinition $sqlDefinition = null;
    private ?TcaColumnsDefinition $tcaColumnsDefinition = null;
    private ?PaletteDefinitionCollection $paletteDefinitionCollection = null;

    public function __clone()
    {
        if ($this->typeDefinitionCollection instanceof TypeDefinitionCollection) {
            $this->typeDefinitionCollection = clone $this->typeDefinitionCollection;
        }
        if ($this->sqlDefinition instanceof SqlDefinition) {
            $this->sqlDefinition = clone $this->sqlDefinition;
        }
        if ($this->tcaColumnsDefinition instanceof TcaColumnsDefinition) {
            $this->tcaColumnsDefinition = clone $this->tcaColumnsDefinition;
        }
        if ($this->paletteDefinitionCollection instanceof PaletteDefinitionCollection) {
            $this->paletteDefinitionCollection = clone $this->paletteDefinitionCollection;
        }
    }

    public static function createFromTableArray(string $table, array $definition): TableDefinition
    {
        if ($table === '') {
            throw new \InvalidArgumentException('The name of the table must not be empty.', 1628672227);
        }

        $tableDefinition = new self();
        $tableDefinition = $tableDefinition
            ->withTable($table)
            ->withTcaColumnsDefinition(TcaColumnsDefinition::createFromArray($definition['fields'] ?? [], $table))
            ->withSqlDefinition(SqlDefinition::createFromArray($definition['fields'] ?? [], $table))
            ->withTypeDefinitionCollection(TypeDefinitionCollection::createFromArray($definition['elements'] ?? [], $table))
            ->withPaletteDefinitionCollection(PaletteDefinitionCollection::createFromArray($definition['palettes'] ?? [], $table));

        return $tableDefinition;
    }

    public function toArray(): array
    {
        $definitionArray = [];
        if ($this->typeDefinitionCollection instanceof TypeDefinitionCollection && $this->typeDefinitionCollection->count() > 0) {
            $definitionArray['elements'] = $this->typeDefinitionCollection->toArray();
        }
        if ($this->sqlDefinition instanceof SqlDefinition && $this->sqlDefinition->count() > 0) {
            $definitionArray['sql'] = $this->sqlDefinition->toArray();
        }
        if ($this->tcaColumnsDefinition instanceof TcaColumnsDefinition && $this->tcaColumnsDefinition->count() > 0) {
            $definitionArray['tca'] = $this->tcaColumnsDefinition->toArray();
        }
        if ($this->paletteDefinitionCollection instanceof PaletteDefinitionCollection && $this->paletteDefinitionCollection->count() > 0) {
            $definitionArray['palettes'] = $this->paletteDefinitionCollection->toArray();
        }

        return $definitionArray;
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function getTypeDefinitionCollection(): TypeDefinitionCollection
    {
        return $this->typeDefinitionCollection;
    }

    public function getSqlDefinition(): SqlDefinition
    {
        return $this->sqlDefinition;
    }

    public function getTcaColumnsDefinition(): TcaColumnsDefinition
    {
        return $this->tcaColumnsDefinition;
    }

    public function getPaletteDefinitionCollection(): PaletteDefinitionCollection
    {
        return $this->paletteDefinitionCollection;
    }

    public function withTable(string $table): TableDefinition
    {
        $clone = clone $this;
        $clone->table = $table;
        return $clone;
    }

    public function withTypeDefinitionCollection(TypeDefinitionCollection $typeDefinitionCollection): TableDefinition
    {
        $clone = clone $this;
        $clone->typeDefinitionCollection = $typeDefinitionCollection;
        return $clone;
    }

    public function withSqlDefinition(SqlDefinition $sqlDefinition): TableDefinition
    {
        $clone = clone $this;
        $clone->sqlDefinition = $sqlDefinition;
        return $clone;
    }

    public function withTcaColumnsDefinition(TcaColumnsDefinition $tcaColumnsDefinition): TableDefinition
    {
        $clone = clone $this;
        $clone->tcaColumnsDefinition = $tcaColumnsDefinition;
        return $clone;
    }

    public function withPaletteDefinitionCollection(PaletteDefinitionCollection $paletteDefinitionCollection): TableDefinition
    {
        $clone = clone $this;
        $clone->paletteDefinitionCollection = $paletteDefinitionCollection;
        return $clone;
    }
}
