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

namespace TYPO3\CMS\ContentBlocks\Loader;

use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentTypeIcon;
use TYPO3\CMS\ContentBlocks\Service\ContentTypeIconResolver;

/**
 * @internal Not part of TYPO3's public API.
 */
class IconProcessor
{
    /** @var callable[] */
    protected array $instructions = [];

    public function process(): void
    {
        while ($instruction = array_shift($this->instructions)) {
            $instruction();
        }
    }

    public function addInstruction(
        ContentTypeIcon $contentTypeIcon,
        string $name,
        string $absolutePath,
        string $extPath,
        string $identifier,
        ContentType $contentType,
        string $table,
        int|string $typeName,
        string $suffix = '',
    ): void {
        $instruction = function () use (
            $contentTypeIcon,
            $name,
            $absolutePath,
            $extPath,
            $identifier,
            $contentType,
            $table,
            $typeName,
            $suffix,
        ) {
            $resultIcon = ContentTypeIconResolver::resolve(
                $name,
                $absolutePath,
                $extPath,
                $identifier,
                $contentType,
                $table,
                $typeName,
                $suffix,
            );
            $contentTypeIcon->iconIdentifier = $resultIcon->iconIdentifier;
            $contentTypeIcon->iconPath = $resultIcon->iconPath;
            $contentTypeIcon->iconProvider = $resultIcon->iconProvider;
        };
        $this->instructions[] = $instruction;
    }
}
