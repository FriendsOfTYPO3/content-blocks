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

namespace TYPO3\CMS\ContentBlocks\Definition;

use TYPO3\CMS\ContentBlocks\Enumeration\FieldType;

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

        return match ($tca['config']['type']) {
            'input' => FieldType::TEXT,
            'text' => FieldType::TEXTAREA,
            'color' => FieldType::COLOR,
            'language' => FieldType::LANGUAGE,
            'link' => FieldType::LINK,
            'datetime' => FieldType::DATETIME,
            'email' => FieldType::EMAIL,
            'number' => FieldType::NUMBER,
            'select' => FieldType::SELECT,
            'radio' => FieldType::RADIO,
            'check' => FieldType::CHECKBOX,
            'group' => FieldType::REFERENCE,
            'folder' => FieldType::FOLDER,
            'file' => FieldType::FILE,
            'flex' => FieldType::FLEXFORM,
            'category' => FieldType::CATEGORY,
            default => throw new \InvalidArgumentException('Field type "' . $tca['config']['type'] . '" is either not implemented or cannot be shared in Content Blocks.', 1680110918)
        };
    }
}
