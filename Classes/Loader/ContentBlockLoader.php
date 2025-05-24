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

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;
use TYPO3\CMS\ContentBlocks\Basics\BasicsLoader;
use TYPO3\CMS\ContentBlocks\Basics\BasicsService;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentTypeIcon;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\PageIconSet;
use TYPO3\CMS\ContentBlocks\Definition\Factory\UniqueIdentifierCreator;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\ContentBlocks\Service\Icon\ContentTypeIconResolverInput;
use TYPO3\CMS\ContentBlocks\Service\Icon\IconProcessor;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\ContentBlocks\Validation\ContentBlockNameValidator;
use TYPO3\CMS\ContentBlocks\Validation\PageTypeNameValidator;
use TYPO3\CMS\Core\Cache\Frontend\PhpFrontend;
use TYPO3\CMS\Core\Package\PackageInterface;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Resource\FileType;

/**
 * Main bootstrap loader for Content Blocks. This class finds registered
 * Content Blocks in loaded TYPO3 extensions and adds them to the registry.
 * Before that, Content Block Basics are already loaded by BasicsLoader and
 * are applied here for each Content Block. The result is cached in the core
 * cache with the identifier `content-blocks` and is hydrated for new requests.
 *
 * Content Blocks are loaded from these folders inside extensions:
 *
 * host_extension
 * |__ ContentBlocks
 *     |__ ContentElements
 *     |   |__ block-a
 *     |   |   |__ config.yaml < name: vendor/block-a
 *     |   |__ block-b
 *     |__ PageTypes
 *     |   |__ block-c
 *     |__ RecordTypes
 *         |__ block-d
 *
 * These sub-folders may contain any number of Content Blocks. The folder name
 * of a Content Block is not important, but should, for clarity, be the same as
 * the Content Block name. They must contain a config.yaml file with a
 * `name` config. Just like for composer names, it must consist of a vendor and
 * package part separated by a slash. The name parts must be lowercase and can
 * be separated by dashes.
 *
 * @internal Not part of TYPO3's public API.
 */
class ContentBlockLoader
{
    protected ContentBlockRegistry $contentBlockRegistry;

    public function __construct(
        #[Autowire(service: 'cache.core')]
        protected readonly PhpFrontend $cache,
        protected readonly BasicsService $basicsService,
        protected readonly BasicsLoader $basicsLoader,
        protected readonly PackageManager $packageManager,
        protected readonly IconProcessor $iconProcessor,
        protected readonly AssetPublisher $assetPublisher,
    ) {}

    public function load(): ContentBlockRegistry
    {
        if (isset($this->contentBlockRegistry)) {
            return $this->contentBlockRegistry;
        }
        if (is_array($contentBlocks = $this->getFromCache())) {
            $contentBlocks = array_map(fn(array $contentBlock): LoadedContentBlock => LoadedContentBlock::fromArray($contentBlock), $contentBlocks);
            $this->contentBlockRegistry = $this->fillContentBlockRegistry($contentBlocks);
            return $this->contentBlockRegistry;
        }
        $this->contentBlockRegistry = $this->loadUncached();
        $this->setCache();
        return $this->contentBlockRegistry;
    }

    public function loadUncached(): ContentBlockRegistry
    {
        $loadedContentBlocks = [];
        foreach ($this->packageManager->getActivePackages() as $package) {
            $loadedContentBlocks[] = $this->loadContentBlocks($package);
        }
        $loadedContentBlocks = array_merge([], ...$loadedContentBlocks);
        $sortByPriority = fn(LoadedContentBlock $a, LoadedContentBlock $b): int => (int)($b->getYaml()['priority'] ?? 0) <=> (int)($a->getYaml()['priority'] ?? 0);
        usort($loadedContentBlocks, $sortByPriority);
        $contentBlockRegistry = $this->fillContentBlockRegistry($loadedContentBlocks);

        $this->assetPublisher->publishAssets($loadedContentBlocks);
        $this->iconProcessor->process();

        return $contentBlockRegistry;
    }

    /**
     * @param LoadedContentBlock[] $contentBlocks
     */
    protected function fillContentBlockRegistry(array $contentBlocks): ContentBlockRegistry
    {
        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register($contentBlock);
        }
        return $contentBlockRegistry;
    }

    /**
     * @return LoadedContentBlock[]
     */
    protected function loadContentBlocks(PackageInterface $package): array
    {
        $loadedContentBlocks[] = $this->loadContentType($package, ContentType::CONTENT_ELEMENT);
        $loadedContentBlocks[] = $this->loadContentType($package, ContentType::PAGE_TYPE);
        $loadedContentBlocks[] = $this->loadContentType($package, ContentType::RECORD_TYPE);
        $loadedContentBlocks[] = $this->loadContentType($package, ContentType::FILE_TYPE);
        $loadedContentBlocks = array_merge([], ...$loadedContentBlocks);
        return $loadedContentBlocks;
    }

    /**
     * @return LoadedContentBlock[]
     */
    protected function loadContentType(PackageInterface $package, ContentType $contentType): array
    {
        $path = $package->getPackagePath() . ContentBlockPathUtility::getRelativeContentTypePath($contentType);
        if (!is_dir($path)) {
            return [];
        }
        $extensionKey = $package->getPackageKey();
        $result = [];
        $finder = new Finder();
        $finder->directories()->depth(0)->in($path);
        foreach ($finder as $splFileInfo) {
            $absoluteContentBlockPath = $splFileInfo->getPathname();
            $contentBlockFolderName = $splFileInfo->getRelativePathname();
            $contentBlockExtPath = ContentBlockPathUtility::getContentBlockExtPath($extensionKey, $contentBlockFolderName, $contentType);
            $configYaml = $this->parseConfigYaml($absoluteContentBlockPath, $contentType);
            if ($configYaml === null) {
                continue;
            }
            $result[] = $this->loadSingleContentBlock(
                $configYaml['name'],
                $contentType,
                $absoluteContentBlockPath,
                $extensionKey,
                $contentBlockExtPath,
                $configYaml,
            );
        }
        return $result;
    }

    protected function parseConfigYaml(string $absoluteContentBlockPath, ContentType $contentType): ?array
    {
        $contentBlockDefinitionFileName = ContentBlockPathUtility::getContentBlockDefinitionFileName();
        $yamlPath = $absoluteContentBlockPath . '/' . $contentBlockDefinitionFileName;
        if (!file_exists($yamlPath)) {
            return null;
        }
        $config = Yaml::parseFile($yamlPath);
        if (!is_array($config) || strlen($config['name'] ?? '') < 3 || !str_contains($config['name'], '/')) {
            throw new \RuntimeException(
                'Invalid config.yaml file in "' . $yamlPath . '"' . ': Cannot find a valid name in format "vendor/name".',
                1678224283
            );
        }
        [$vendor, $name] = explode('/', $config['name']);
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
            if (!array_key_exists('typeName', $config)) {
                throw new \InvalidArgumentException(
                    'Missing mandatory integer value for "typeName" in ContentBlock "' . $config['name'] . '".',
                    1689286814
                );
            }
            PageTypeNameValidator::validate($config['typeName'], $config['name']);
        }
        if ($contentType === ContentType::FILE_TYPE) {
            if (!array_key_exists('typeName', $config)) {
                throw new \InvalidArgumentException(
                    'Missing mandatory file type for "typeName" in ContentBlock "' . $config['name'] . '".',
                    1733583692
                );
            }
        }
        return $config;
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
        // Hard override table.
        if ($contentType->getTable() !== null) {
            $yaml['table'] = $contentType->getTable();
        }
        // Hard override type field.
        if ($contentType->getTypeField() !== null) {
            $yaml['typeField'] = $contentType->getTypeField();
        }
        $typeName = $this->createTypeName($contentType, $yaml, $name);
        $yaml['typeName'] = $typeName;
        if (!array_key_exists('table', $yaml)) {
            throw new \RuntimeException('Content Block "' . $name . '" does not define required "table".', 1731412650);
        }
        $table = $yaml['table'];
        $basicsRegistry = $this->basicsLoader->loadUncached();
        $yaml = $this->basicsService->applyBasics($basicsRegistry, $yaml);
        $iconIdentifier = ContentBlockPathUtility::getIconNameWithoutFileExtension();
        $contentBlockIcon = new ContentTypeIcon();
        $baseIconInput = new ContentTypeIconResolverInput(
            name: $name,
            absolutePath: $absolutePath,
            extension: $extensionKey,
            identifier: $iconIdentifier,
            contentType: $contentType,
            table: $table,
            typeName: $typeName
        );
        $this->iconProcessor->addInstruction($contentBlockIcon, $baseIconInput);
        $pageIconSet = null;
        if ($contentType === ContentType::PAGE_TYPE) {
            $pageIconSet = $this->constructPageIconSet($baseIconInput);
        }
        return new LoadedContentBlock(
            name: $name,
            yaml: $yaml,
            icon: $contentBlockIcon,
            hostExtension: $extensionKey,
            extPath: $extPath,
            contentType: $contentType,
            pageIconSet: $pageIconSet,
        );
    }

    protected function createTypeName(ContentType $contentType, array $yaml, string $name): string
    {
        if ($contentType === ContentType::FILE_TYPE) {
            $fileType = $yaml['typeName'] ?? '0';
            $typeName = FileType::tryFromMimeType($fileType)->value;
            return (string)$typeName;
        }
        if (isset($yaml['typeName'])) {
            return (string)$yaml['typeName'];
        }
        if ($contentType === ContentType::RECORD_TYPE) {
            return '1';
        }
        $typeName = UniqueIdentifierCreator::createContentTypeIdentifier($name);
        return $typeName;
    }

    protected function constructPageIconSet(ContentTypeIconResolverInput $baseIconInput): ?PageIconSet
    {
        $pageIconHideInMenuFileName = ContentBlockPathUtility::getIconHideInMenuNameWithoutFileExtension();
        $pageIconHideInMenu = $this->createPageIcon($baseIconInput, $pageIconHideInMenuFileName, '-hide-in-menu');

        $pageIconRootFileName = ContentBlockPathUtility::getIconRootNameWithoutFileExtension();
        $pageIconRoot = $this->createPageIcon($baseIconInput, $pageIconRootFileName, '-root');

        $pageIconSet = new PageIconSet(
            $pageIconHideInMenu,
            $pageIconRoot,
        );
        return $pageIconSet;
    }

    protected function createPageIcon(
        ContentTypeIconResolverInput $baseIconInput,
        string $fileName,
        string $suffix,
    ): ContentTypeIcon {
        $pageIcon = new ContentTypeIcon();
        $iconIdentifier = $fileName;
        $pageIconInput = clone $baseIconInput;
        $pageIconInput->identifier = $iconIdentifier;
        $pageIconInput->suffix = $suffix;
        $pageIconInput->withFallback = false;
        $this->iconProcessor->addInstruction($pageIcon, $pageIconInput);
        return $pageIcon;
    }

    protected function getFromCache(): false|array
    {
        return $this->cache->require('ContentBlocks_Raw');
    }

    protected function setCache(): void
    {
        $cache = array_map(fn(LoadedContentBlock $contentBlock): array => $contentBlock->toArray(), $this->contentBlockRegistry->getAll());
        $this->cache->set('ContentBlocks_Raw', 'return ' . var_export($cache, true) . ';');
    }
}
