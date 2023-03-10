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
use TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider;
use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @internal Not part of TYPO3's public API.
 */
class PackageLoader implements LoaderInterface
{
    public function __construct(
        protected PhpFrontend $cache,
        protected ?TableDefinitionCollection $tableDefinitionCollection = null,
    ) {
    }

    public function load(): TableDefinitionCollection
    {
        if ($this->tableDefinitionCollection instanceof TableDefinitionCollection) {
            return $this->tableDefinitionCollection;
        }

        if (is_array($contentBlocks = $this->cache->require('content-blocks'))) {
            $contentBlocks = array_map(fn (array $contentBlock): ParsedContentBlock => ParsedContentBlock::fromArray($contentBlock), $contentBlocks);
            $tableDefinitionCollection = TableDefinitionCollection::createFromArray($contentBlocks);
            $this->tableDefinitionCollection = $tableDefinitionCollection;
            return $this->tableDefinitionCollection;
        }

        $parsedContentBlocks = [];
        $packageManager = GeneralUtility::makeInstance(PackageManager::class);
        foreach ($packageManager->getAvailablePackages() as $package) {
            $extensionKey = $package->getPackageKey();
            $contentBlockFolder = $package->getPackagePath() . ContentBlockPathUtility::getContentBlocksSubDirectory();
            if (is_dir($contentBlockFolder)) {
                $parsedContentBlocks[] = $this->loadContentBlocks($contentBlockFolder, $extensionKey);
            }
        }
        $parsedContentBlocks = array_merge([], ...$parsedContentBlocks);
        $this->checkForUniqueness($parsedContentBlocks);

        // @todo: insert asset publishing here when cache is empty

        $cache = array_map(fn (ParsedContentBlock $contentBlock): array => $contentBlock->toArray(), $parsedContentBlocks);
        $this->cache->set('content-blocks', 'return ' . var_export($cache, true) . ';');
        $tableDefinitionCollection = TableDefinitionCollection::createFromArray($parsedContentBlocks);
        $this->tableDefinitionCollection = $tableDefinitionCollection;
        return $this->tableDefinitionCollection;
    }

    protected function loadContentBlocks(string $path, string $extensionKey): array
    {
        $result = [];
        $finder = new Finder();
        $finder->directories()->depth(0)->in($path);
        foreach ($finder as $splFileInfo) {
            $yamlPath = $splFileInfo->getPathname() . '/' . ContentBlockPathUtility::getPathToEditorConfig();
            $yamlContent = Yaml::parseFile($yamlPath);
            if (!is_array($yamlContent) || strlen($yamlContent['name'] ?? '') < 3 || !str_contains($yamlContent['name'], '/')) {
                throw new \RuntimeException('Invalid EditorInterface.yaml file in "' . $yamlPath . '"' . ': Cannot find a valid name in format "vendor/package".', 1678224283);
            }

            $relativeExtensionPath = ContentBlockPathUtility::getRelativeContentBlockPath($extensionKey, $splFileInfo->getRelativePathname());
            $result[] = $this->loadPackageConfiguration($yamlContent['name'], $splFileInfo->getPathname() . '/', $relativeExtensionPath, $yamlContent);
        }
        return $result;
    }

    protected function loadPackageConfiguration(
        string $name,
        string $packagePath = '',
        string $contentBlockFolder = '',
        array $yaml = []
    ): ParsedContentBlock {
        if (!file_exists($packagePath)) {
            throw new \RuntimeException('Content block "' . $name . '" could not be found in "' . $packagePath . '".', 1674225340);
        }

        $iconPath = null;
        $iconProviderClass = null;

        foreach (['svg', 'png', 'gif'] as $fileExtension) {
            $iconName = 'ContentBlockIcon.' . $fileExtension;
            $checkIconPath = $contentBlockFolder . ContentBlockPathUtility::getPublicPathSegment() . $iconName;
            if (is_readable($checkIconPath)) {
                $iconPath = $checkIconPath;
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
            packagePath: $contentBlockFolder
        );
    }

    /**
     * @param ParsedContentBlock[] $parsedContentBlocks
     */
    protected function checkForUniqueness(array $parsedContentBlocks): void
    {
        $uniqueNames = [];
        foreach ($parsedContentBlocks as $parsedContentBlock) {
            if (in_array($parsedContentBlock->getName(), $uniqueNames, true)) {
                throw new \InvalidArgumentException(
                    'The content block with the name "' . $parsedContentBlock->getName() . '" exists more than once. Please choose another name.',
                    1678474766
                );
            }
            $uniqueNames[] = $parsedContentBlock->getName();
        }
    }
}