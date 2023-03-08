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
use TYPO3\CMS\Core\Cache\Frontend\PhpFrontend;
use TYPO3\CMS\Core\Package\Package;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @internal Not part of TYPO3's public API.
 */
class PackageLoader extends AbstractLoader implements LoaderInterface
{
    protected ?TableDefinitionCollection $tableDefinitionCollection = null;

    public function __construct(
        protected PhpFrontend $cache
    ) {
    }

    public function load(): TableDefinitionCollection
    {
        if ($this->tableDefinitionCollection instanceof TableDefinitionCollection) {
            return $this->tableDefinitionCollection;
        }

        if (is_array($contentBlocks = $this->cache->require('content-blocks'))) {
            $contentBlocks = array_map(fn ($contentBlock) => ParsedContentBlock::fromArray($contentBlock), $contentBlocks);
            $tableDefinitionCollection = TableDefinitionCollection::createFromArray($contentBlocks);
            $this->tableDefinitionCollection = $tableDefinitionCollection;
            return $this->tableDefinitionCollection;
        }

        $result = [];

        /** @var PackageManager */
        $packageManager = GeneralUtility::makeInstance(PackageManager::class);
        /** @var Package $t3Package */
        foreach($packageManager->getAvailablePackages() as $t3Package) {
            $extKey = $t3Package->getPackageKey();
            $cbPathInPackage = $t3Package->getPackagePath() . ContentBlockPathUtility::getContentBlocksSubDirectory();
            if (is_dir($cbPathInPackage)) {
                $result = array_merge($result, $this->loadDir($cbPathInPackage, $extKey));
            }
        }
        // @todo: insert asset publishing here when cache is empty

        $cache = array_map(fn (ParsedContentBlock $contentBlock) => $contentBlock->toArray(), $result);
        $this->cache->set('content-blocks', 'return ' . var_export($cache, true) . ';');
        $tableDefinitionCollection = TableDefinitionCollection::createFromArray($result);
        $this->tableDefinitionCollection = $tableDefinitionCollection;
        return $this->tableDefinitionCollection;
    }

    protected function loadDir(string $path, string $extKey): array
    {
        $result = [];
        $cbFinder = new Finder();
        $cbFinder->directories()->depth(0)->in($path);

        foreach ($cbFinder as $splPath) {
            $yamlDestination = $splPath->getPathname() . '/' . ContentBlockPathUtility::getPathToEditorConfig();
            if (!is_readable($yamlDestination)) {
                throw new \RuntimeException('Cannot read or find EditorInterface.yaml file in "' . $splPath->getPathname() . '"', 1674224824);
            }
            $yamlContent = Yaml::parseFile($yamlDestination);
            if (!is_array($yamlContent) || !isset($yamlContent['name']) || strlen($yamlContent['name']) < 3 || strpos($yamlContent['name'], '/') < 1) {
                throw new \RuntimeException('Invalid EditorInterface.yaml file in "' . $yamlDestination . '"' . ': Cannot find a valid name in format "vendor/package".', 1678224283);
            }

            $pathInExt = ContentBlockPathUtility::getRelativeContentBlockPath($extKey, $splPath->getRelativePathname());
            $result[] = $this->loadPackageConfiguration($yamlContent['name'], $splPath->getPathname() . '/', $pathInExt, $yamlContent);
        }
        return $result;
    }
}
