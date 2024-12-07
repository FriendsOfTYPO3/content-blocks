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

namespace TYPO3\CMS\ContentBlocks\Definition\ContentType;

/**
 * @internal Not part of TYPO3's public API.
 */
enum ContentType
{
    case CONTENT_ELEMENT;
    case PAGE_TYPE;
    case FILE_TYPE;
    case RECORD_TYPE;

    public function getTable(): ?string
    {
        return match ($this) {
            self::CONTENT_ELEMENT => 'tt_content',
            self::PAGE_TYPE => 'pages',
            self::FILE_TYPE => 'sys_file_reference',
            self::RECORD_TYPE => null,
        };
    }

    public function getTypeField(): ?string
    {
        return match ($this) {
            self::CONTENT_ELEMENT => 'CType',
            self::PAGE_TYPE => 'doktype',
            self::FILE_TYPE, self::RECORD_TYPE => null,
        };
    }

    public static function getByTable(string $table): self
    {
        return match ($table) {
            'tt_content' => self::CONTENT_ELEMENT,
            'pages' => self::PAGE_TYPE,
            'sys_file_reference' => self::FILE_TYPE,
            default => self::RECORD_TYPE,
        };
    }

    public function getHumanReadable(): string
    {
        return match ($this) {
            self::CONTENT_ELEMENT => 'Content Element',
            self::PAGE_TYPE => 'Page Type',
            self::FILE_TYPE => 'File Type',
            self::RECORD_TYPE => 'Record Type',
        };
    }

    public function getDefaultGroup(): ?string
    {
        return match ($this) {
            self::CONTENT_ELEMENT, self::PAGE_TYPE => 'default',
            self::FILE_TYPE, self::RECORD_TYPE => null,
        };
    }

    public function getShortName(): string
    {
        return match ($this) {
            self::CONTENT_ELEMENT => 'content-element',
            self::PAGE_TYPE => 'page-type',
            self::FILE_TYPE => 'file-type',
            self::RECORD_TYPE => 'record-type',
        };
    }
}
