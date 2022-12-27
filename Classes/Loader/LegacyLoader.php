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
use TYPO3\CMS\ContentBlocks\Service\ConfigurationService;
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
        GeneralUtility::mkdir_deep(ConfigurationService::getContentBlockLegacyPath());
        $result = [];
        $cbFinder = new Finder();
        $cbFinder->directories()->depth(0)->in(ConfigurationService::getContentBlockLegacyPath());

        foreach ($cbFinder as $splPath) {
            if (!is_readable($splPath->getPathname() . '/composer.json')) {
                throw new \RuntimeException('Cannot read or find composer.json file in "' . $splPath->getPathname() . '"' . '/composer.json');
            }
            $composerJson = json_decode(file_get_contents($splPath->getPathname() . '/composer.json'), true);
            if ($composerJson['type'] !== ConfigurationService::getComposerType()) {
                continue;
            }
            $nameFromComposer = explode('/', $composerJson['name']);
            $result[] = $this->findByIdentifier($nameFromComposer[1]);
        }

        $tableDefinitionCollection = TableDefinitionCollection::createFromArray($result);
        $this->tableDefinitionCollection = $tableDefinitionCollection;
        return $this->tableDefinitionCollection;
    }

    protected function findByIdentifier(string $identifier): array
    {
        $cbBasePath = ConfigurationService::getContentBlockLegacyPath() . '/' . $identifier;
        if (!file_exists($cbBasePath)) {
            throw new \RuntimeException('Content block "' . $identifier . '" could not be found in "' . $cbBasePath . '".');
        }
        // @todo Validator check if the content block can be processed
        $cbConf = [];
        $cbConf['composerJson'] = json_decode(
            file_get_contents($cbBasePath . '/' . 'composer.json'),
            true
        );
        $cbConf['yaml'] = Yaml::parseFile(
            $cbBasePath . '/' . ConfigurationService::getContentBlocksPrivatePath() . '/' . 'EditorInterface.yaml',
        );

        // icon
        // directory paths (relative to publicPath())
        $path = ConfigurationService::getContentBlockLegacyPath() . '/' . $identifier;
        $iconPath = null;
        $iconProviderClass = null;
        foreach (['svg', 'png', 'gif'] as $ext) {
            $checkIconPath = GeneralUtility::getFileAbsFileName(
                $path . '/' . ConfigurationService::getContentBlocksPublicPath() . '/' . 'ContentBlockIcon.' . $ext
            );
            if (is_readable($checkIconPath)) {
                $iconPath = $path . '/' . ConfigurationService::getContentBlocksPublicPath() . '/' . 'ContentBlockIcon.' . $ext;
                $iconProviderClass = $ext === 'svg'
                    ? SvgIconProvider::class
                    : BitmapIconProvider::class;
                break;
            }
        }
        $cbConf['icon'] = $iconPath;
        $cbConf['iconProvider'] = $iconProviderClass;

        if ($iconPath === null) {
            throw new \RuntimeException('No icon could be found for content block "' . $identifier . '" in path "' . $path . '".');
        }
        return $cbConf;
    }
}
