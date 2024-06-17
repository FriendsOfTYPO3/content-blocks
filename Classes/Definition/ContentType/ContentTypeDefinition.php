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

namespace TYPO3\CMS\ContentBlocks\Definition\ContentType;

use TYPO3\CMS\ContentBlocks\Definition\PaletteDefinition;
use TYPO3\CMS\ContentBlocks\Definition\TCA\TabDefinition;
use TYPO3\CMS\ContentBlocks\Definition\TcaFieldDefinition;

/**
 * @internal Not part of TYPO3's public API.
 */
abstract class ContentTypeDefinition
{
    private string $identifier = '';
    private string $title = '';
    private string $description = '';
    private string $table = '';
    private string|int $typeName = '';
    /** @var string[] */
    private array $columns = [];
    /** @var array<string|PaletteDefinition|TabDefinition> */
    private array $showItems = [];
    /** @var TcaFieldDefinition[] */
    private array $overrideColumns = [];
    private string $vendor = '';
    private string $package = '';
    private int $priority = 0;
    private ContentTypeIcon $typeIcon;
    private string $languagePathTitle;
    private string $languagePathDescription;
    private ?string $group = null;

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function getTypeName(): string|int
    {
        return $this->typeName;
    }

    public function getVendor(): string
    {
        return $this->vendor;
    }

    public function getPackage(): string
    {
        return $this->package;
    }

    /**
     * @return string[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @return array<string|PaletteDefinition|TabDefinition>
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

    /**
     * @return TcaFieldDefinition[]
     */
    public function getOverrideColumns(): array
    {
        return $this->overrideColumns;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function getTypeIcon(): ContentTypeIcon
    {
        return $this->typeIcon;
    }

    public function getLanguagePathTitle(): string
    {
        return $this->languagePathTitle;
    }

    public function getLanguagePathDescription(): string
    {
        return $this->languagePathDescription;
    }

    public function getGroup(): ?string
    {
        return $this->group;
    }

    public function withIdentifier(string $identifier): static
    {
        $clone = clone $this;
        $clone->identifier = $identifier;
        return $clone;
    }

    public function withTitle(string $title): static
    {
        $clone = clone $this;
        $clone->title = $title;
        return $clone;
    }

    public function withDescription(string $description): static
    {
        $clone = clone $this;
        $clone->description = $description;
        return $clone;
    }

    public function withTable(string $table): static
    {
        $clone = clone $this;
        $clone->table = $table;
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
     * @param array<string|PaletteDefinition|TabDefinition> $showItems
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

    public function withTypeIcon(ContentTypeIcon $typeIcon): static
    {
        $clone = clone $this;
        $clone->typeIcon = $typeIcon;
        return $clone;
    }

    public function withLanguagePathTitle(string $languagePathTitle): static
    {
        $clone = clone $this;
        $clone->languagePathTitle = $languagePathTitle;
        return $clone;
    }

    public function withLanguagePathDescription(string $languagePathDescription): static
    {
        $clone = clone $this;
        $clone->languagePathDescription = $languagePathDescription;
        return $clone;
    }

    public function withGroup(?string $group): static
    {
        $clone = clone $this;
        $clone->group = $group;
        return $clone;
    }
}
