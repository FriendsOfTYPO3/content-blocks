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
use TYPO3\CMS\Backend\Controller\Event\ModifyPageLayoutContentEvent;
use TYPO3\CMS\Backend\Module\ModuleData;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\View\PageViewMode;
use TYPO3\CMS\ContentBlocks\DataProcessing\ContentBlockDataDecorator;
use TYPO3\CMS\ContentBlocks\DataProcessing\ContentTypeResolver;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentTypeInterface;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Domain\RecordFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\View\ViewFactoryData;
use TYPO3\CMS\Core\View\ViewFactoryInterface;
use TYPO3\CMS\Core\View\ViewInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;

/**
 * @internal Not part of TYPO3's public API.
 */
#[AsEventListener(identifier: 'content-blocks-page-preview')]
readonly class PageLayout
{
    public function __construct(
        protected TableDefinitionCollection $tableDefinitionCollection,
        protected RecordFactory $recordFactory,
        protected ContentBlockRegistry $contentBlockRegistry,
        protected ContentBlockDataDecorator $contentBlockDataDecorator,
        protected RootPathsSettings $rootPathsSettings,
        protected ContentTypeResolver $contentTypeResolver,
        protected ViewFactoryInterface $viewFactory,
    ) {}

    public function __invoke(ModifyPageLayoutContentEvent $event): void
    {
        $request = $event->getRequest();
        /** @var ModuleData $moduleData */
        $moduleData = $request->getAttribute('moduleData');
        $viewMode = PageViewMode::LayoutView::tryFrom((int)$moduleData->get('viewMode'));
        if ($viewMode !== PageViewMode::LayoutView) {
            return;
        }
        $pageTypeTable = 'pages';
        if (!$this->tableDefinitionCollection->hasTable($pageTypeTable)) {
            return;
        }
        $pageUid = (int)($request->getQueryParams()['id'] ?? 0);
        $pageRow = BackendUtility::getRecord($pageTypeTable, $pageUid);
        if ($pageRow === null) {
            return;
        }
        $resolvedRecord = $this->recordFactory->createResolvedRecordFromDatabaseRow(
            $pageTypeTable,
            $pageRow,
        );
        $contentTypeDefinition = $this->contentTypeResolver->resolve($resolvedRecord);
        if ($contentTypeDefinition === null) {
            return;
        }
        if ($this->hasPreviewTemplate($contentTypeDefinition) === false) {
            return;
        }
        $contentBlockData = $this->contentBlockDataDecorator->decorate($resolvedRecord);
        $settings['_content_block_name'] = $contentBlockData->get('_name');
        $view = $this->createView($contentTypeDefinition, $pageUid, $request);
        $view->assign('data', $contentBlockData);
        $view->assign('settings', $settings);
        try {
            $renderedView = $view->render('backend-preview');
        } catch (Exception $exception) {
            $renderedView = '<div class="callout callout-danger">
    <div class="callout-content">
        <div class="callout-title">#' . $exception->getCode() . '</div>
        <div class="callout-body">' . $exception->getMessage() . '</div>
    </div>
</div>';
        }
        $event->addHeaderContent($renderedView);
    }

    protected function createView(ContentTypeInterface $contentTypeDefinition, int $pageUid, ServerRequestInterface $request): ViewInterface
    {
        $contentBlockExtPath = $this->getContentBlocksExtPath($contentTypeDefinition);
        $contentBlockTemplatePath = $contentBlockExtPath . '/' . ContentBlockPathUtility::getTemplatesFolder();
        $layoutRootPaths = $this->getContentBlocksLayoutRootPaths($contentBlockTemplatePath, $pageUid);
        $partialRootPaths = $this->getContentBlocksPartialRootPaths($contentBlockTemplatePath, $pageUid);
        $viewData = new ViewFactoryData(
            templateRootPaths: [$contentBlockTemplatePath],
            partialRootPaths: $partialRootPaths,
            layoutRootPaths: $layoutRootPaths,
            request: $request,
        );
        return $this->viewFactory->create($viewData);
    }

    /**
     * @return array<int, string>
     */
    protected function getContentBlocksPartialRootPaths(string $contentBlockTemplatePath, int $pageUid): array
    {
        $partialRootPaths = $this->rootPathsSettings->getContentBlocksPartialRootPaths($pageUid);
        $partialRootPaths[] = $contentBlockTemplatePath . '/partials/';
        return $partialRootPaths;
    }

    /**
     * @return array<int, string>
     */
    protected function getContentBlocksLayoutRootPaths(string $contentBlockTemplatePath, int $pageUid): array
    {
        $layoutRootPaths = $this->rootPathsSettings->getContentBlocksLayoutRootPaths($pageUid);
        $layoutRootPaths[] = $contentBlockTemplatePath . '/layouts/';
        return $layoutRootPaths;
    }

    protected function hasPreviewTemplate(ContentTypeInterface $contentTypeDefinition): bool
    {
        $contentBlockExtPath = $this->getContentBlocksExtPath($contentTypeDefinition);
        $backendPreviewPathHtml = ContentBlockPathUtility::getBackendPreviewPath();
        $backendPreviewPathDotFluid = ContentBlockPathUtility::getBackendPreviewPathDotFluid();
        foreach ([$backendPreviewPathDotFluid, $backendPreviewPathHtml] as $backendPreviewPath) {
            $editorPreviewExtPath = $contentBlockExtPath . '/' . $backendPreviewPath;
            $editorPreviewAbsPath = GeneralUtility::getFileAbsFileName($editorPreviewExtPath);
            if (file_exists($editorPreviewAbsPath)) {
                return true;
            }
        }
        return false;
    }

    protected function getContentBlocksExtPath(ContentTypeInterface $contentTypeDefinition): string
    {
        $contentBlockExtPath = $this->contentBlockRegistry->getContentBlockExtPath($contentTypeDefinition->getName());
        return $contentBlockExtPath;
    }
}
