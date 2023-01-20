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
            ->withPaletteDefinitionCollection(PaletteDefinitionCollection::createFromArray($definition['palettes'] ?? [], $table));

        if (!empty($definition['elements'])) {
            $tableDefinition = $tableDefinition->withTypeDefinitionCollection(TypeDefinitionCollection::createFromArray($definition['elements'], $table));
        }

        return $tableDefinition;
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function getTypeDefinitionCollection(): ?TypeDefinitionCollection
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
