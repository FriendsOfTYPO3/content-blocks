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

namespace TYPO3\CMS\ContentBlocks\Registry;

use TYPO3\CMS\ContentBlocks\Loader\ParsedContentBlock;
use TYPO3\CMS\Core\SingletonInterface;

class ContentBlockRegistry implements SingletonInterface
{
    /**
     * @var ParsedContentBlock[]
     */
    protected array $contentBlocks = [];

    public function addContentBlock(ParsedContentBlock $contentBlock): void
    {
        $this->contentBlocks[$contentBlock->getName()] = $contentBlock;
    }

    public function hasContentBlock(string $name): bool
    {
        return array_key_exists($name, $this->contentBlocks);
    }

    public function getContentBlock(string $name): ParsedContentBlock
    {
        if (!$this->hasContentBlock($name)) {
            throw new \OutOfBoundsException('Content block with the name "' . $name . '" is not registered.', 1678478902);
        }
        return $this->contentBlocks[$name];
    }

    public function getContentBlockPath(string $name): string
    {
        return $this->getContentBlock($name)->getPath();
    }
}
