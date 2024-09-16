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

use TYPO3\CMS\Backend\Controller\Event\ModifyPageLayoutContentEvent;
use TYPO3\CMS\Backend\Module\ModuleData;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\ContentBlocks\DataProcessing\ContentBlockDataDecorator;
use TYPO3\CMS\ContentBlocks\DataProcessing\ContentTypeResolver;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentTypeInterface;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\Core\Domain\RecordFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * @internal Not part of TYPO3's public API.
 */
readonly class PageLayout
{
    public function __construct(
        protected TableDefinitionCollection $tableDefinitionCollection,
        protected RecordFactory $recordFactory,
        protected ContentBlockRegistry $contentBlockRegistry,
        protected ContentBlockDataDecorator $contentBlockDataDecorator,
        protected RootPathsSettings $rootPathsSettings,
        protected ContentTypeResolver $contentTypeResolver,
    ) {}

    public function __invoke(ModifyPageLayoutContentEvent $event): void
    {
        $request = $event->getRequest();
        /** @var ModuleData $moduleData */
        $moduleData = $request->getAttribute('moduleData');
        $function = (int)($moduleData->get('function') ?? 0);
        if ($function !== 1) {
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
        if ($this->getEditorPreviewExtPath($contentTypeDefinition) === null) {
            return;
        }
        $contentBlockData = $this->contentBlockDataDecorator->decorate($resolvedRecord);
        $view = $this->createView($contentTypeDefinition, $pageUid);
        $view->setRequest($request);
        $view->assign('data', $contentBlockData);
        $renderedView = (string)$view->render();
        $event->addHeaderContent($renderedView);
    }

    protected function createView(ContentTypeInterface $contentTypeDefinition, int $pageUid): StandaloneView
    {
        $contentBlockPrivatePath = $this->getContentBlockPrivatePath($contentTypeDefinition);
        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setLayoutRootPaths($this->getContentBlocksLayoutRootPaths($contentBlockPrivatePath, $pageUid));
        $view->setPartialRootPaths($this->getContentBlocksPartialRootPaths($contentBlockPrivatePath, $pageUid));
        $view->setTemplateRootPaths([$contentBlockPrivatePath]);
        $view->setTemplate(ContentBlockPathUtility::getBackendPreviewFileNameWithoutExtension());
        return $view;
    }

    /**
     * @return array<int, string>
     */
    protected function getContentBlocksPartialRootPaths(string $contentBlockPrivatePath, int $pageUid): array
    {
        $partialRootPaths = $this->rootPathsSettings->getContentBlocksPartialRootPaths($pageUid);
        $partialRootPaths[] = $contentBlockPrivatePath . '/Partials/';
        return $partialRootPaths;
    }

    /**
     * @return array<int, string>
     */
    protected function getContentBlocksLayoutRootPaths(string $contentBlockPrivatePath, int $pageUid): array
    {
        $partialRootPaths = $this->rootPathsSettings->getContentBlocksLayoutRootPaths($pageUid);
        $partialRootPaths[] = $contentBlockPrivatePath . '/Layouts';
        return $partialRootPaths;
    }

    protected function getContentBlockPrivatePath(ContentTypeInterface $contentTypeDefinition): string
    {
        $contentBlockExtPath = $this->getEditorPreviewExtPath($contentTypeDefinition);
        $contentBlockPrivatePath = $contentBlockExtPath . '/' . ContentBlockPathUtility::getTemplatesFolder();
        return $contentBlockPrivatePath;
    }

    protected function getEditorPreviewExtPath(ContentTypeInterface $contentTypeDefinition): ?string
    {
        $contentBlockExtPath = $this->contentBlockRegistry->getContentBlockExtPath($contentTypeDefinition->getName());
        $editorPreviewExtPath = $contentBlockExtPath . '/' . ContentBlockPathUtility::getBackendPreviewPath();
        $editorPreviewAbsPath = GeneralUtility::getFileAbsFileName($editorPreviewExtPath);
        if (!file_exists($editorPreviewAbsPath)) {
            return null;
        }
        return $contentBlockExtPath;
    }
}
