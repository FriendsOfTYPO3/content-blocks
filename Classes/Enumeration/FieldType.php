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

use TYPO3\CMS\ContentBlocks\FieldConfiguration\AbstractFieldConfiguration;
use TYPO3\CMS\ContentBlocks\FieldConfiguration\EmailFieldConfiguration;
use TYPO3\CMS\ContentBlocks\FieldConfiguration\InputFieldConfiguration;
use TYPO3\CMS\ContentBlocks\FieldConfiguration\TextareaFieldConfiguration;

enum FieldType: String
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
    case TEXT = 'Text';
    case TEXTAREA = 'Textarea';
    // For testing
    case IMAGE = 'Image';
    case LINEBREAK = 'linebreak';
    case URL = 'Url';

    /**
     * Checks if this field type is a structure field.
     */
    public function isStructure(): bool
    {
        return match ($this) {
            self::COLLECTION => true,
            default => false,
        };
    }

    public function getTcaType(): string
    {
        return match ($this) {
            self::CATEGORY => 'input',
            self::CHECKBOX => 'check',
            self::COLLECTION => 'inline',
            self::COLOR => 'input',
            self::DATETIME => 'input',
            self::EMAIL => 'input',
            self::FILE => 'inline',
            self::LINK => 'input',
            self::NUMBER => 'input',
            self::RADIO => 'radio',
            self::SELECT => 'select',
            self::REFERENCE => 'input',
            self::TEXT => 'input',
            self::TEXTAREA => 'text',
            // For testing
            self::IMAGE => 'input',
            self::LINEBREAK =>  'input',
            self::URL =>  'input',
            default => '',
        };
    }

    /**
     * TODO: this method moved to FieldTypeConfiguration!!!
     */
    public function getTca(): array
    {
        return match ($this) {
            self::TEXT => [
                'type' => 'text',
            ],
            default => [],
        };
    }

    /**
     * Get SQL Definition
     */
    public function getSql(): string
    {
        return match ($this) {
            default => '',
        };
    }

    /**
     * Get the matching FieldTypeConfiguration
     * TODO: add the missing field types
     */
    public function getFieldTypeConfiguration(array $config): AbstractFieldConfiguration
    {
        return match ($this) {
            // self::CATEGORY => 'input',
            // self::CHECKBOX => 'check',
            self::COLLECTION => new InputFieldConfiguration($config), // For testing
            // self::COLOR => 'input',
            // self::DATETIME => 'input',
            self::EMAIL => new EmailFieldConfiguration($config),
            // self::FILE => 'inline',
            // self::LINK => 'input',
            // self::NUMBER => 'input',
            // self::RADIO => 'radio',
            // self::SELECT => 'select',
            // self::REFERENCE => 'input',
            self::TEXT => new InputFieldConfiguration($config),
            self::TEXTAREA => new TextareaFieldConfiguration($config),
            // For testing
            self::IMAGE => new InputFieldConfiguration($config),
            self::LINEBREAK =>  new InputFieldConfiguration($config),
            self::URL =>  new InputFieldConfiguration($config),
            default => var_dump($config),
        };
    }

    /*
     * TODO:
     * - Add all of the types
     * - helper functions: look at EXT:mask
     */
}
