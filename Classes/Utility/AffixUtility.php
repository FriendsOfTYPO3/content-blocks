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
class AffixUtility
{
    private const COLLECTION_PREFIX = 'cb_collection_';
    private const DB_COLUMN_PREFIX = 'cb_';

    public static function hasCollectionPrefix(string $table): bool
    {
        return str_starts_with($table, self::COLLECTION_PREFIX);
    }

    public static function prefixCollection(string $table): string
    {
        if (self::hasCollectionPrefix($table)) {
            return $table;
        }

        return self::COLLECTION_PREFIX . $table;
    }

    public static function hasDbColumnPrefix(string $column): bool
    {
        return str_starts_with($column, self::DB_COLUMN_PREFIX);
    }

    public static function prefixDbColumn(string $column): string
    {
        if (self::hasDbColumnPrefix($column)) {
            return $column;
        }

        return self::DB_COLUMN_PREFIX . $column;
    }
}
