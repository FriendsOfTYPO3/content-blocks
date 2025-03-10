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
use TYPO3\CMS\ContentBlocks\FieldType\FieldTypeRegistry;
use TYPO3\CMS\ContentBlocks\Generator\HtmlTemplateCodeGenerator;
use TYPO3\CMS\ContentBlocks\Generator\LanguageFileGenerator;
use TYPO3\CMS\ContentBlocks\Loader\LoadedContentBlock;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\ContentBlocks\Schema\SimpleTcaSchemaFactory;
use TYPO3\CMS\ContentBlocks\Service\Icon\ContentTypeIconResolver;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @internal Not part of TYPO3's public API.
 */
readonly class ContentBlockBuilder
{
    public function __construct(
        protected HtmlTemplateCodeGenerator $htmlTemplateCodeGenerator,
        protected LanguageFileGenerator $languageFileGenerator,
        protected ContentBlockRegistry $contentBlockRegistry,
        protected FieldTypeRegistry $fieldTypeRegistry,
        protected TableDefinitionCollectionFactory $tableDefinitionCollectionFactory,
        protected SimpleTcaSchemaFactory $simpleTcaSchemaFactory,
    ) {}

    /**
     * Writes a Content Block to file system.
     */
    public function create(LoadedContentBlock $contentBlock, ?string $skeletonPath = null): void
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

        $this->copySkeleton($basePath, $skeletonPath);
        $this->initializeRegistries($contentBlock);

        // Create base directories for a Content Block.
        $assetsPath = $basePath . '/' . ContentBlockPathUtility::getAssetsFolder();
        $templatePath = $basePath . '/' . ContentBlockPathUtility::getTemplatesFolder();
        $languagePath = $basePath . '/' . ContentBlockPathUtility::getLanguageFolder();
        GeneralUtility::mkdir_deep($assetsPath);
        GeneralUtility::mkdir_deep($templatePath);
        GeneralUtility::mkdir_deep($languagePath);

        $this->createLabelsXlf($contentBlock, $basePath);
        $this->createConfigYaml($contentBlock, $basePath);

        $contentType = $contentBlock->getContentType();
        if ($contentType === ContentType::CONTENT_ELEMENT) {
            $this->createFrontendHtml($contentBlock, $basePath);
            $this->createBackendPreviewHtml($contentBlock, $basePath);
        }
        $this->copyDefaultIcon($contentType, $basePath);
        if ($contentType === ContentType::PAGE_TYPE) {
            $this->copyHideInMenuIcon($basePath);
            $this->createBackendPreviewHtml($contentBlock, $basePath);
        }
    }

    protected function initializeRegistries(LoadedContentBlock $contentBlock): void
    {
        $this->contentBlockRegistry->register($contentBlock);
        $tableDefinitionCollection = $this->tableDefinitionCollectionFactory->createUncached(
            $this->contentBlockRegistry,
            $this->fieldTypeRegistry,
            $this->simpleTcaSchemaFactory,
        );
        $automaticLanguageKeysRegistry = $tableDefinitionCollection->getAutomaticLanguageKeysRegistry();
        $this->languageFileGenerator->setAutomaticLanguageKeysRegistry($automaticLanguageKeysRegistry);
    }

    protected function createConfigYaml(LoadedContentBlock $contentBlock, string $basePath): void
    {
        $contentType = $contentBlock->getContentType();
        $yamlContent = $contentBlock->getYaml();
        unset($yamlContent['title']);
        unset($yamlContent['description']);
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
        $xliffContent = $this->languageFileGenerator->generate($contentBlock);
        GeneralUtility::writeFile(
            $basePath . '/' . ContentBlockPathUtility::getLanguageFilePath(),
            $xliffContent
        );
    }

    protected function copySkeleton(string $basePath, ?string $skeletonPath): void
    {
        if ($skeletonPath === null) {
            return;
        }
        if (is_dir($skeletonPath)) {
            GeneralUtility::copyDirectory($skeletonPath, $basePath);
        }
    }

    protected function createBackendPreviewHtml(LoadedContentBlock $contentBlock, string $basePath): void
    {
        $filePath = $basePath . '/' . ContentBlockPathUtility::getBackendPreviewPath();
        if (file_exists($filePath)) {
            return;
        }
        $backendPreviewHtml = $this->htmlTemplateCodeGenerator->generateEditorPreviewTemplate($contentBlock);
        GeneralUtility::writeFile($filePath, $backendPreviewHtml);
    }

    protected function createFrontendHtml(LoadedContentBlock $contentBlock, string $basePath): void
    {
        $filePath = $basePath . '/' . ContentBlockPathUtility::getFrontendTemplatePath();
        if (file_exists($filePath)) {
            return;
        }
        $frontendHtml = $this->htmlTemplateCodeGenerator->generateFrontendTemplate($contentBlock);
        GeneralUtility::writeFile($filePath, $frontendHtml);
    }

    protected function copyDefaultIcon(ContentType $contentType, string $basePath): void
    {
        $defaultIcon = ContentTypeIconResolver::getDefaultContentTypeIcon($contentType);
        $absoluteDefaultIconPath = GeneralUtility::getFileAbsFileName($defaultIcon);
        $contentBlockIconPath = $basePath . '/' . ContentBlockPathUtility::getIconPathWithoutFileExtension() . '.svg';
        if (file_exists($contentBlockIconPath)) {
            return;
        }
        copy($absoluteDefaultIconPath, $contentBlockIconPath);
    }

    protected function copyHideInMenuIcon(string $basePath): void
    {
        $hideInMenuIcon = 'EXT:content_blocks/Resources/Public/Icons/DefaultPageTypeIconHideInMenu.svg';
        $absoluteHideInMenuIconPath = GeneralUtility::getFileAbsFileName($hideInMenuIcon);
        $contentBlockHideInMenuIconPath = $basePath . '/' . ContentBlockPathUtility::getHideInMenuIconPathWithoutFileExtension() . '.svg';
        if (file_exists($contentBlockHideInMenuIconPath)) {
            return;
        }
        copy($absoluteHideInMenuIconPath, $contentBlockHideInMenuIconPath);
    }
}
