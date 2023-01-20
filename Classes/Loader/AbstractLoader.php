<?php

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
        $packagePath = ContentBlockPathUtility::getAbsolutePackagePath($package, $vendor);
        if (!file_exists($packagePath)) {
            throw new \RuntimeException('Content block "' . $package . '" could not be found in "' . $packagePath . '".');
        }
        $packageConfiguration = [];
        $packageConfiguration['composerJson'] = json_decode(
            file_get_contents($packagePath . '/' . 'composer.json'),
            true
        );
        $packageConfiguration['yaml'] = Yaml::parseFile(ContentBlockPathUtility::getAbsoluteContentBlocksPrivatePath($package, $vendor) . '/' . 'EditorInterface.yaml');

        $iconPath = null;
        $iconProviderClass = null;
        foreach (['svg', 'png', 'gif'] as $fileExtension) {
            $iconName = 'ContentBlockIcon.' . $fileExtension;
            $checkIconPath = ContentBlockPathUtility::getAbsoluteContentBlocksPublicPath($package, $vendor) . '/' . $iconName;
            if (is_readable($checkIconPath)) {
                $iconPath = ContentBlockPathUtility::getRelativeContentBlocksPublicPath($package, $vendor) . '/' . $iconName;
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
