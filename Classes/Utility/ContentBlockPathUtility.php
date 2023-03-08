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

namespace TYPO3\CMS\ContentBlocks\Utility;

use Composer\InstalledVersions;
use TYPO3\CMS\Core\Core\Environment;

/**
 * @internal Not part of TYPO3's public API.
 */
class ContentBlockPathUtility
{
    public static function getAbsoluteContentBlockLegacyPath(): string
    {
        return Environment::getLegacyConfigPath() . '/content-blocks';
    }

    public static function getAbsoluteContentBlockPath(string $package, string $vendor = ''): string
    {
        if (Environment::isComposerMode()) {
            if ($vendor === '') {
                throw new \InvalidArgumentException('`$vendor` must be set to retrieve absolute path of package in composer-mode.', 1674170723);
            }
            try {
                return realpath(InstalledVersions::getInstallPath($vendor . '/' . $package));
            } catch (\OutOfBoundsException) {
                return '';
            }
        }
        return self::getAbsoluteContentBlockLegacyPath() . '/' . $package;
    }

    public static function getAbsoluteContentBlockPrivatePath(string $package, string $vendor = ''): string
    {
        return self::getAbsoluteContentBlockPath($package, $vendor) . '/Resources/Private';
    }

    public static function getPrefixedContentBlockPrivatePath(string $package, string $vendor = ''): string
    {
        return self::getPrefixedContentBlockPath($package, $vendor) . '/Resources/Private';
    }

    public static function getAbsoluteContentBlockPublicPath(string $package, string $vendor = ''): string
    {
        return self::getAbsoluteContentBlockPath($package, $vendor) . '/Resources/Public';
    }

    public static function getPrefixedContentBlockPublicPath(string $package, string $vendor = ''): string
    {
        return self::getPrefixedContentBlockPath($package, $vendor) . '/Resources/Public';
    }

    protected static function getPrefixedContentBlockPath(string $package, string $vendor): string
    {
        return 'CB:' . $vendor . '/' . $package;
    }

    public static function isContentBlockPath(string $path): bool
    {
        return str_starts_with($path, 'CB:');
    }

    /**
     * Returns something like "EXT:my_ext/ContentBlocks/my_content_block/".
     */
    public static function getRelativeContentBlockPath(string $extKey, string $cbDir): string
    {
        return 'EXT:' . $extKey . '/' . self::getContentBlocksSubDirectory() . $cbDir . '/';
    }

    public static function getPathToEditorConfig(): string
    {
        return self::getPrivatePathSegment() . 'EditorInterface.yaml';
    }

    public static function getContentBlocksSubDirectory(): string
    {
        return 'ContentBlocks/';
    }

    /**
     * There are thoughts to change this to "dist/"
     */
    public static function getPublicPathSegment(): string
    {
        return 'Resources/Public/';
    }

    /**
     * There are thoughts to change this to "src/"
     */
    public static function getPrivatePathSegment(): string
    {
        return 'Resources/Private/';
    }

    public static function getPathToLabels(): string
    {
        return self::getPrivatePathSegment() . 'Language/Labels.xlf';
    }
}
