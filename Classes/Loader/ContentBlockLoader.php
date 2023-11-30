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
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;
use TYPO3\CMS\ContentBlocks\Definition\Factory\TableDefinitionCollectionFactory;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\ContentBlocks\Registry\LanguageFileRegistry;
use TYPO3\CMS\ContentBlocks\Service\ContentTypeIconResolver;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\ContentBlocks\Validation\ContentBlockNameValidator;
use TYPO3\CMS\ContentBlocks\Validation\PageTypeNameValidator;
use TYPO3\CMS\Core\Cache\Frontend\PhpFrontend;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @internal Not part of TYPO3's public API.
 */
class ContentBlockLoader
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
    ) {}

    public function load(): TableDefinitionCollection
    {
        if ($this->tableDefinitionCollection instanceof TableDefinitionCollection) {
            return $this->tableDefinitionCollection;
        }

        if (is_array($contentBlocks = $this->cache->require('content-blocks'))) {
            $contentBlocks = array_map(fn(array $contentBlock): LoadedContentBlock => LoadedContentBlock::fromArray($contentBlock), $contentBlocks);
            foreach ($contentBlocks as $contentBlock) {
                $this->contentBlockRegistry->register($contentBlock);
                $this->languageFileRegistry->register($contentBlock);
            }
            $tableDefinitionCollection = $this->tableDefinitionCollectionFactory->createFromLoadedContentBlocks($contentBlocks);
            $this->tableDefinitionCollection = $tableDefinitionCollection;
            return $this->tableDefinitionCollection;
        }

        return $this->loadUncached();
    }

    public function loadUncached(): TableDefinitionCollection
    {
        $this->contentBlockRegistry->flush();
        // Load Basics before content block types.
        $this->basicsLoader->load();

        // Load content blocks
        $loadedContentBlocks = [];
        foreach ($this->packageManager->getActivePackages() as $package) {
            $extensionKey = $package->getPackageKey();
            $contentElementsFolder = $package->getPackagePath() . ContentBlockPathUtility::getRelativeContentElementsPath();
            if (is_dir($contentElementsFolder)) {
                $loadedContentBlocks[] = $this->loadContentBlocksInExtension($contentElementsFolder, $extensionKey, ContentType::CONTENT_ELEMENT);
            }
            $pageTypesFolder = $package->getPackagePath() . ContentBlockPathUtility::getRelativePageTypesPath();
            if (is_dir($pageTypesFolder)) {
                $loadedContentBlocks[] = $this->loadContentBlocksInExtension($pageTypesFolder, $extensionKey, ContentType::PAGE_TYPE);
            }
            $recordTypesFolder = $package->getPackagePath() . ContentBlockPathUtility::getRelativeRecordTypesPath();
            if (is_dir($recordTypesFolder)) {
                $loadedContentBlocks[] = $this->loadContentBlocksInExtension($recordTypesFolder, $extensionKey, ContentType::RECORD_TYPE);
            }
        }
        $loadedContentBlocks = array_merge([], ...$loadedContentBlocks);
        $sortByPriority = fn(LoadedContentBlock $a, LoadedContentBlock $b): int => (int)($b->getYaml()['priority'] ?? 0) <=> (int)($a->getYaml()['priority'] ?? 0);
        usort($loadedContentBlocks, $sortByPriority);
        foreach ($loadedContentBlocks as $contentBlock) {
            $this->contentBlockRegistry->register($contentBlock);
            $this->languageFileRegistry->register($contentBlock);
        }

        $this->publishAssets($loadedContentBlocks);

        $tableDefinitionCollection = $this->tableDefinitionCollectionFactory->createFromLoadedContentBlocks($loadedContentBlocks);
        $this->tableDefinitionCollection = $tableDefinitionCollection;

        $cache = array_map(fn(LoadedContentBlock $contentBlock): array => $contentBlock->toArray(), $loadedContentBlocks);
        $this->cache->set('content-blocks', 'return ' . var_export($cache, true) . ';');

        return $this->tableDefinitionCollection;
    }

    /**
     * @return LoadedContentBlock[]
     */
    protected function loadContentBlocksInExtension(string $path, string $extensionKey, ContentType $contentType): array
    {
        $result = [];
        $finder = new Finder();
        $finder->directories()->depth(0)->in($path);
        foreach ($finder as $splFileInfo) {
            $absoluteContentBlockPath = $splFileInfo->getPathname();
            $contentBlockFolderName = $splFileInfo->getRelativePathname();
            $contentBlockExtPath = ContentBlockPathUtility::getContentBlockExtPath($extensionKey, $contentBlockFolderName, $contentType);
            $editorInterfaceYaml = $this->parseEditorInterfaceYaml($absoluteContentBlockPath, $contentType);
            $result[] = $this->loadSingleContentBlock(
                $editorInterfaceYaml['name'],
                $contentType,
                $absoluteContentBlockPath,
                $extensionKey,
                $contentBlockExtPath,
                $editorInterfaceYaml,
            );
        }
        return $result;
    }

    protected function parseEditorInterfaceYaml(string $absoluteContentBlockPath, mixed $contentType): array
    {
        $yamlPath = $absoluteContentBlockPath . '/' . ContentBlockPathUtility::getContentBlockDefinitionFileName();
        $editorInterfaceYaml = Yaml::parseFile($yamlPath);
        if (!is_array($editorInterfaceYaml) || strlen($editorInterfaceYaml['name'] ?? '') < 3 || !str_contains($editorInterfaceYaml['name'], '/')) {
            throw new \RuntimeException(
                'Invalid EditorInterface.yaml file in "' . $yamlPath . '"' . ': Cannot find a valid name in format "vendor/name".',
                1678224283
            );
        }
        [$vendor, $name] = explode('/', $editorInterfaceYaml['name']);
        if (!ContentBlockNameValidator::isValid($vendor)) {
            throw new \InvalidArgumentException(
                'Invalid vendor name for Content Block "' . $vendor . '". The vendor must be lowercase and consist of words separated by -',
                1696004679
            );
        }
        if (!ContentBlockNameValidator::isValid($name)) {
            throw new \InvalidArgumentException(
                'Invalid name for Content Block "' . $name . '". The name must be lowercase and consist of words separated by -',
                1696004684
            );
        }
        if ($contentType === ContentType::PAGE_TYPE) {
            if (!array_key_exists('typeName', $editorInterfaceYaml)) {
                throw new \InvalidArgumentException(
                    'Missing mandatory integer value for "typeName" in ContentBlock "' . $editorInterfaceYaml['name'] . '".',
                    1689286814
                );
            }
            PageTypeNameValidator::validate($editorInterfaceYaml['typeName'], $editorInterfaceYaml['name']);
        }
        return $editorInterfaceYaml;
    }

    protected function loadSingleContentBlock(
        string $name,
        ContentType $contentType,
        string $absolutePath,
        string $extensionKey,
        string $extPath,
        array $yaml,
    ): LoadedContentBlock {
        if (!file_exists($absolutePath)) {
            throw new \RuntimeException('Content Block "' . $name . '" could not be found in "' . $absolutePath . '".', 1678699637);
        }

        // Override table and typeField for Content Elements and Page Types.
        if ($contentType === ContentType::CONTENT_ELEMENT || $contentType === ContentType::PAGE_TYPE) {
            $yaml['table'] = $contentType->getTable();
            $yaml['typeField'] = $contentType->getTypeField();
        }

        $yaml = $this->basicsService->applyBasics($yaml);
        $iconIdentifier = ContentBlockPathUtility::getIconNameWithoutFileExtension();
        $contentBlockIcon = ContentTypeIconResolver::resolve($name, $absolutePath, $extPath, $iconIdentifier, $contentType);

        return new LoadedContentBlock(
            name: $name,
            yaml: $yaml,
            icon: $contentBlockIcon->iconPath,
            iconProvider: $contentBlockIcon->iconProvider,
            hostExtension: $extensionKey,
            extPath: $extPath,
            contentType: $contentType,
        );
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
        $assetsPath = Environment::getPublicPath() . '/' . ContentBlockPathUtility::getPublicAssetsFolder();
        foreach ($loadedContentBlocks as $loadedContentBlock) {
            $absolutContentBlockPublicPath = GeneralUtility::getFileAbsFileName(
                $loadedContentBlock->getExtPath() . '/' . ContentBlockPathUtility::getPublicFolder()
            );

            $contentBlockAssetsTargetDirectory = $assetsPath . '/' . md5($loadedContentBlock->getName());
            $relativePath = $fileSystem->makePathRelative($absolutContentBlockPublicPath, $assetsPath);
            if (!$fileSystem->exists($contentBlockAssetsTargetDirectory)) {
                $fileSystem->symlink($relativePath, $contentBlockAssetsTargetDirectory);
            }
        }
    }
}
