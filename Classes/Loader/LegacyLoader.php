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

use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider;
use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class LegacyLoader implements LoaderInterface
{
    protected ?TableDefinitionCollection $tableDefinitionCollection = null;

    public function load(): TableDefinitionCollection
    {
        if ($this->tableDefinitionCollection instanceof TableDefinitionCollection) {
            return $this->tableDefinitionCollection;
        }
        GeneralUtility::mkdir_deep(ContentBlockPathUtility::getAbsoluteContentBlockLegacyPath());
        $result = [];
        $cbFinder = new Finder();
        $cbFinder->directories()->depth(0)->in(ContentBlockPathUtility::getAbsoluteContentBlockLegacyPath());

        foreach ($cbFinder as $splPath) {
            if (!is_readable($splPath->getPathname() . '/composer.json')) {
                throw new \RuntimeException('Cannot read or find composer.json file in "' . $splPath->getPathname() . '"' . '/composer.json');
            }
            $composerJson = json_decode(file_get_contents($splPath->getPathname() . '/composer.json'), true);
            if (($composerJson['type'] ?? '') !== 'typo3-contentblock') {
                continue;
            }
            $package = explode('/', $composerJson['name'])[1];
            $result[] = $this->findByPackageName($package);
        }

        $tableDefinitionCollection = TableDefinitionCollection::createFromArray($result);
        $this->tableDefinitionCollection = $tableDefinitionCollection;
        return $this->tableDefinitionCollection;
    }

    protected function findByPackageName(string $package): array
    {
        $packagePath = ContentBlockPathUtility::getAbsolutePackagePath($package);
        if (!file_exists($packagePath)) {
            throw new \RuntimeException('Content block "' . $package . '" could not be found in "' . $packagePath . '".');
        }
        // @todo Validator check if the content block can be processed
        $cbConf = [];
        $cbConf['composerJson'] = json_decode(
            file_get_contents($packagePath . '/' . 'composer.json'),
            true
        );
        $cbConf['yaml'] = Yaml::parseFile(ContentBlockPathUtility::getAbsoluteContentBlocksPrivatePath($package) . '/' . 'EditorInterface.yaml');

        $iconPath = null;
        $iconProviderClass = null;
        foreach (['svg', 'png', 'gif'] as $fileExtension) {
            $iconName = 'ContentBlockIcon.' . $fileExtension;
            $checkIconPath = ContentBlockPathUtility::getAbsoluteContentBlocksPublicPath($package) . '/' . $iconName;
            if (is_readable($checkIconPath)) {
                $iconPath = ContentBlockPathUtility::getRelativeContentBlocksPublicPath($package) . '/' . $iconName;
                $iconProviderClass = $fileExtension === 'svg' ? SvgIconProvider::class : BitmapIconProvider::class;
                break;
            }
        }
        if ($iconPath === null) {
            throw new \RuntimeException('No icon could be found for content block "' . $package . '" in path "' . $packagePath . '".');
        }

        $cbConf['icon'] = $iconPath;
        $cbConf['iconProvider'] = $iconProviderClass;
        return $cbConf;
    }
}
