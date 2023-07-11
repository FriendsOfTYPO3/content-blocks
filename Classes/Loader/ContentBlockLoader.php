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

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;
use TYPO3\CMS\ContentBlocks\Basics\BasicsLoader;
use TYPO3\CMS\ContentBlocks\Basics\BasicsService;
use TYPO3\CMS\ContentBlocks\Definition\Factory\TableDefinitionCollectionFactory;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\ContentBlocks\Registry\LanguageFileRegistry;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\Core\Cache\Frontend\PhpFrontend;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider;
use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @internal Not part of TYPO3's public API.
 */
class ContentBlockLoader implements LoaderInterface
{
    protected ?TableDefinitionCollection $tableDefinitionCollection = null;

    public function __construct(
        protected readonly PhpFrontend $cache,
        protected readonly ContentBlockRegistry $contentBlockRegistry,
        protected readonly LanguageFileRegistry $languageFileRegistry,
        protected readonly TableDefinitionCollectionFactory $tableDefinitionCollectionFactory,
        protected readonly BasicsLoader $basicsLoader,
        protected readonly BasicsService $basicsService,
        protected readonly PackageManager $packageManager,
    ) {
    }

    public function load(bool $allowCache = true): TableDefinitionCollection
    {
        if ($allowCache && $this->tableDefinitionCollection instanceof TableDefinitionCollection) {
            return $this->tableDefinitionCollection;
        }

        if ($allowCache && is_array($contentBlocks = $this->cache->require('content-blocks'))) {
            $contentBlocks = array_map(fn (array $contentBlock): LoadedContentBlock => LoadedContentBlock::fromArray($contentBlock), $contentBlocks);
            foreach ($contentBlocks as $contentBlock) {
                $this->contentBlockRegistry->register($contentBlock);
                $this->languageFileRegistry->register($contentBlock);
            }
            $tableDefinitionCollection = $this->tableDefinitionCollectionFactory->createFromLoadedContentBlocks($contentBlocks);
            $this->tableDefinitionCollection = $tableDefinitionCollection;
            return $this->tableDefinitionCollection;
        }

        // Load Basics before content block types.
        $this->basicsLoader->load();

        // Load content blocks
        $loadedContentBlocks = [];
        foreach ($this->packageManager->getActivePackages() as $package) {
            $extensionKey = $package->getPackageKey();
            $contentBlockFolder = $package->getPackagePath() . ContentBlockPathUtility::getRelativeContentTypesPath();
            if (is_dir($contentBlockFolder)) {
                $loadedContentBlocks[] = $this->loadContentBlocksInExtension($contentBlockFolder, $extensionKey);
            }
        }
        $loadedContentBlocks = array_merge([], ...$loadedContentBlocks);
        $this->checkForUniqueness($loadedContentBlocks);
        foreach ($loadedContentBlocks as $contentBlock) {
            $this->contentBlockRegistry->register($contentBlock);
            $this->languageFileRegistry->register($contentBlock);
        }

        $this->publishAssets($loadedContentBlocks);

        $tableDefinitionCollection = $this->tableDefinitionCollectionFactory->createFromLoadedContentBlocks($loadedContentBlocks);
        $this->tableDefinitionCollection = $tableDefinitionCollection;

        $cache = array_map(fn (LoadedContentBlock $contentBlock): array => $contentBlock->toArray(), $loadedContentBlocks);
        $this->cache->set('content-blocks', 'return ' . var_export($cache, true) . ';');

        return $this->tableDefinitionCollection;
    }

    protected function loadContentBlocksInExtension(string $path, string $extensionKey): array
    {
        $result = [];
        $finder = new Finder();
        $finder->directories()->depth(0)->in($path);
        foreach ($finder as $splFileInfo) {
            $yamlPath = $splFileInfo->getPathname() . '/' . ContentBlockPathUtility::getEditorInterfacePath();
            $yamlContent = Yaml::parseFile($yamlPath);
            if (!is_array($yamlContent) || strlen($yamlContent['name'] ?? '') < 3 || !str_contains($yamlContent['name'], '/')) {
                throw new \RuntimeException('Invalid EditorInterface.yaml file in "' . $yamlPath . '"' . ': Cannot find a valid name in format "vendor/package".', 1678224283);
            }

            $relativeExtensionPath = ContentBlockPathUtility::getRelativeContentBlockPath($extensionKey, $splFileInfo->getRelativePathname());
            $result[] = $this->loadSingleContentBlock($yamlContent['name'], $splFileInfo->getPathname() . '/', $relativeExtensionPath, $yamlContent);
        }
        return $result;
    }

    protected function loadSingleContentBlock(
        string $name,
        string $packagePath = '',
        string $contentBlockFolder = '',
        array $yaml = []
    ): LoadedContentBlock {
        if (!file_exists($packagePath)) {
            throw new \RuntimeException('Content block "' . $name . '" could not be found in "' . $packagePath . '".', 1678699637);
        }

        $iconPath = null;
        $iconProviderClass = null;
        foreach (['svg', 'png', 'gif'] as $fileExtension) {
            $checkIconPath = $contentBlockFolder . '/' . ContentBlockPathUtility::getIconPath();
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

        $yaml = $this->basicsService->applyBasics($yaml);

        return new LoadedContentBlock(
            name: $name,
            yaml: $yaml,
            icon: $iconPath,
            iconProvider: $iconProviderClass,
            path: $contentBlockFolder
        );
    }

    /**
     * @param LoadedContentBlock[] $loadedContentBlocks
     */
    protected function checkForUniqueness(array $loadedContentBlocks): void
    {
        $uniqueNames = [];
        foreach ($loadedContentBlocks as $loadedContentBlock) {
            if (in_array($loadedContentBlock->getName(), $uniqueNames, true)) {
                throw new \InvalidArgumentException(
                    'The content block with the name "' . $loadedContentBlock->getName() . '" exists more than once. Please choose another name.',
                    1678474766
                );
            }
            $uniqueNames[] = $loadedContentBlock->getName();
        }
    }

    /**
     * @param LoadedContentBlock[] $loadedContentBlocks
     */
    public function publishAssets(array $loadedContentBlocks): void
    {
        if (!Environment::isComposerMode()) {
            return;
        }

        $fileSystem = new Filesystem();
        $assetsPath = Environment::getPublicPath() . '/_assets/cb';
        $fileSystem->remove($assetsPath);
        $fileSystem->mkdir($assetsPath);
        foreach ($loadedContentBlocks as $loadedContentBlock) {
            $absolutContentBlockPublicPath = GeneralUtility::getFileAbsFileName(
                $loadedContentBlock->getPath() . '/' . ContentBlockPathUtility::getPublicFolderPath()
            );
            $contentBlockAssetsPathDestination = $assetsPath . '/' . $loadedContentBlock->getName();
            if (!$fileSystem->exists($contentBlockAssetsPathDestination)) {
                $fileSystem->symlink($absolutContentBlockPublicPath, $contentBlockAssetsPathDestination);
            }
        }
    }
}
