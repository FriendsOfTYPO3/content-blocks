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

namespace TYPO3\CMS\ContentBlocks\FieldType;

/**
 * Enum of Core field types for special handling.
 *
 * @internal Not part of TYPO3's public API.
 */
enum FieldType: string
{
    case TEXT = 'Text';
    case TEXTAREA = 'Textarea';
    case EMAIL = 'Email';
    case NUMBER = 'Number';
    case LINK = 'Link';
    case COLOR = 'Color';
    case DATETIME = 'DateTime';
    case SLUG = 'Slug';
    case SELECT = 'Select';
    case LANGUAGE = 'Language';
    case CATEGORY = 'Category';
    case RADIO = 'Radio';
    case CHECKBOX = 'Checkbox';
    case COLLECTION = 'Collection';
    case FILE = 'File';
    case FOLDER = 'Folder';
    case JSON = 'Json';
    case RELATION = 'Relation';
    case FLEXFORM = 'FlexForm';
    case PASSWORD = 'Password';
    case PALETTE = 'Palette';
    case LINEBREAK = 'Linebreak';
    case TAB = 'Tab';
    case UUID = 'Uuid';

    public static function isValidFlexFormField(FieldTypeInterface $fieldType): bool
    {
        $fieldTypeEnum = FieldType::tryFrom($fieldType::getName());
        if ($fieldTypeEnum->isStructureField()) {
            return false;
        }
        return $fieldTypeEnum !== self::FLEXFORM;
    }

    public function isStructureField(): bool
    {
        $structureFields = [self::PALETTE, self::LINEBREAK, self::TAB];
        $isStructureField = in_array($this, $structureFields, true);
        return $isStructureField;
    }
}
