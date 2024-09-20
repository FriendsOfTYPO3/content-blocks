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

/**
 * Sets up Fluid and applies the same DataProcessor as the frontend to the data record.
 *
 * @internal Not part of TYPO3's public API.
 */
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

    public function renderPageModulePreviewContent(GridColumnItem $item): string
    {
        /** @var ServerRequestInterface $request */
        $request = $GLOBALS['TYPO3_REQUEST'];
        $record = $item->getRecord();
        $recordType = $item->getRecordType();
        $table = $item->getTable();
        $tableDefinition = $this->tableDefinitionCollection->getTable($table);
        $contentTypeCollection = $tableDefinition->getContentTypeDefinitionCollection();
        if ($contentTypeCollection->hasType($recordType)) {
            $contentTypeDefinition = $contentTypeCollection->getType($recordType);
        } else {
            $contentTypeDefinition = $contentTypeCollection->getFirst();
        }
        $contentBlockExtPath = $this->contentBlockRegistry->getContentBlockExtPath($contentTypeDefinition->getName());
        $contentBlockPrivatePath = $contentBlockExtPath . '/' . ContentBlockPathUtility::getTemplatesFolder();

        // Fall back to standard preview rendering if EditorPreview.html does not exist.
        $editorPreviewExtPath = $contentBlockExtPath . '/' . ContentBlockPathUtility::getBackendPreviewPath();
        $editorPreviewAbsPath = GeneralUtility::getFileAbsFileName($editorPreviewExtPath);
        if (!file_exists($editorPreviewAbsPath)) {
            $result = parent::renderPageModulePreviewContent($item);
            return $result;
        }
        $resolvedRecord = $this->recordFactory->createResolvedRecordFromDatabaseRow($table, $record);
        $data = $this->contentBlockDataDecorator->decorate($resolvedRecord, $item->getContext());
        $view = $this->createView($contentBlockPrivatePath, $request, $item);
        $view->assign('data', $data);
        $result = $view->render();
        return $result;
    }

    protected function createView(
        string $contentBlockPrivatePath,
        ServerRequestInterface $request,
        GridColumnItem $item
    ): ViewInterface {
        $pageUid = $item->getContext()->getPageId();
        $partialRootPaths = $this->getContentBlocksPartialRootPaths($contentBlockPrivatePath, $pageUid);
        $layoutRootPaths = $this->getContentBlocksLayoutRootPaths($contentBlockPrivatePath, $pageUid);
        $viewFactoryData = new ViewFactoryData(
            partialRootPaths: $partialRootPaths,
            layoutRootPaths: $layoutRootPaths,
            templatePathAndFilename: $contentBlockPrivatePath . '/' . ContentBlockPathUtility::getBackendPreviewFileName(),
            request: $request
        );
        return $this->viewFactory->create($viewFactoryData);
    }

    /**
     * @return array<int, string>
     */
    protected function getContentBlocksPartialRootPaths(string $contentBlockPrivatePath, int $pageUid): array
    {
        $contentBlockPartialRootPaths = $this->rootPathsSettings->getContentBlocksPartialRootPaths($pageUid);
        $partialRootPaths = [
            'EXT:backend/Resources/Private/Partials/',
            'EXT:content_blocks/Resources/Private/Partials/',
            ...$contentBlockPartialRootPaths,
            $contentBlockPrivatePath . '/Partials/',
        ];
        return $partialRootPaths;
    }

    /**
     * @return array<int, string>
     */
    protected function getContentBlocksLayoutRootPaths(string $contentBlockPrivatePath, int $pageUid): array
    {
        $layoutRootPaths = $this->rootPathsSettings->getContentBlocksLayoutRootPaths($pageUid);
        $layoutRootPaths[] = $contentBlockPrivatePath . '/Layouts/';
        return $layoutRootPaths;
    }
}
