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

use TYPO3\CMS\ContentBlocks\Definition\TCA\LinebreakDefinition;

/**
 * @internal Not part of TYPO3's public API.
 */
final class PaletteDefinition
{
    private string $identifier = '';
    private string $contentBlockName = '';
    private string $label = '';
    private string $description = '';
    private string $languagePathLabel = '';
    private string $languagePathDescription = '';
    /** @var array<string|LinebreakDefinition> */
    private array $items = [];

    public static function createFromArray(array $array): PaletteDefinition
    {
        if (($array['identifier'] ?? '') === '') {
            throw new \InvalidArgumentException('Palette identifier must not be empty.', 1629293639);
        }
        $self = new self();
        $self->identifier = (string)$array['identifier'];
        $self->contentBlockName = (string)($array['contentBlockName'] ?? '');
        $self->label = (string)($array['label'] ?? '');
        $self->description = (string)($array['description'] ?? '');
        $self->languagePathLabel = (string)($array['languagePathLabel'] ?? '');
        $self->languagePathDescription = (string)($array['languagePathDescription'] ?? '');
        $self->items = (array)($array['items'] ?? []);
        return $self;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function hasLabel(): bool
    {
        return $this->label !== '';
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function hasDescription(): bool
    {
        return $this->description !== '';
    }

    /**
     * @return array<string|LinebreakDefinition>
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function getLanguagePathLabel(): string
    {
        return $this->languagePathLabel;
    }

    public function getLanguagePathDescription(): string
    {
        return $this->languagePathDescription;
    }

    public function getContentBlockName(): string
    {
        return $this->contentBlockName;
    }
}
