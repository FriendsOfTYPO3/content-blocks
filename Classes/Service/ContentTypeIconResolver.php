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

namespace TYPO3\CMS\ContentBlocks\Service;

use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentTypeIcon;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider;
use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @internal Not part of TYPO3's public API.
 */
class ContentTypeIconResolver
{
    public static function resolve(
        string $name,
        string $absolutePath,
        string $extPath,
        string $identifier,
        ContentType $contentType,
        string $table,
        int|string $typeName,
        string $suffix = '',
    ): ContentTypeIcon {
        foreach (['svg', 'png', 'gif'] as $fileExtension) {
            $iconPathWithoutFileExtension = ContentBlockPathUtility::getPublicFolder() . '/' . $identifier;
            $relativeIconPath = $iconPathWithoutFileExtension . '.' . $fileExtension;
            $checkIconPath = $absolutePath . '/' . $relativeIconPath;
            if (!file_exists($checkIconPath)) {
                continue;
            }
            $prefixPath = match (Environment::isComposerMode()) {
                true => ContentBlockPathUtility::getSymlinkedAssetsPath($name),
                false => $extPath . '/' . ContentBlockPathUtility::getPublicFolder(),
            };
            $iconNameWithoutFileExtension = $identifier;
            $contentTypeIcon = new ContentTypeIcon();
            $icon = $prefixPath . '/' . $iconNameWithoutFileExtension . '.' . $fileExtension;
            $iconProviderClass = $fileExtension === 'svg' ? SvgIconProvider::class : BitmapIconProvider::class;
            $contentTypeIcon->iconPath = $icon;
            $contentTypeIcon->iconProvider = $iconProviderClass;
            $contentTypeIcon->iconIdentifier = self::buildTypeIconIdentifier(
                $table,
                $typeName,
                $contentTypeIcon->iconPath,
                $suffix
            );
            return $contentTypeIcon;
        }
        $contentTypeIcon = new ContentTypeIcon();
        $contentTypeIcon->iconPath = self::getDefaultContentTypeIcon($contentType);
        $contentTypeIcon->iconProvider = SvgIconProvider::class;
        $contentTypeIcon->iconIdentifier = self::buildTypeIconIdentifier(
            $table,
            $typeName,
            $contentTypeIcon->iconPath,
            $suffix
        );
        return $contentTypeIcon;
    }

    public static function getDefaultContentTypeIcon(ContentType $contentType): string
    {
        $iconPath = match ($contentType) {
            ContentType::CONTENT_ELEMENT => 'EXT:content_blocks/Resources/Public/Icons/DefaultContentElementIcon.svg',
            ContentType::PAGE_TYPE => 'EXT:content_blocks/Resources/Public/Icons/DefaultPageTypeIcon.svg',
            ContentType::RECORD_TYPE => 'EXT:content_blocks/Resources/Public/Icons/DefaultRecordTypeIcon.svg',
        };
        return $iconPath;
    }

    /**
     * We add a part of the md5 hash here in order to mitigate browser caching issues when changing the Content Block
     * Icon. Otherwise, the icon identifier would always be the same and stored in the local storage.
     */
    protected static function buildTypeIconIdentifier(
        string $table,
        int|string $typeName,
        string $iconPath,
        string $suffix = ''
    ): string {
        $typeIconIdentifier = $table . '-' . $typeName . $suffix;
        $absolutePath = GeneralUtility::getFileAbsFileName($iconPath);
        if ($absolutePath !== '') {
            $contents = @file_get_contents($absolutePath);
            if ($contents === false) {
                throw new \RuntimeException(
                    'Icon in path ' . $iconPath . ' is not available.',
                    1711970286
                );
            }
            $hash = md5($contents);
            $hasSubString = substr($hash, 0, 7);
            $typeIconIdentifier .= '-' . $hasSubString;
        }
        return $typeIconIdentifier;
    }
}
