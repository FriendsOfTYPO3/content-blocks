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

use TYPO3\CMS\ContentBlocks\Loader\LoadedContentBlock;

/**
 * @internal Not part of TYPO3's public API.
 */
class UniqueIdentifierCreator
{
    public static function createContentTypeIdentifier(LoadedContentBlock $contentBlock): string
    {
        $contentTypeIdentifier = self::createFullIdentifier($contentBlock);
        return $contentTypeIdentifier;
    }

    public static function prefixIdentifier(LoadedContentBlock $contentBlock, PrefixType $prefixType, string $identifier): string
    {
        $prefix = self::createCombinedIdentifier($contentBlock, $prefixType);
        $fieldIdentifier = $prefix . '_' . $identifier;
        return $fieldIdentifier;
    }

    protected static function createCombinedIdentifier(LoadedContentBlock $contentBlock, PrefixType $prefixType): string
    {
        $contentTypeIdentifier = match ($prefixType) {
            PrefixType::FULL => self::createFullIdentifier($contentBlock),
            PrefixType::VENDOR => self::createVendorIdentifier($contentBlock),
        };
        return $contentTypeIdentifier;
    }

    protected static function createFullIdentifier(LoadedContentBlock $contentBlock): string
    {
        if (!str_contains($contentBlock->getName(), '/')) {
            throw new \InvalidArgumentException(
                'Failed to create content type identifier from name "' . $contentBlock->getName() . '". Missing "/".',
                1699554680
            );
        }
        $parts = explode('/', $contentBlock->getName());
        $contentTypeIdentifier = self::removeDashes($parts[0]) . '_' . self::removeDashes($parts[1]);
        return $contentTypeIdentifier;
    }

    protected static function createVendorIdentifier(LoadedContentBlock $contentBlock): string
    {
        $contentTypeIdentifier = self::removeDashes($contentBlock->getVendor());
        return $contentTypeIdentifier;
    }

    protected static function removeDashes(string $string): string
    {
        return str_replace('-', '', $string);
    }
}
