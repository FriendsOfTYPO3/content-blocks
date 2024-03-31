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

namespace TYPO3\CMS\ContentBlocks\DataProcessing;

/**
 * @internal Not part of TYPO3's public API.
 */
class ContentObjectProcessorSession
{
    /**
     * @var array<string, RenderedGridItem>
     */
    protected array $renderedGrids = [];

    public function addRenderedGrid(ContentBlockData $contentBlockData, RenderedGridItem $renderedGridItem): void
    {
        $identifier = $this->createIdentifier($contentBlockData);
        $this->renderedGrids[$identifier] = $renderedGridItem;
    }

    public function hasRenderedGrid(ContentBlockData $contentBlockData): bool
    {
        $identifier = $this->createIdentifier($contentBlockData);
        return array_key_exists($identifier, $this->renderedGrids);
    }

    public function setRenderedGrid(ContentBlockData $contentBlockData, RenderedGridItem $renderedGridItem): void
    {
        $identifier = $this->createIdentifier($contentBlockData);
        $currentRenderedGridItem = $this->renderedGrids[$identifier];
        $currentRenderedGridItem->data = $contentBlockData;
        $currentRenderedGridItem->content = $renderedGridItem->content;
    }

    public function getRenderedGrid(ContentBlockData $contentBlockData): RenderedGridItem
    {
        $identifier = $this->createIdentifier($contentBlockData);
        return $this->renderedGrids[$identifier];
    }

    private function createIdentifier(ContentBlockData $contentBlockData): string
    {
        $identifier = $contentBlockData->tableName . $contentBlockData->uid;
        return $identifier;
    }
}
