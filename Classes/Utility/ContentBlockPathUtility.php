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

class ContentBlockPathUtility
{
    public static function getAbsoluteContentBlockLegacyPath(): string
    {
        return Environment::getLegacyConfigPath() . '/content-blocks';
    }

    public static function getAbsolutePackagePath(string $package, string $vendor = ''): string
    {
        if (Environment::isComposerMode()) {
            if ($vendor === '') {
                throw new \InvalidArgumentException('`$vendor` must be set to retrieve absolute path of package.', 1674170723);
            }
            return realpath(InstalledVersions::getInstallPath($vendor . '/' . $package));
        } else {
            return self::getAbsoluteContentBlockLegacyPath() . '/' . $package;
        }
    }

    public static function getAbsoluteContentBlocksPrivatePath(string $package, string $vendor = ''): string
    {
        return self::getAbsolutePackagePath($package, $vendor) . '/Resources/Private';
    }

    public static function getRelativeContentBlocksPrivatePath(string $package, string $vendor = ''): string
    {
        return self::getRelativePackagePath($package, $vendor) . '/Resources/Private';
    }

    public static function getAbsoluteContentBlocksPublicPath(string $package, string $vendor = ''): string
    {
        return self::getAbsolutePackagePath($package, $vendor) . '/Resources/Public';
    }

    public static function getRelativeContentBlocksPublicPath(string $package, string $vendor = ''): string
    {
        return self::getRelativePackagePath($package, $vendor) . '/Resources/Public';
    }

    protected static function getRelativePackagePath(string $package, string $vendor): string
    {
        return 'CB:' . $vendor . '/' . $package;
    }
}
