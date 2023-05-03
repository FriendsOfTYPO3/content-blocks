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

namespace TYPO3\CMS\ContentBlocks\Enumeration;

use TYPO3\CMS\ContentBlocks\FieldConfiguration\CategoryFieldConfiguration;
use TYPO3\CMS\ContentBlocks\FieldConfiguration\CheckboxFieldConfiguration;
use TYPO3\CMS\ContentBlocks\FieldConfiguration\CollectionFieldConfiguration;
use TYPO3\CMS\ContentBlocks\FieldConfiguration\ColorFieldConfiguration;
use TYPO3\CMS\ContentBlocks\FieldConfiguration\DateTimeFieldConfiguration;
use TYPO3\CMS\ContentBlocks\FieldConfiguration\EmailFieldConfiguration;
use TYPO3\CMS\ContentBlocks\FieldConfiguration\FieldConfigurationInterface;
use TYPO3\CMS\ContentBlocks\FieldConfiguration\FileFieldConfiguration;
use TYPO3\CMS\ContentBlocks\FieldConfiguration\FolderFieldConfiguration;
use TYPO3\CMS\ContentBlocks\FieldConfiguration\LinebreakFieldConfiguration;
use TYPO3\CMS\ContentBlocks\FieldConfiguration\LinkFieldConfiguration;
use TYPO3\CMS\ContentBlocks\FieldConfiguration\NumberFieldConfiguration;
use TYPO3\CMS\ContentBlocks\FieldConfiguration\PaletteFieldConfiguration;
use TYPO3\CMS\ContentBlocks\FieldConfiguration\RadioFieldConfiguration;
use TYPO3\CMS\ContentBlocks\FieldConfiguration\ReferenceFieldConfiguration;
use TYPO3\CMS\ContentBlocks\FieldConfiguration\SelectFieldConfiguration;
use TYPO3\CMS\ContentBlocks\FieldConfiguration\TabFieldConfiguration;
use TYPO3\CMS\ContentBlocks\FieldConfiguration\TextareaFieldConfiguration;
use TYPO3\CMS\ContentBlocks\FieldConfiguration\TextFieldConfiguration;

/**
 * @internal Not part of TYPO3's public API.
 */
enum FieldType: string
{
    case CATEGORY = 'Category';
    case CHECKBOX = 'Checkbox';
    case COLLECTION = 'Collection';
    case COLOR = 'Color';
    case DATETIME = 'DateTime';
    case EMAIL = 'Email';
    case FILE = 'File';
    case LINK = 'Link';
    case NUMBER = 'Number';
    case RADIO = 'Radio';
    case SELECT = 'Select';
    case REFERENCE = 'Reference';
    case FOLDER = 'Folder';
    case TEXT = 'Text';
    case TEXTAREA = 'Textarea';
    case PALETTE = 'Palette';
    case LINEBREAK = 'Linebreak';
    case TAB = 'Tab';

    public function getTcaType(): string
    {
        return match ($this) {
            self::CATEGORY => 'category',
            self::CHECKBOX => 'check',
            self::COLLECTION => 'inline',
            self::COLOR => 'color',
            self::DATETIME => 'datetime',
            self::EMAIL => 'email',
            self::FILE => 'file',
            self::LINK => 'link',
            self::NUMBER => 'number',
            self::RADIO => 'radio',
            self::SELECT => 'select',
            self::REFERENCE => 'group',
            self::FOLDER => 'folder',
            self::TEXT => 'input',
            self::TEXTAREA => 'text',
            self::PALETTE, self::LINEBREAK, self::TAB => '',
        };
    }

    public function isSearchable(): bool
    {
        return in_array($this, [FieldType::TEXT, FieldType::TEXTAREA, FieldType::EMAIL], true);
    }

    public function isRenderable(): bool
    {
        return !in_array($this, [self::PALETTE, self::LINEBREAK, self::TAB], true);
    }

    public function getFieldConfiguration(array $config): FieldConfigurationInterface
    {
        return match ($this) {
            self::CATEGORY => CategoryFieldConfiguration::createFromArray($config),
            self::CHECKBOX => CheckboxFieldConfiguration::createFromArray($config),
            self::COLLECTION => CollectionFieldConfiguration::createFromArray($config),
            self::COLOR => ColorFieldConfiguration::createFromArray($config),
            self::DATETIME => DateTimeFieldConfiguration::createFromArray($config),
            self::EMAIL => EmailFieldConfiguration::createFromArray($config),
            self::FILE => FileFieldConfiguration::createFromArray($config),
            self::LINK => LinkFieldConfiguration::createFromArray($config),
            self::NUMBER => NumberFieldConfiguration::createFromArray($config),
            self::RADIO => RadioFieldConfiguration::createFromArray($config),
            self::SELECT => SelectFieldConfiguration::createFromArray($config),
            self::REFERENCE => ReferenceFieldConfiguration::createFromArray($config),
            self::FOLDER => FolderFieldConfiguration::createFromArray($config),
            self::TEXT => TextFieldConfiguration::createFromArray($config),
            self::TEXTAREA => TextareaFieldConfiguration::createFromArray($config),
            self::PALETTE => new PaletteFieldConfiguration(),
            self::LINEBREAK => new LinebreakFieldConfiguration(),
            self::TAB => new TabFieldConfiguration(),
        };
    }
}
