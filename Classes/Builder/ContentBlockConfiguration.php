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

namespace TYPO3\CMS\ContentBlocks\Builder;

use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;

/**
 * @internal Not part of TYPO3's public API.
 */
final class ContentBlockConfiguration
{
    public function __construct(
        private readonly array $yamlConfig,
        private readonly string $basePath,
        private readonly ContentType $contentType,
    ) {
    }

    public function getYamlConfig(): array
    {
        return $this->yamlConfig;
    }

    public function getVendor(): string
    {
        return explode('/', $this->getContentBlockName())[0];
    }

    public function getName(): string
    {
        return explode('/', $this->getContentBlockName())[1];
    }

    public function getContentBlockName(): string
    {
        return $this->yamlConfig['name'];
    }

    public function getBasePath(): string
    {
        return $this->basePath;
    }

    public function getContentType(): ContentType
    {
        return $this->contentType;
    }
}
