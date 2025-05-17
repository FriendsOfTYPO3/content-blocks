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
trait ContentTypeDefinition
{
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
}
