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

use TYPO3\CMS\ContentBlocks\Utility\UniqueNameUtility;

class TypeDefinition
{
    private string $identifier = '';
    private string $table = '';
    private string $typeField = '';
    private string $type = '';
    private string $label = '';
    private string $icon = '';
    private string $iconProviderClassName = '';
    /** @var string[] */
    private array $columns = [];
    private string $vendor = '';
    private string $package = '';

    final public function __construct()
    {
    }

    public static function createFromArray(array $array, string $table): static
    {
        if (!isset($array['identifier']) || $array['identifier'] === '') {
            throw new \InvalidArgumentException('Type identifier must not be empty.', 1629292395);
        }

        if (!isset($array['typeField']) || $array['typeField'] === '') {
            throw new \InvalidArgumentException('Type field must not be empty.', 1668856783);
        }

        if ($table === '') {
            throw new \InvalidArgumentException('Type table must not be empty.', 1668858103);
        }

        $self = new static();
        return $self
            ->withTable($table)
            ->withIdentifier($array['identifier'])
            ->withTypeField($array['typeField'])
            ->withLabel($array['label'] ?? '')
            ->withIcon($array['icon'] ?? '')
            ->withColumns($array['columns'] ?? [])
            ->withIconProviderClassName($array['iconProvider'] ?? '')
            ->withVendor($array['vendor'] ?? '')
            ->withPackage($array['package'] ?? '')
            ->withType(UniqueNameUtility::composerNameToTypeIdentifier($array['composerName'] ?? ''));
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function getTypeField(): string
    {
        return $this->typeField;
    }

    public function getType(): string
    {
        return $this->type;
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

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function getIconProviderClassName(): string
    {
        return $this->iconProviderClassName;
    }

    public function getColumns(): array
    {
        return $this->columns;
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

    public function withTypeField(string $typeField): static
    {
        $clone = clone $this;
        $clone->typeField = $typeField;
        return $clone;
    }

    public function withLabel(string $label): static
    {
        $clone = clone $this;
        $clone->label = $label;
        return $clone;
    }

    public function withIcon(string $icon): static
    {
        $clone = clone $this;
        $clone->icon = $icon;
        return $clone;
    }

    public function withIconProviderClassName(string $iconProvider): static
    {
        $clone = clone $this;
        $clone->iconProviderClassName = $iconProvider;
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

    public function withVendor(string $vendor): self
    {
        $clone = clone $this;
        $clone->vendor = $vendor;
        return $clone;
    }

    public function withPackage(string $package): self
    {
        $clone = clone $this;
        $clone->package = $package;
        return $clone;
    }

    public function withType(string $type): self
    {
        $clone = clone $this;
        $clone->type = $type;
        return $clone;
    }
}
