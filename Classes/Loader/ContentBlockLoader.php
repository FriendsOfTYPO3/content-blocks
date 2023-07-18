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
use TYPO3\CMS\ContentBlocks\Definition\ContentType;
use TYPO3\CMS\ContentBlocks\Definition\Factory\TableDefinitionCollectionFactory;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\ContentBlocks\Registry\LanguageFileRegistry;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\Core\Cache\Frontend\PhpFrontend;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider;
use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;

/**
 * @internal Not part of TYPO3's public API.
 */
class ContentBlockLoader implements LoaderInterface
{
    protected ?TableDefinitionCollection $tableDefinitionCollection = null;

    /** @var list<int> $reservedPageTypes */
    protected static array $reservedPageTypes = [
        PageRepository::DOKTYPE_DEFAULT,
        PageRepository::DOKTYPE_LINK,
        PageRepository::DOKTYPE_SHORTCUT,
        PageRepository::DOKTYPE_BE_USER_SECTION,
        PageRepository::DOKTYPE_SPACER,
        PageRepository::DOKTYPE_SYSFOLDER
    ];

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

    protected function loadContentBlocksInExtension(string $path, string $extensionKey, ContentType $contentType): array
    {
        $result = [];
        $finder = new Finder();
        $finder->directories()->depth(0)->in($path);
        foreach ($finder as $splFileInfo) {
            $yamlPath = $splFileInfo->getPathname() . '/' . ContentBlockPathUtility::getContentBlockDefinitionFileName();
            $yamlContent = Yaml::parseFile($yamlPath);
            if (!is_array($yamlContent) || strlen($yamlContent['name'] ?? '') < 3 || !str_contains($yamlContent['name'], '/')) {
                throw new \RuntimeException('Invalid EditorInterface.yaml file in "' . $yamlPath . '"' . ': Cannot find a valid name in format "vendor/name".', 1678224283);
            }
            if ($contentType === ContentType::PAGE_TYPE) {
                if (!array_key_exists('typeName', $yamlContent)) {
                    throw new \InvalidArgumentException('Missing mandatory integer value for "typeName" in ContentBlock "' . $yamlContent['name'] . '".', 1689286814);
                }
                $typeName = (int)$yamlContent['typeName'];
                if (!MathUtility::canBeInterpretedAsInteger($yamlContent['typeName']) || $typeName < 0 || in_array($typeName, self::$reservedPageTypes, true)) {
                    throw new \InvalidArgumentException(
                        'Invalid value "' . $yamlContent['typeName'] . '" for "typeName" in ContentBlock "' . $yamlContent['name'] . '". Value must be a positive integer and not one of the reserved page types: '
                        . implode(', ', self::$reservedPageTypes),
                        1689287031
                    );
                }
            }

            $contentBlockExtPath = ContentBlockPathUtility::getContentBlockExtPath($extensionKey, $splFileInfo->getRelativePathname(), $contentType);
            $result[] = $this->loadSingleContentBlock(
                $yamlContent['name'],
                $contentType,
                $splFileInfo->getPathname(),
                $contentBlockExtPath,
                $yamlContent,
            );
        }
        return $result;
    }

    protected function loadSingleContentBlock(
        string $name,
        ContentType $contentType,
        string $absolutePath = '',
        string $extPath = '',
        array $yaml = [],
    ): LoadedContentBlock {
        if (!file_exists($absolutePath)) {
            throw new \RuntimeException('Content block "' . $name . '" could not be found in "' . $absolutePath . '".', 1678699637);
        }

        $icon = null;
        $iconProviderClass = null;
        foreach (['svg', 'png', 'gif'] as $fileExtension) {
            $relativeIconPath = ContentBlockPathUtility::getIconPathWithoutFileExtension() . '.' . $fileExtension;
            $checkIconPath = $absolutePath . '/' . $relativeIconPath;
            if (is_readable($checkIconPath)) {
                $prefixPath = Environment::isComposerMode()
                    ? Environment::getPublicPath() . '/' . ContentBlockPathUtility::getSymlinkedAssetsPath($name)
                    : $extPath;
                $icon = $prefixPath . '/' . ContentBlockPathUtility::getIconNameWithoutFileExtension() . '.' . $fileExtension;
                $iconProviderClass = $fileExtension === 'svg' ? SvgIconProvider::class : BitmapIconProvider::class;
                break;
            }
        }

        // Override table and typeField for Content Elements and Page Types.
        if ($contentType === ContentType::CONTENT_ELEMENT || $contentType === ContentType::PAGE_TYPE) {
            $yaml['table'] = $contentType->getTable();
            $yaml['typeField'] = $contentType->getTypeField();
        }

        $yaml = $this->basicsService->applyBasics($yaml);

        return new LoadedContentBlock(
            name: $name,
            yaml: $yaml,
            icon: $icon ?? 'EXT:content_blocks/Resources/Public/Icons/ContentBlockIcon.svg',
            iconProvider: $iconProviderClass ?? SvgIconProvider::class,
            path: $extPath,
            contentType: $contentType,
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
        $assetsPath = Environment::getPublicPath() . '/' . ContentBlockPathUtility::getPublicAssetsFolder();
        $fileSystem->remove($assetsPath);
        $fileSystem->mkdir($assetsPath);
        foreach ($loadedContentBlocks as $loadedContentBlock) {
            $absolutContentBlockPublicPath = GeneralUtility::getFileAbsFileName(
                $loadedContentBlock->getPath() . '/' . ContentBlockPathUtility::getPublicFolder()
            );
            $contentBlockAssetsPathDestination = $assetsPath . '/' . $loadedContentBlock->getName();
            if (!$fileSystem->exists($contentBlockAssetsPathDestination)) {
                $fileSystem->symlink($absolutContentBlockPublicPath, $contentBlockAssetsPathDestination);
            }
        }
    }
}
