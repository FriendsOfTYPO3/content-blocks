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

use TYPO3\CMS\ContentBlocks\Definition\Capability\TableDefinitionCapability;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentTypeDefinitionCollection;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentTypeInterface;

/**
 * @internal Not part of TYPO3's public API.
 */
final class TableDefinition
{
    private string $table = '';
    private bool $isAggregateRoot = true;
    private ?string $typeField = null;
    private TableDefinitionCapability $capability;
    private ?ContentType $contentType = null;
    private ?ContentTypeDefinitionCollection $contentTypeDefinitionCollection = null;
    private ?SqlColumnDefinitionCollection $sqlColumnDefinitionCollection = null;
    private ?TcaFieldDefinitionCollection $tcaFieldDefinitionCollection = null;
    private ?PaletteDefinitionCollection $paletteDefinitionCollection = null;

    public static function createFromTableArray(string $table, array $definition): TableDefinition
    {
        if ($table === '') {
            throw new \InvalidArgumentException('The name of the table must not be empty.', 1628672227);
        }

        $tableDefinition = new self();
        $tableDefinition = $tableDefinition
            ->withTable($table)
            ->withIsAggregateRoot((bool)($definition['aggregateRoot'] ?? true))
            ->withTypeField($definition['typeField'] ?? null)
            ->withCapability(TableDefinitionCapability::createFromArray($definition['raw']))
            ->withContentType($definition['contentType'] ?? null)
            ->withTcaColumnsDefinition(TcaFieldDefinitionCollection::createFromArray($definition['fields'] ?? [], $table))
            ->withSqlDefinition(SqlColumnDefinitionCollection::createFromArray($definition['fields'] ?? [], $table))
            ->withPaletteDefinitionCollection(PaletteDefinitionCollection::createFromArray($definition['palettes'] ?? [], $table));

        if (!empty($definition['elements'])) {
            $tableDefinition = $tableDefinition->withTypeDefinitionCollection(ContentTypeDefinitionCollection::createFromArray($definition['elements'], $table));
        }

        return $tableDefinition;
    }

    public function getDefaultTypeDefinition(): ContentTypeInterface
    {
        $typeDefinitionCollection = $this->getContentTypeDefinitionCollection();
        if ($typeDefinitionCollection->hasType('1')) {
            $defaultTypeDefinition = $typeDefinitionCollection->getType('1');
        } else {
            $defaultTypeDefinition = $typeDefinitionCollection->getFirst();
        }
        return $defaultTypeDefinition;
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function isAggregateRoot(): bool
    {
        return $this->isAggregateRoot;
    }

    public function getTypeField(): ?string
    {
        return $this->typeField;
    }

    public function hasTypeField(): bool
    {
        return $this->typeField !== null;
    }

    public function getCapability(): TableDefinitionCapability
    {
        return $this->capability;
    }

    public function getContentType(): ContentType
    {
        return $this->contentType;
    }

    public function getContentTypeDefinitionCollection(): ?ContentTypeDefinitionCollection
    {
        return $this->contentTypeDefinitionCollection;
    }

    public function getSqlColumnDefinitionCollection(): SqlColumnDefinitionCollection
    {
        return $this->sqlColumnDefinitionCollection;
    }

    public function getTcaFieldDefinitionCollection(): TcaFieldDefinitionCollection
    {
        return $this->tcaFieldDefinitionCollection;
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

    public function withCapability(TableDefinitionCapability $capability): TableDefinition
    {
        $clone = clone $this;
        $clone->capability = $capability;
        return $clone;
    }

    public function withContentType(ContentType $contentType): TableDefinition
    {
        $clone = clone $this;
        $clone->contentType = $contentType;
        return $clone;
    }

    public function withTypeDefinitionCollection(ContentTypeDefinitionCollection $typeDefinitionCollection): TableDefinition
    {
        $clone = clone $this;
        $clone->contentTypeDefinitionCollection = $typeDefinitionCollection;
        return $clone;
    }

    public function withSqlDefinition(SqlColumnDefinitionCollection $sqlDefinition): TableDefinition
    {
        $clone = clone $this;
        $clone->sqlColumnDefinitionCollection = $sqlDefinition;
        return $clone;
    }

    public function withTcaColumnsDefinition(TcaFieldDefinitionCollection $tcaColumnsDefinition): TableDefinition
    {
        $clone = clone $this;
        $clone->tcaFieldDefinitionCollection = $tcaColumnsDefinition;
        return $clone;
    }

    public function withPaletteDefinitionCollection(PaletteDefinitionCollection $paletteDefinitionCollection): TableDefinition
    {
        $clone = clone $this;
        $clone->paletteDefinitionCollection = $paletteDefinitionCollection;
        return $clone;
    }
}
