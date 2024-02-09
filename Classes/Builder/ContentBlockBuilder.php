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

namespace TYPO3\CMS\ContentBlocks\Builder;

use Symfony\Component\Yaml\Yaml;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;
use TYPO3\CMS\ContentBlocks\Definition\Factory\TableDefinitionCollectionFactory;
use TYPO3\CMS\ContentBlocks\Generator\HtmlTemplateCodeGenerator;
use TYPO3\CMS\ContentBlocks\Generator\LanguageFileGenerator;
use TYPO3\CMS\ContentBlocks\Loader\LoadedContentBlock;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\ContentBlocks\Service\ContentTypeIconResolver;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @internal Not part of TYPO3's public API.
 */
class ContentBlockBuilder
{
    public function __construct(
        protected readonly HtmlTemplateCodeGenerator $htmlTemplateCodeGenerator,
        protected readonly LanguageFileGenerator $languageFileGenerator,
        protected readonly ContentBlockRegistry $contentBlockRegistry,
        protected readonly TableDefinitionCollectionFactory $tableDefinitionCollectionFactory,
    ) {}

    /**
     * Writes a Content Block to file system.
     */
    public function create(LoadedContentBlock $contentBlock): void
    {
        $name = $contentBlock->getPackage();
        $extPath = $contentBlock->getExtPath();
        $basePath = GeneralUtility::getFileAbsFileName($extPath);
        if ($basePath === '') {
            throw new \RuntimeException('Path to package "' . $name . '" cannot be empty.', 1674225339);
        }
        $basePath .= '/' . $name;
        if (file_exists($basePath)) {
            throw new \RuntimeException(
                'A Content Block with the folder name "' . $name . '" already exists in extension "' . $contentBlock->getHostExtension() . '".',
                1674225340
            );
        }

        $this->initializeRegistries($contentBlock);

        // Create public Assets directory.
        $publicPath = $basePath . '/' . ContentBlockPathUtility::getPublicFolder();
        GeneralUtility::mkdir_deep($publicPath);

        $this->createEditorInterfaceYaml($contentBlock, $basePath);
        $this->createLabelsXlf($contentBlock, $basePath);

        $contentType = $contentBlock->getContentType();
        if ($contentType === ContentType::CONTENT_ELEMENT) {
            $this->createFrontendHtml($contentBlock, $basePath);
            $this->createBackendPreviewHtml($contentBlock, $basePath);
            $this->createExamplePublicAssets($publicPath);
        }
        $this->copyDefaultIcon($contentType, $basePath);
    }

    protected function initializeRegistries(LoadedContentBlock $contentBlock): void
    {
        $this->contentBlockRegistry->register($contentBlock);
        $tableDefinitionCollection = $this->tableDefinitionCollectionFactory->createUncached($this->contentBlockRegistry);
        $automaticLanguageKeysRegistry = $tableDefinitionCollection->getAutomaticLanguageKeysRegistry();
        $this->languageFileGenerator->setAutomaticLanguageKeysRegistry($automaticLanguageKeysRegistry);
    }

    protected function createEditorInterfaceYaml(LoadedContentBlock $contentBlock, string $basePath): void
    {
        $contentType = $contentBlock->getContentType();
        $yamlContent = $contentBlock->getYaml();
        if ($contentType === ContentType::CONTENT_ELEMENT || $contentType === ContentType::PAGE_TYPE) {
            unset($yamlContent['table']);
            unset($yamlContent['typeField']);
        }
        GeneralUtility::writeFile(
            $basePath . '/' . ContentBlockPathUtility::getContentBlockDefinitionFileName(),
            Yaml::dump($yamlContent, 10, 2)
        );
    }

    protected function createLabelsXlf(LoadedContentBlock $contentBlock, string $basePath): void
    {
        GeneralUtility::mkdir_deep($basePath . '/' . ContentBlockPathUtility::getLanguageFolderPath());
        $xliffContent = $this->languageFileGenerator->generate($contentBlock);
        GeneralUtility::writeFile(
            $basePath . '/' . ContentBlockPathUtility::getLanguageFilePath(),
            $xliffContent
        );
    }

    protected function createBackendPreviewHtml(LoadedContentBlock $contentBlock, string $basePath): void
    {
        GeneralUtility::writeFile(
            $basePath . '/' . ContentBlockPathUtility::getBackendPreviewPath(),
            $this->htmlTemplateCodeGenerator->generateEditorPreviewTemplate($contentBlock)
        );
    }

    protected function createFrontendHtml(LoadedContentBlock $contentBlock, string $basePath): void
    {
        GeneralUtility::writeFile(
            $basePath . '/' . ContentBlockPathUtility::getFrontendTemplatePath(),
            $this->htmlTemplateCodeGenerator->generateFrontendTemplate($contentBlock)
        );
    }

    protected function createExamplePublicAssets(string $publicPath): void
    {
        GeneralUtility::writeFile(
            $publicPath . '/EditorPreview.css',
            '/* Created by Content Blocks */'
        );
        GeneralUtility::writeFile(
            $publicPath . '/Frontend.css',
            '/* Created by Content Blocks */'
        );
        GeneralUtility::writeFile(
            $publicPath . '/Frontend.js',
            '/* Created by Content Blocks */'
        );
    }

    protected function copyDefaultIcon(ContentType $contentType, string $basePath): void
    {
        $defaultIcon = ContentTypeIconResolver::getDefaultContentTypeIcon($contentType);
        $absoluteDefaultIconPath = GeneralUtility::getFileAbsFileName($defaultIcon);
        $contentBlockIconPath = $basePath . '/' . ContentBlockPathUtility::getIconPathWithoutFileExtension() . '.svg';
        copy($absoluteDefaultIconPath, $contentBlockIconPath);
    }
}
