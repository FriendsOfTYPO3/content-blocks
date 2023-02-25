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

class AbstractLoader
{
    // @todo create object for configuration dto.
    protected function loadPackageConfiguration(string $package, string $vendor): array
    {
        $packagePath = ContentBlockPathUtility::getAbsoluteContentBlockPath($package, $vendor);
        if (!file_exists($packagePath)) {
            throw new \RuntimeException('Content block "' . $package . '" could not be found in "' . $packagePath . '".', 1674225340);
        }
        $packageConfiguration = [];
        $packageConfiguration['composerJson'] = json_decode(
            file_get_contents($packagePath . '/' . 'composer.json'),
            true
        );
        $packageConfiguration['yaml'] = Yaml::parseFile(ContentBlockPathUtility::getAbsoluteContentBlockPrivatePath($package, $vendor) . '/' . 'EditorInterface.yaml');

        $iconPath = null;
        $iconProviderClass = null;
        foreach (['svg', 'png', 'gif'] as $fileExtension) {
            $iconName = 'ContentBlockIcon.' . $fileExtension;
            $checkIconPath = ContentBlockPathUtility::getAbsoluteContentBlockPublicPath($package, $vendor) . '/' . $iconName;
            if (is_readable($checkIconPath)) {
                $iconPath = ContentBlockPathUtility::getPrefixedContentBlockPublicPath($package, $vendor) . '/' . $iconName;
                $iconProviderClass = $fileExtension === 'svg' ? SvgIconProvider::class : BitmapIconProvider::class;
                break;
            }
        }
        if ($iconPath === null) {
            $iconPath = 'EXT:content_block/Resources/Public/Icons/ContentBlockIcon.svg';
        }

        $packageConfiguration['icon'] = $iconPath;
        $packageConfiguration['iconProvider'] = $iconProviderClass;
        return $packageConfiguration;
    }
}
