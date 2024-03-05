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

/**
 * @internal Not part of TYPO3's public API.
 */
final class FlexFormDefinition implements \IteratorAggregate
{
    private string $contentBlockName = '';
    private string|int $typeName = '';

    /**
     * @return \Iterator<SheetDefinition>
     */
    public function getIterator(): \Iterator
    {
        return new \ArrayIterator($this->sheets);
    }

    /**
     * @var SheetDefinition[]
     */
    private array $sheets = [];

    public function addSheet(SheetDefinition $sheet): void
    {
        $this->sheets[] = $sheet;
    }

    public function getTypeName(): string|int
    {
        return $this->typeName;
    }

    public function setTypeName(string|int $typeName): void
    {
        $this->typeName = $typeName;
    }

    public function hasDefaultSheet(): bool
    {
        return count($this->sheets) === 1;
    }

    public function getContentBlockName(): string
    {
        return $this->contentBlockName;
    }

    public function setContentBlockName(string $contentBlockName): void
    {
        $this->contentBlockName = $contentBlockName;
    }
}
