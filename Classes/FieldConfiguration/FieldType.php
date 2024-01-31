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

namespace TYPO3\CMS\ContentBlocks\FieldConfiguration;

/**
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

    public function getTcaType(): string
    {
        return match ($this) {
            self::CATEGORY => 'category',
            self::CHECKBOX => 'check',
            self::COLLECTION => 'inline',
            self::COLOR => 'color',
            self::DATETIME => 'datetime',
            self::SLUG => 'slug',
            self::EMAIL => 'email',
            self::FILE => 'file',
            self::JSON => 'json',
            self::LANGUAGE => 'language',
            self::LINK => 'link',
            self::NUMBER => 'number',
            self::RADIO => 'radio',
            self::SELECT => 'select',
            self::RELATION => 'group',
            self::FOLDER => 'folder',
            self::TEXT => 'input',
            self::TEXTAREA => 'text',
            self::FLEXFORM => 'flex',
            self::PASSWORD => 'password',
            self::PALETTE, self::LINEBREAK, self::TAB => '',
            self::UUID => 'uuid',
        };
    }

    public function isSearchable(): bool
    {
        return in_array(
            $this,
            [
                self::TEXT,
                self::TEXTAREA,
                self::EMAIL,
                self::COLOR,
                self::LINK,
                self::SLUG,
                self::FLEXFORM,
                self::JSON,
                self::UUID,
            ],
            true
        );
    }

    public function isRenderable(): bool
    {
        return !in_array($this, [self::PALETTE, self::LINEBREAK, self::TAB], true);
    }

    public function isRelation(): bool
    {
        return in_array($this, [self::SELECT, self::COLLECTION, self::RELATION]);
    }

    public function hasItems(): bool
    {
        return in_array($this, [self::SELECT, self::RADIO, self::CHECKBOX]);
    }

    public static function isValidFlexFormField(FieldType $type): bool
    {
        if (!$type->isRenderable()) {
            return false;
        }
        return $type !== self::FLEXFORM;
    }

    public function getFieldConfiguration(array $config): FieldConfigurationInterface
    {
        return match ($this) {
            self::CATEGORY => CategoryFieldConfiguration::createFromArray($config),
            self::CHECKBOX => CheckboxFieldConfiguration::createFromArray($config),
            self::COLLECTION => CollectionFieldConfiguration::createFromArray($config),
            self::COLOR => ColorFieldConfiguration::createFromArray($config),
            self::DATETIME => DateTimeFieldConfiguration::createFromArray($config),
            self::SLUG => SlugFieldConfiguration::createFromArray($config),
            self::EMAIL => EmailFieldConfiguration::createFromArray($config),
            self::FILE => FileFieldConfiguration::createFromArray($config),
            self::JSON => JsonFieldConfiguration::createFromArray($config),
            self::LANGUAGE => LanguageFieldConfiguration::createFromArray($config),
            self::LINK => LinkFieldConfiguration::createFromArray($config),
            self::NUMBER => NumberFieldConfiguration::createFromArray($config),
            self::RADIO => RadioFieldConfiguration::createFromArray($config),
            self::SELECT => SelectFieldConfiguration::createFromArray($config),
            self::RELATION => RelationFieldConfiguration::createFromArray($config),
            self::FOLDER => FolderFieldConfiguration::createFromArray($config),
            self::TEXT => TextFieldConfiguration::createFromArray($config),
            self::TEXTAREA => TextareaFieldConfiguration::createFromArray($config),
            self::FLEXFORM => FlexFormFieldConfiguration::createFromArray($config),
            self::PASSWORD => PasswordFieldConfiguration::createFromArray($config),
            self::PALETTE => PaletteFieldConfiguration::createFromArray($config),
            self::LINEBREAK => LinebreakFieldConfiguration::createFromArray($config),
            self::TAB => TabFieldConfiguration::createFromArray($config),
            self::UUID => UuidFieldConfiguration::createFromArray($config),
        };
    }
}
