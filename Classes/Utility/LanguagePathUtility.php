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

class LanguagePathUtility
{
    protected static function getLanguageFolderPath(string $package, string $vendor): string
    {
        return ContentBlockPathUtility::getRelativeContentBlocksPrivatePath($package, $vendor). '/Language';
    }

    public static function getPartialLanguageIdentifierPath(string $package, string $vendor, string $identifier): string
    {
        return self::getLanguageFolderPath($package, $vendor) . '/Labels.xlf:' . $identifier;
    }

    public static function getFullLanguageIdentifierPath(string $package, string $vendor, string $identifier, string $suffix): string
    {
        return self::getPartialLanguageIdentifierPath($package, $vendor, $identifier) . '.' . $suffix;
    }
}
