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
    public static function createContentTypeIdentifier(string $name): string
    {
        $contentTypeIdentifier = self::createFullIdentifier($name);
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
        $vendorPrefix = self::createVendorIdentifier($contentBlock);
        $contentTypeIdentifier = match ($prefixType) {
            PrefixType::FULL => self::createFullIdentifier($contentBlock->getName(), $vendorPrefix),
            PrefixType::VENDOR => $vendorPrefix,
        };
        return $contentTypeIdentifier;
    }

    protected static function createFullIdentifier(string $name, ?string $vendorPrefix = null): string
    {
        if (!str_contains($name, '/')) {
            throw new \InvalidArgumentException(
                'Failed to create content type identifier from name "' . $name . '". Missing "/".',
                1699554680
            );
        }
        $parts = explode('/', $name);
        $vendorPrefix = $vendorPrefix ?? self::removeDashes($parts[0]);
        $contentTypeIdentifier = $vendorPrefix . '_' . self::removeDashes($parts[1]);
        return $contentTypeIdentifier;
    }

    protected static function createVendorIdentifier(LoadedContentBlock $contentBlock): string
    {
        $vendorPrefix = $contentBlock->getYaml()['vendorPrefix'] ?? $contentBlock->getVendor();
        $contentTypeIdentifier = self::removeDashes($vendorPrefix);
        return $contentTypeIdentifier;
    }

    protected static function removeDashes(string $string): string
    {
        return str_replace('-', '', $string);
    }
}
