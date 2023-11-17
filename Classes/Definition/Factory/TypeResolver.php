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

use TYPO3\CMS\ContentBlocks\FieldConfiguration\FieldType;

/**
 * @internal Not part of TYPO3's public API.
 */
class TypeResolver
{
    public static function resolve(string $field, string $table): FieldType
    {
        $tca = $GLOBALS['TCA'][$table]['columns'][$field] ?? [];
        if ($tca === [] || !isset($tca['config']['type'])) {
            throw new \InvalidArgumentException('Tried to resolve type of non-existing field "' . $field . '" of table "' . $table . '".', 1680110446);
        }
        $tcaType = $tca['config']['type'];
        foreach (FieldType::cases() as $enum) {
            if ($enum->getTcaType() === $tcaType) {
                return $enum;
            }
        }
        throw new \InvalidArgumentException('Field type "' . $tcaType . '" is either not implemented or cannot be shared in Content Blocks.', 1680110918);
    }
}
