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

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;
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
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
        protected readonly PhpFrontend $cache,
        protected readonly BasicsService $basicsService,
        protected readonly PackageManager $packageManager,
        protected readonly IconProcessor $iconProcessor,
    ) {}

    public function load(): ContentBlockRegistry
    {
        if (isset($this->contentBlockRegistry)) {
            return $this->contentBlockRegistry;
        }
        if (is_array($contentBlocks = $this->cache->require('content-blocks'))) {
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
        $contentBlockRegistry = $this->fillContentBlockRegistry($loadedContentBlocks);

        $this->publishAssets($loadedContentBlocks);
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
    protected function loadContentBlocksInExtension(string $path, string $extensionKey, ContentType $contentType): array
    {
        $result = [];
        $finder = new Finder();
        $finder->directories()->depth(0)->in($path);
        foreach ($finder as $splFileInfo) {
            $absoluteContentBlockPath = $splFileInfo->getPathname();
            $contentBlockFolderName = $splFileInfo->getRelativePathname();
            $contentBlockExtPath = ContentBlockPathUtility::getContentBlockExtPath($extensionKey, $contentBlockFolderName, $contentType);
            $configYaml = $this->parseConfigYaml($absoluteContentBlockPath, $contentBlockExtPath, $contentType);
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

    protected function parseConfigYaml(string $absoluteContentBlockPath, string $contentBlockExtPath, ContentType $contentType): ?array
    {
        $contentBlockDefinitionFileName = ContentBlockPathUtility::getContentBlockDefinitionFileName();
        $yamlPath = $absoluteContentBlockPath . '/' . $contentBlockDefinitionFileName;
        if (!file_exists($yamlPath)) {
            return null;
        }
        $editorInterfaceYaml = Yaml::parseFile($yamlPath);
        if (!is_array($editorInterfaceYaml) || strlen($editorInterfaceYaml['name'] ?? '') < 3 || !str_contains($editorInterfaceYaml['name'], '/')) {
            throw new \RuntimeException(
                'Invalid config.yaml file in "' . $yamlPath . '"' . ': Cannot find a valid name in format "vendor/name".',
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
        // Create typeName
        $typeName = $yaml['typeName'] ?? UniqueIdentifierCreator::createContentTypeIdentifier($name);
        $yaml['typeName'] ??= $typeName;
        $table = $yaml['table'];
        $yaml = $this->basicsService->applyBasics($yaml);
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

    /**
     * @param LoadedContentBlock[] $loadedContentBlocks
     */
    protected function publishAssets(array $loadedContentBlocks): void
    {
        $fileSystem = new Filesystem();
        foreach ($loadedContentBlocks as $loadedContentBlock) {
            $hostExtension = $loadedContentBlock->getHostExtension();
            $contentBlockExtPublicPath = $loadedContentBlock->getExtPath() . '/' . ContentBlockPathUtility::getAssetsFolder();
            $contentBlockAbsolutePublicPath = GeneralUtility::getFileAbsFileName($contentBlockExtPublicPath);
            // If the Content Block does not have an Assets folder, nothing to publish here.
            if (!file_exists($contentBlockAbsolutePublicPath)) {
                continue;
            }
            $hostAbsolutePublicContentBlockBasePath = ContentBlockPathUtility::getHostAbsolutePublicContentBlockBasePath($hostExtension);
            // Prevent symlinks from being added to git index.
            $gitIgnorePath = $hostAbsolutePublicContentBlockBasePath . '/.gitignore';
            if (!file_exists($gitIgnorePath)) {
                GeneralUtility::mkdir_deep($hostAbsolutePublicContentBlockBasePath);
                file_put_contents($gitIgnorePath, '*');
            }
            $hostAbsolutePublicContentBlockBasePathWithVendor = $hostAbsolutePublicContentBlockBasePath . '/' . $loadedContentBlock->getVendor();
            $contentBlockRelativePublicPath = $fileSystem->makePathRelative(
                $contentBlockAbsolutePublicPath,
                $hostAbsolutePublicContentBlockBasePathWithVendor
            );
            $hostAbsolutePublicContentBlockPath = ContentBlockPathUtility::getHostAbsolutePublicContentBlockPath(
                $hostExtension,
                $loadedContentBlock->getName(),
            );
            try {
                $fileSystem->symlink($contentBlockRelativePublicPath, $hostAbsolutePublicContentBlockPath);
            } catch (IOException) {
                $fileSystem->mirror($contentBlockAbsolutePublicPath, $hostAbsolutePublicContentBlockPath);
            }
        }
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
