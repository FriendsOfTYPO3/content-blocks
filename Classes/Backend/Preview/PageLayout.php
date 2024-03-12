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

namespace TYPO3\CMS\Contentblocks\Backend\Preview;

use TYPO3\CMS\Backend\Controller\Event\ModifyPageLayoutContentEvent;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\ContentBlocks\DataProcessing\ContentBlockData;
use TYPO3\CMS\ContentBlocks\DataProcessing\ContentBlockDataDecorator;
use TYPO3\CMS\ContentBlocks\DataProcessing\ContentTypeResolver;
use TYPO3\CMS\ContentBlocks\DataProcessing\RelationResolver;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentTypeInterface;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinition;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

final class PageLayout
{
    public function __construct(
        protected TableDefinitionCollection $tableDefinitionCollection,
        protected RelationResolver $relationResolver,
        protected ContentBlockRegistry $contentBlockRegistry,
        protected ContentBlockDataDecorator $contentBlockDataDecorator,
    ) {}

    public function __invoke(
        ModifyPageLayoutContentEvent $event
    ): void {
        $request = $event->getRequest();
        $moduleData = $request->getAttribute('moduleData');
        if (($moduleData->get('function') ?? 0) !== 1) {
            return;
        }

        $pageRow = BackendUtility::getRecord('pages', (int)($request->getQueryParams()['id'] ?? '0'));
        $tableDefinition = $this->tableDefinitionCollection->getTable('pages');
        $contentTypeDefinition = ContentTypeResolver::resolve(
            $tableDefinition,
            $pageRow
        );
        if ($contentTypeDefinition === null) {
            return;
        }

        $view = $this->createView($contentTypeDefinition);
        if ($view === null) {
            return;
        }
        $view->setRequest($request);
        $view->assign('data', $this->resolveData($contentTypeDefinition, $tableDefinition, $pageRow));

        $event->addHeaderContent($view->render());
    }

    private function resolveData(ContentTypeInterface $contentTypeDefinition, TableDefinition $tableDefinition, array $pageRow): ContentBlockData
    {
        $resolvedData = $this->relationResolver->resolve(
            $contentTypeDefinition,
            $tableDefinition,
            $pageRow,
            'pages',
        );

        return $this->contentBlockDataDecorator->decorate(
            $contentTypeDefinition,
            $tableDefinition,
            $pageRow,
            $resolvedData,
            'pages'
        );
    }

    private function createView(ContentTypeInterface $contentTypeDefinition): ?StandaloneView
    {
        $contentBlockExtPath = $this->contentBlockRegistry->getContentBlockExtPath($contentTypeDefinition->getName());
        $contentBlockPrivatePath = $contentBlockExtPath . '/' . ContentBlockPathUtility::getPrivateFolder();

        $editorPreviewExtPath = $contentBlockExtPath . '/' . ContentBlockPathUtility::getBackendPreviewPath();
        $editorPreviewAbsPath = GeneralUtility::getFileAbsFileName($editorPreviewExtPath);
        if (!file_exists($editorPreviewAbsPath)) {
            return null;
        }

        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setLayoutRootPaths([$contentBlockPrivatePath . '/Layouts']);
        $view->setPartialRootPaths([
            'EXT:backend/Resources/Private/Partials/',
            'EXT:content_blocks/Resources/Private/Partials/',
            $contentBlockPrivatePath . '/Partials/',
        ]);
        $view->setTemplateRootPaths([$contentBlockPrivatePath]);
        $view->setTemplate(ContentBlockPathUtility::getBackendPreviewFileNameWithoutExtension());

        return $view;
    }
}
