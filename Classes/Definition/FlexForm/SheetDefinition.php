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

namespace TYPO3\CMS\ContentBlocks\Definition\FlexForm;

use TYPO3\CMS\ContentBlocks\Definition\TcaFieldDefinition;

/**
 * @internal Not part of TYPO3's public API.
 */
final class SheetDefinition implements \IteratorAggregate
{
    private string $identifier = 'sDEF';
    private string $languagePathLabel = '';
    private string $languagePathDescription = '';
    private string $languagePathLinkTitle = '';
    private string $label = '';
    private string $description = '';
    private string $linkTitle = '';

    /**
     * @var array<TcaFieldDefinition|SectionDefinition>
     */
    private array $fields = [];

    /**
     * @return \Iterator<TcaFieldDefinition|SectionDefinition>
     */
    public function getIterator(): \Iterator
    {
        return new \ArrayIterator($this->fields);
    }

    public function addFieldOrSection(TcaFieldDefinition|SectionDefinition $fieldOrSection): void
    {
        $this->fields[] = $fieldOrSection;
    }

    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getLanguagePathLabel(): string
    {
        return $this->languagePathLabel;
    }

    public function setLanguagePathLabel(string $languagePathLabel): void
    {
        $this->languagePathLabel = $languagePathLabel;
    }

    public function getLanguagePathDescription(): string
    {
        return $this->languagePathDescription;
    }

    public function setLanguagePathDescription(string $languagePathDescription): void
    {
        $this->languagePathDescription = $languagePathDescription;
    }

    public function getLanguagePathLinkTitle(): string
    {
        return $this->languagePathLinkTitle;
    }

    public function setLanguagePathLinkTitle(string $languagePathLinkTitle): void
    {
        $this->languagePathLinkTitle = $languagePathLinkTitle;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function hasLabel(): bool
    {
        return $this->label !== '';
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function hasDescription(): bool
    {
        return $this->description !== '';
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getLinkTitle(): string
    {
        return $this->linkTitle;
    }

    public function hasLinkTitle(): bool
    {
        return $this->linkTitle !== '';
    }

    public function setLinkTitle(string $linkTitle): void
    {
        $this->linkTitle = $linkTitle;
    }
}
