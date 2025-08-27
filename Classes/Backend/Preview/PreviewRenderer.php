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

namespace TYPO3\CMS\ContentBlocks\Backend\Preview;

use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use TYPO3\CMS\Backend\Preview\StandardContentPreviewRenderer;
use TYPO3\CMS\Backend\View\BackendLayout\Grid\GridColumnItem;
use TYPO3\CMS\ContentBlocks\DataProcessing\ContentBlockDataDecorator;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\Core\Domain\RecordFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\View\ViewFactoryData;
use TYPO3\CMS\Core\View\ViewFactoryInterface;
use TYPO3\CMS\Core\View\ViewInterface;
use TYPO3Fluid\Fluid\View\Exception\InvalidSectionException;
use TYPO3Fluid\Fluid\View\Exception\InvalidTemplateResourceException;

/**
 * Sets up Fluid and applies the same DataProcessor as the frontend to the data record.
 *
 * @internal Not part of TYPO3's public API.
 */
#[Autoconfigure(public: true)]
class PreviewRenderer extends StandardContentPreviewRenderer
{
    public function __construct(
        protected TableDefinitionCollection $tableDefinitionCollection,
        protected RecordFactory $recordFactory,
        protected ContentBlockRegistry $contentBlockRegistry,
        protected ContentBlockDataDecorator $contentBlockDataDecorator,
        protected RootPathsSettings $rootPathsSettings,
        protected ViewFactoryInterface $viewFactory,
    ) {}

    public function renderPageModulePreviewHeader(GridColumnItem $item): string
    {
        if (!$this->hasPreviewLayout($item)) {
            return parent::renderPageModulePreviewHeader($item);
        }
        try {
            $preview = $this->renderPreview($item, 'Header');
        } catch (InvalidSectionException|InvalidTemplateResourceException) {
            return parent::renderPageModulePreviewHeader($item);
        }
        return $preview;
    }

    public function renderPageModulePreviewContent(GridColumnItem $item): string
    {
        if (!$this->hasPreviewHtml($item)) {
            return parent::renderPageModulePreviewContent($item);
        }
        if (!$this->hasPreviewLayout($item)) {
            $template = $this->getContentBlockTemplatePath($item) . '/' . ContentBlockPathUtility::getBackendPreviewFileName();
            trigger_error(
                'The Content Blocks preview template "' . $template . '" should be migrated to use the Preview layout.',
                E_USER_DEPRECATED
            );
        }
        try {
            $preview = $this->renderPreview($item, 'Content');
        } catch (InvalidSectionException|InvalidTemplateResourceException) {
            return parent::renderPageModulePreviewContent($item);
        }
        return $preview;
    }

    public function renderPageModulePreviewFooter(GridColumnItem $item): string
    {
        if (!$this->hasPreviewLayout($item)) {
            return parent::renderPageModulePreviewFooter($item);
        }
        try {
            $preview = $this->renderPreview($item, 'Footer');
        } catch (InvalidSectionException|InvalidTemplateResourceException) {
            return parent::renderPageModulePreviewFooter($item);
        }
        return $preview;
    }

    protected function renderPreview(GridColumnItem $item, string $section): string
    {
        /** @var ServerRequestInterface $request */
        $request = $GLOBALS['TYPO3_REQUEST'];
        $record = $item->getRecord();
        $table = $item->getTable();
        $resolvedRecord = $this->recordFactory->createResolvedRecordFromDatabaseRow($table, $record);
        $data = $this->contentBlockDataDecorator->decorate($resolvedRecord, $item->getContext());
        $view = $this->createView($request, $item, $section);
        $view->assign('data', $data);
        $result = $view->render();
        $result = trim($result);
        return $result;
    }

    protected function createView(
        ServerRequestInterface $request,
        GridColumnItem $item,
        string $section,
    ): ViewInterface {
        $templatePath = $this->getContentBlockTemplatePath($item);
        $pageUid = $item->getContext()->getPageId();
        $partialRootPaths = $this->getContentBlocksPartialRootPaths($templatePath, $pageUid);
        $layoutRootPaths = $this->getContentBlocksLayoutRootPaths($templatePath, $pageUid, $section);
        $viewFactoryData = new ViewFactoryData(
            partialRootPaths: $partialRootPaths,
            layoutRootPaths: $layoutRootPaths,
            templatePathAndFilename: $templatePath . '/' . ContentBlockPathUtility::getBackendPreviewFileName(),
            request: $request
        );
        return $this->viewFactory->create($viewFactoryData);
    }

    /**
     * @return array<int, string>
     */
    protected function getContentBlocksPartialRootPaths(string $templatePath, int $pageUid): array
    {
        $contentBlockPartialRootPaths = $this->rootPathsSettings->getContentBlocksPartialRootPaths($pageUid);
        $partialRootPaths = [
            'EXT:backend/Resources/Private/Partials/',
            'EXT:content_blocks/Resources/Private/Partials/',
            ...$contentBlockPartialRootPaths,
            $templatePath . '/partials/',
        ];
        return $partialRootPaths;
    }

    /**
     * @return array<int, string>
     */
    protected function getContentBlocksLayoutRootPaths(string $templatePath, int $pageUid, string $section): array
    {
        $layoutRootPaths = [
            'EXT:content_blocks/Resources/Private/Layouts/Preview/' . $section,
        ];
        if ($section === 'Content') {
            $layoutRootPaths = [
                ...$layoutRootPaths,
                ...$this->rootPathsSettings->getContentBlocksLayoutRootPaths($pageUid),
                $templatePath . '/layouts/',
            ];
        }
        return $layoutRootPaths;
    }

    protected function getContentBlockTemplatePath(GridColumnItem $item): string
    {
        $recordType = $item->getRecordType();
        $table = $item->getTable();
        $tableDefinition = $this->tableDefinitionCollection->getTable($table);
        $contentTypeCollection = $tableDefinition->contentTypeDefinitionCollection;
        if ($contentTypeCollection->hasType($recordType)) {
            $contentTypeDefinition = $contentTypeCollection->getType($recordType);
        } else {
            $contentTypeDefinition = $contentTypeCollection->getFirst();
        }
        $contentBlockExtPath = $this->contentBlockRegistry->getContentBlockExtPath($contentTypeDefinition->getName());
        $contentBlockPrivatePath = $contentBlockExtPath . '/' . ContentBlockPathUtility::getTemplatesFolder();
        return $contentBlockPrivatePath;
    }

    protected function getAbsolutePreviewHtmlTemplatePath(GridColumnItem $item): string
    {
        $templatePath = $this->getContentBlockTemplatePath($item);
        $templatePathAndFilename = $templatePath . '/' . ContentBlockPathUtility::getBackendPreviewFileName();
        $absoluteTemplatePath = GeneralUtility::getFileAbsFileName($templatePathAndFilename);
        return $absoluteTemplatePath;
    }

    protected function hasPreviewHtml(GridColumnItem $item): bool
    {
        $absoluteTemplatePath = $this->getAbsolutePreviewHtmlTemplatePath($item);
        return file_exists($absoluteTemplatePath);
    }

    /**
     * @deprecated Remove in Content Blocks v2.0
     */
    protected function hasPreviewLayout(GridColumnItem $item): bool
    {
        $absoluteTemplatePath = $this->getAbsolutePreviewHtmlTemplatePath($item);
        if (!file_exists($absoluteTemplatePath)) {
            return false;
        }
        $contents = file_get_contents($absoluteTemplatePath);
        return str_contains($contents, '<f:layout name="Preview"');
    }
}
