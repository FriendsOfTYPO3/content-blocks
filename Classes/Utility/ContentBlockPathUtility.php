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

use TYPO3\CMS\Core\Core\Environment;

/**
 * @todo paths for composer packages
 */
class ContentBlockPathUtility
{
    public static function getAbsoluteContentBlockLegacyPath(): string
    {
        return Environment::getLegacyConfigPath() . '/content-blocks';
    }

    /**
     * @todo make base path for ContentBlocks configurable and
     * @todo deliver it due to the configuration
     */
    public static function getRelativeContentBlockLegacyPath(): string
    {
        return str_replace(Environment::getPublicPath() . '/', '', self::getAbsoluteContentBlockLegacyPath());
    }

    public static function getAbsolutePackagePath(string $package): string
    {
        return self::getAbsoluteContentBlockLegacyPath() . '/' . $package;
    }

    public static function getRelativePackagePath(string $package): string
    {
        return self::getRelativeContentBlockLegacyPath() . '/' . $package;
    }

    public static function getAbsoluteContentBlocksPrivatePath(string $package): string
    {
        return self::getAbsolutePackagePath($package) . '/Resources/Private';
    }

    /**
     * Since there are dicussions of making/using 'src' or 'Resources/Private',
     * or if it should be configurable, this could be a configurable constant.
     */
    public static function getRelativeContentBlocksPrivatePath(string $package): string
    {
        return self::getRelativePackagePath($package) . '/Resources/Private';
    }

    public static function getAbsoluteContentBlocksPublicPath(string $package): string
    {
        return self::getAbsolutePackagePath($package) . '/Resources/Public';
    }

    /**
     * Since there are dicussions of making/using 'dist' or 'Resources/Public',
     * or if it should be configurable, this could be a configurable constant.
     */
    public static function getRelativeContentBlocksPublicPath(string $package): string
    {
        return self::getRelativePackagePath($package) . '/Resources/Public';
    }
}
