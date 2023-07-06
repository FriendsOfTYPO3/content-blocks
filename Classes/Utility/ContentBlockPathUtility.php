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
        return 'EXT:' . $extensionKey . '/' . self::getSubDirectoryPath() . '/' . $contentBlockPackageName;
    }

    public static function getEditorInterfacePath(): string
    {
        return 'EditorInterface.yaml';
    }

    public static function getBackendPreviewPath(): string
    {
        return self::getPrivateFolderPath() . '/EditorPreview.html';
    }

    public static function getFrontendTemplatePath(): string
    {
        return self::getPrivateFolderPath() . '/Frontend.html';
    }

    public static function getLanguageFolderPath(): string
    {
        return self::getPrivateFolderPath() . '/Language';
    }

    public static function getLanguageFilePath(): string
    {
        return self::getLanguageFolderPath() . '/Labels.xlf';
    }

    public static function getIconPath(): string
    {
        return self::getPublicFolderPath() . '/ContentBlockIcon.svg';
    }

    public static function getSubDirectoryPath(): string
    {
        return 'ContentBlocks';
    }

    public static function getPublicFolderPath(): string
    {
        return 'Assets';
    }

    public static function getPrivateFolderPath(): string
    {
        return 'Source';
    }

    public static function getSymlinkedAssetsPath(string $name): string
    {
        return '_assets/cb/' . $name;
    }

    public static function getRelativeBasicsPath(): string
    {
        return 'Configuration/Yaml/ContentBlockBasics.yaml';
    }
}
