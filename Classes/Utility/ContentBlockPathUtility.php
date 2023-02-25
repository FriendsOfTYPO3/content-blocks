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

    public static function getAbsoluteContentBlockPath(string $package, string $vendor = ''): string
    {
        if (Environment::isComposerMode()) {
            if ($vendor === '') {
                throw new \InvalidArgumentException('`$vendor` must be set to retrieve absolute path of package.', 1674170723);
            }
            return realpath(InstalledVersions::getInstallPath($vendor . '/' . $package));
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
}
