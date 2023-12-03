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

/**
 * @internal Not part of TYPO3's public API.
 */
class LocalLangPathUtility
{
    public static function extractKeyFromLLLPath(string $key): string
    {
        if (str_starts_with($key, 'LLL:EXT:')) {
            $parts = explode(':', $key);
            $key = $parts[3];
        }
        return $key;
    }
}
