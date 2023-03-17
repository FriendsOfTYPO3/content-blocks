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
class ContentBlockPathUtility
{
    public static function getRelativeContentBlockPath(string $extensionKey, string $contentBlockPackageName): string
    {
        return 'EXT:' . $extensionKey . '/' . self::getContentBlocksSubDirectory() . $contentBlockPackageName . '/';
    }

    public static function getPathToEditorConfig(): string
    {
        return 'EditorInterface.yaml';
    }

    public static function getPathToBackendPreviewTemplate(): string
    {
        return self::getPrivatePathSegment() . 'EditorPreview.html';
    }

    public static function getPathToFrontendTemplate(): string
    {
        return self::getPrivatePathSegment() . 'Frontend.html';
    }

    public static function getPathToLanguageFolder(): string
    {
        return self::getPrivatePathSegment() . 'Language/';
    }

    public static function getPathToDefaultLanguageFile(): string
    {
        return self::getPathToLanguageFolder() . 'Labels.xlf';
    }

    public static function getPathToIcon(): string
    {
        return self::getPublicPathSegment() . 'ContentBlockIcon.svg';
    }

    public static function getContentBlocksSubDirectory(): string
    {
        return 'ContentBlocks/';
    }

    public static function getPublicPathSegment(): string
    {
        return 'Assets/';
    }

    public static function getPrivatePathSegment(): string
    {
        return 'Source/';
    }

    public static function getSymlinkedAssetsPath(string $name): string
    {
        return '_assets/cb/' . $name . '/';
    }
}
