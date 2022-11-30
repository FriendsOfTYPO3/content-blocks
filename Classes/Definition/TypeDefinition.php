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

class TypeDefinition
{
    private string $identifier = '';
    private string $table = '';
    private string $typeField = '';
    private string $label = '';
    private string $icon = '';
    private string $iconProviderClassName = '';

    /**
     * Array of column identifiers.
     *
     * @var string[]
     */
    private array $columns = [];

    public static function createFromArray(array $array, string $table) {
        return self::fromArray($array, $table);
    }

    protected static function fromArray(array $array, string $table)
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
            ->withIconProviderClassName($array['iconProvider'] ?? '');
    }

    public function toArray(): array
    {
        return [
            'identifier' => $this->identifier,
            'typeField' => $this->typeField,
            'label' => $this->label,
            'icon' => $this->icon,
            'showItems' => $this->columns,
            'iconProviderClassName' => $this->iconProviderClassName,
        ];
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

    /**
     * @return string[]
     */
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
}
