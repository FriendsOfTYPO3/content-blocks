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
use Symfony\Component\VarExporter\Exception\NotInstantiableTypeException;
use Symfony\Component\VarExporter\VarExporter;
use TYPO3\CMS\Backend\Preview\StandardContentPreviewRenderer;
use TYPO3\CMS\Backend\View\BackendLayout\Grid\GridColumnItem;
use TYPO3\CMS\ContentBlocks\DataProcessing\ContentBlockDataDecorator;
use TYPO3\CMS\ContentBlocks\DataProcessing\RelationResolver;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\Core\Cache\Frontend\PhpFrontend;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Sets up Fluid and applies the same DataProcessor as the frontend to the data record.
 *
 * @internal Not part of TYPO3's public API.
 */
class PreviewRenderer extends StandardContentPreviewRenderer
{
    public function __construct(
        protected TableDefinitionCollection $tableDefinitionCollection,
        protected RelationResolver $relationResolver,
        protected ContentBlockRegistry $contentBlockRegistry,
        protected ContentBlockDataDecorator $contentBlockDataDecorator,
        protected PhpFrontend $cache,
        protected RootPathsSettings $rootPathsSettings,
    ) {}

    public function renderPageModulePreviewContent(GridColumnItem $item): string
    {
        /** @var ServerRequestInterface $request */
        $request = $GLOBALS['TYPO3_REQUEST'];
        $record = $item->getRecord();
        $typeField = ContentType::CONTENT_ELEMENT->getTypeField();
        $contentElementTable = ContentType::CONTENT_ELEMENT->getTable();
        $cacheIdentifier = $contentElementTable . '-' . $record['uid'] . '-' . md5(json_encode($record));
        $typeName = $record[$typeField];
        $contentElementDefinition = $this->tableDefinitionCollection->getContentElementDefinition($typeName);
        $contentBlockExtPath = $this->contentBlockRegistry->getContentBlockExtPath($contentElementDefinition->getName());
        $contentBlockPrivatePath = $contentBlockExtPath . '/' . ContentBlockPathUtility::getPrivateFolder();

        // Fall back to standard preview rendering if EditorPreview.html does not exist.
        $editorPreviewExtPath = $contentBlockExtPath . '/' . ContentBlockPathUtility::getBackendPreviewPath();
        $editorPreviewAbsPath = GeneralUtility::getFileAbsFileName($editorPreviewExtPath);
        if (!file_exists($editorPreviewAbsPath)) {
            $result = parent::renderPageModulePreviewContent($item);
            return $result;
        }
        $contentElementTableDefinition = $this->tableDefinitionCollection->getTable($contentElementTable);
        if ($this->cache->has($cacheIdentifier)) {
            $resolvedData = $this->cache->require($cacheIdentifier);
        } else {
            $this->relationResolver->setRequest($request);
            $resolvedData = $this->relationResolver->resolve(
                $contentElementDefinition,
                $contentElementTableDefinition,
                $record,
                $contentElementTable,
            );
            // Avoid flooding cache with redundant data.
            if ($resolvedData->resolved !== $record) {
                try {
                    $exported = 'return ' . VarExporter::export($resolvedData) . ';';
                    $this->cache->set($cacheIdentifier, $exported);
                } catch (NotInstantiableTypeException) {
                    // @todo objects of class TYPO3\CMS\Core\Resource\File can't be exported
                    // @todo due to attached storage, which itself has EventDispatcher attached
                    // @todo which eventually leads to illegal Closures for EventListeners.
                    // @todo Right now, this happens for relations of type "folder".
                }
            }
        }
        $data = $this->contentBlockDataDecorator->decorate(
            $contentElementDefinition,
            $contentElementTableDefinition,
            $resolvedData,
            $item->getContext(),
        );
        $view = $this->createView($contentBlockPrivatePath, $request, $item);
        $view->assign('data', $data);
        $result = (string)$view->render();
        return $result;
    }

    protected function createView(
        string $contentBlockPrivatePath,
        ServerRequestInterface $request,
        GridColumnItem $item
    ): StandaloneView {
        $pageUid = $item->getContext()->getPageId();
        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setLayoutRootPaths($this->getContentBlocksLayoutRootPaths($contentBlockPrivatePath, $pageUid));
        $view->setPartialRootPaths($this->getContentBlocksPartialRootPaths($contentBlockPrivatePath, $pageUid));
        $view->setTemplateRootPaths([$contentBlockPrivatePath]);
        $view->setTemplate(ContentBlockPathUtility::getBackendPreviewFileNameWithoutExtension());
        $view->setRequest($request);
        return $view;
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
