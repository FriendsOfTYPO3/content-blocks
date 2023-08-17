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

use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;

/**
 * @internal Not part of TYPO3's public API.
 */
final class TableDefinition
{
    private string $table = '';
    private bool $isRootTable = false;
    private bool $isAggregateRoot = true;
    private ?string $typeField = null;
    private string $useAsLabel = '';
    private bool $languageAware = true;
    private ?ContentType $contentType = null;
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
            ->withUseAsLabel($definition['useAsLabel'] ?? '')
            ->withIsRootTable($definition['isRootTable'] ?? false)
            ->withIsAggregateRoot((bool)($definition['aggregateRoot'] ?? true))
            ->withTypeField($definition['typeField'] ?? null)
            ->withLanguageAware($definition['languageAware'] ?? $tableDefinition->languageAware)
            ->withContentType($definition['contentType'] ?? null)
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

    public function isRootTable(): bool
    {
        return $this->isRootTable;
    }

    public function isAggregateRoot(): bool
    {
        return $this->isAggregateRoot;
    }

    public function getTypeField(): ?string
    {
        return $this->typeField;
    }

    public function getUseAsLabel(): string
    {
        return $this->useAsLabel;
    }

    public function hasUseAsLabel(): bool
    {
        return $this->useAsLabel !== '';
    }

    public function isLanguageAware(): bool
    {
        return $this->languageAware;
    }

    public function getContentType(): ContentType
    {
        return $this->contentType;
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

    public function withIsRootTable(bool $isRootTable): TableDefinition
    {
        $clone = clone $this;
        $clone->isRootTable = $isRootTable;
        return $clone;
    }

    public function withIsAggregateRoot(bool $isAggregateRoot): TableDefinition
    {
        $clone = clone $this;
        $clone->isAggregateRoot = $isAggregateRoot;
        return $clone;
    }

    public function withTypeField(?string $typeField): TableDefinition
    {
        $clone = clone $this;
        $clone->typeField = $typeField;
        return $clone;
    }

    public function withUseAsLabel(string $useAsLabel): TableDefinition
    {
        $clone = clone $this;
        $clone->useAsLabel = $useAsLabel;
        return $clone;
    }

    public function withLanguageAware(bool $languageAware): TableDefinition
    {
        $clone = clone $this;
        $clone->languageAware = $languageAware;
        return $clone;
    }

    public function withContentType(ContentType $contentType): TableDefinition
    {
        $clone = clone $this;
        $clone->contentType = $contentType;
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
