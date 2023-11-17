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

namespace TYPO3\CMS\ContentBlocks\Definition\Factory;

/**
 * @internal Not part of TYPO3's public API.
 */
final class LanguagePath
{
    public function __construct(
        private readonly string $basePath,
        private array $path = [],
    ) {}

    public function getCurrentPath(): string
    {
        return $this->basePath . ':' . $this->getPathWithoutBase();
    }

    public function getPathWithoutBase(): string
    {
        return implode('.', $this->path);
    }

    public function addPathSegment(string $segment): void
    {
        $this->path[] = $segment;
    }

    public function popSegment(): void
    {
        array_pop($this->path);
    }
}
