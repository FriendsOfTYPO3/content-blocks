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
class TypeDefinition
{
    protected string $identifier = '';
    protected string $table = '';
    protected string|int $typeName = '';
    protected string $label = '';
    /** @var string[] */
    protected array $columns = [];
    /** @var string[] */
    protected array $showItems = [];
    /** @var array<TcaFieldDefinition> */
    protected array $overrideColumns = [];
    protected string $vendor = '';
    protected string $package = '';
    protected int $priority = 0;

    final public function __construct()
    {
    }

    public static function createFromArray(array $array, string $table): static
    {
        if (!isset($array['identifier']) || $array['identifier'] === '') {
            throw new \InvalidArgumentException('Type identifier must not be empty.', 1629292395);
        }

        if ($table === '') {
            throw new \InvalidArgumentException('Type table must not be empty.', 1668858103);
        }

        $self = new static();
        return $self
            ->withTable($table)
            ->withIdentifier($array['identifier'])
            ->withTypeName($array['typeName'])
            ->withLabel($array['label'] ?? '')
            ->withColumns($array['columns'] ?? [])
            ->withShowItems($array['showItems'] ?? [])
            ->withOverrideColumns($array['overrideColumns'] ?? [])
            ->withVendor($array['vendor'] ?? '')
            ->withPackage($array['package'] ?? '')
            ->withPriority($array['priority'] ?? '');
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function getTypeName(): string|int
    {
        return $this->typeName;
    }

    public function getTypeIconIdentifier(): string
    {
        return $this->typeName . '-icon';
    }

    public function getVendor(): string
    {
        return $this->vendor;
    }

    public function getPackage(): string
    {
        return $this->package;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return string[] $columns
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @return string[] $columns
     */
    public function getShowItems(): array
    {
        return $this->showItems;
    }

    public function hasColumn(string $column): bool
    {
        return in_array($column, $this->columns, true);
    }

    public function getName(): string
    {
        return $this->vendor . '/' . $this->package;
    }

    public function getOverrideColumns(): array
    {
        return $this->overrideColumns;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function withIdentifier(string $identifier): static
    {
        $clone = clone $this;
        $clone->identifier = $identifier;
        return $clone;
    }

    public function withTable(string $table): static
    {
        $clone = clone $this;
        $clone->table = $table;
        return $clone;
    }

    public function withLabel(string $label): static
    {
        $clone = clone $this;
        $clone->label = $label;
        return $clone;
    }

    /**
     * @param string[] $columns
     */
    public function withColumns(array $columns): static
    {
        $clone = clone $this;
        $clone->columns = $columns;
        return $clone;
    }

    /**
     * @param string[] $showItems
     */
    public function withShowItems(array $showItems): static
    {
        $clone = clone $this;
        $clone->showItems = $showItems;
        return $clone;
    }

    /**
     * @param array<TcaFieldDefinition> $overrideColumns
     */
    public function withOverrideColumns(array $overrideColumns): static
    {
        $clone = clone $this;
        $clone->overrideColumns = $overrideColumns;
        return $clone;
    }

    public function withVendor(string $vendor): static
    {
        $clone = clone $this;
        $clone->vendor = $vendor;
        return $clone;
    }

    public function withPackage(string $package): static
    {
        $clone = clone $this;
        $clone->package = $package;
        return $clone;
    }

    public function withTypeName(string|int $type): static
    {
        $clone = clone $this;
        $clone->typeName = $type;
        return $clone;
    }

    public function withPriority(int $priority): static
    {
        $clone = clone $this;
        $clone->priority = $priority;
        return $clone;
    }
}
