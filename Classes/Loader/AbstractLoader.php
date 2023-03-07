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

namespace TYPO3\CMS\ContentBlocks\Loader;

use Symfony\Component\Yaml\Yaml;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider;
use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;

/**
 * @internal Not part of TYPO3's public API.
 */
class AbstractLoader
{
    protected function loadPackageConfiguration(
        string $name,
        string $packagePath = '',
        string $pathInExt = '',
        array $yaml = []
    ): ParsedContentBlock
    {
        if (!file_exists($packagePath)) {
            throw new \RuntimeException('Content block "' . $name . '" could not be found in "' . $packagePath . '".', 1674225340);
        }

        $iconPath = null;
        $iconProviderClass = null;

        foreach (['svg', 'png', 'gif'] as $fileExtension) {
            $iconName = 'ContentBlockIcon.' . $fileExtension;
            $checkIconPath = $pathInExt . 'Resources/Public/' . $iconName;
            if (is_readable($checkIconPath)) {
                $iconPath = $pathInExt . 'Resources/Public/' . $iconName;
                $iconProviderClass = $fileExtension === 'svg' ? SvgIconProvider::class : BitmapIconProvider::class;
                break;
            }
        }
        if ($iconPath === null) {
            $iconPath = 'EXT:content_blocks/Resources/Public/Icons/ContentBlockIcon.svg';
            $iconProviderClass = SvgIconProvider::class;
        }

        return new ParsedContentBlock(
            name: $name,
            yaml: $yaml,
            icon: $iconPath,
            iconProvider: $iconProviderClass,
            packagePath: $pathInExt
        );
    }
}
