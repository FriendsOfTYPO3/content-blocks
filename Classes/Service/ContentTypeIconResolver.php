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

use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentTypeIcon;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider;
use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;

/**
 * @internal Not part of TYPO3's public API.
 */
class ContentTypeIconResolver
{
    public static function resolve(string $name, string $absolutePath, string $extPath, string $identifier): ContentTypeIcon
    {
        foreach (['svg', 'png', 'gif'] as $fileExtension) {
            $iconPathWithoutFileExtension = ContentBlockPathUtility::getPublicFolder() . '/' . $identifier;
            $relativeIconPath = $iconPathWithoutFileExtension . '.' . $fileExtension;
            $checkIconPath = $absolutePath . '/' . $relativeIconPath;
            if (!is_readable($checkIconPath)) {
                continue;
            }
            $prefixPath = match (Environment::isComposerMode()) {
                true => Environment::getPublicPath() . '/' . ContentBlockPathUtility::getSymlinkedAssetsPath($name),
                false => $extPath,
            };
            $iconNameWithoutFileExtension = $identifier;
            $contentTypeIcon = new ContentTypeIcon();
            $icon = $prefixPath . '/' . $iconNameWithoutFileExtension . '.' . $fileExtension;
            $iconProviderClass = $fileExtension === 'svg' ? SvgIconProvider::class : BitmapIconProvider::class;
            $contentTypeIcon->iconPath = $icon;
            $contentTypeIcon->iconProvider = $iconProviderClass;
            return $contentTypeIcon;
        }
        $contentTypeIcon = new ContentTypeIcon();
        $contentTypeIcon->iconPath = 'EXT:content_blocks/Resources/Public/Icons/DefaultIcon.svg';
        $contentTypeIcon->iconProvider = SvgIconProvider::class;
        return $contentTypeIcon;
    }
}
