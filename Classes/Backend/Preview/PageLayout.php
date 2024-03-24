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
use Symfony\Component\VarExporter\VarExporter;
use TYPO3\CMS\Backend\Controller\Event\ModifyPageLayoutContentEvent;
use TYPO3\CMS\Backend\Module\ModuleData;
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
use TYPO3\CMS\Core\Cache\Frontend\PhpFrontend;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * @internal Not part of TYPO3's public API.
 */
class PageLayout
{
    public function __construct(
        protected readonly TableDefinitionCollection $tableDefinitionCollection,
        protected readonly RelationResolver $relationResolver,
        protected readonly ContentBlockRegistry $contentBlockRegistry,
        protected readonly ContentBlockDataDecorator $contentBlockDataDecorator,
        protected readonly PhpFrontend $cache,
        protected readonly RootPathsSettings $rootPathsSettings,
        protected readonly ElementInformation $elementInformation,
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
        $tableDefinition = $this->tableDefinitionCollection->getTable($pageTypeTable);
        $contentTypeDefinitionCollection = $tableDefinition->getContentTypeDefinitionCollection();
        if ($contentTypeDefinitionCollection === null) {
            return;
        }
        $pageUid = (int)($request->getQueryParams()['id'] ?? 0);
        $pageRow = BackendUtility::getRecord($pageTypeTable, $pageUid);
        $contentTypeDefinition = ContentTypeResolver::resolve($tableDefinition, $pageRow);
        if ($contentTypeDefinition === null) {
            return;
        }
        if ($this->getEditorPreviewExtPath($contentTypeDefinition) === null) {
            return;
        }
        $contentBlockData = $this->getContentBlockData($pageRow, $request, $contentTypeDefinition, $tableDefinition);
        $view = $this->createView($contentTypeDefinition, $pageUid);
        $view->setRequest($request);
        $view->assign('data', $contentBlockData);
        $variables = $this->elementInformation->getVariables($pageRow);
        $view->assignMultiple($variables);
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
        $partialRootPaths = [
            'EXT:content_blocks/Resources/Private/Partials/',
            ...$this->rootPathsSettings->getContentBlocksPartialRootPaths($pageUid),
            $contentBlockPrivatePath . '/Partials/'
        ];
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
        $contentBlockPrivatePath = $contentBlockExtPath . '/' . ContentBlockPathUtility::getPrivateFolder();
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

    protected function getContentBlockData(
        ?array $pageRow,
        ServerRequestInterface $request,
        ContentTypeInterface $contentTypeDefinition,
        TableDefinition $tableDefinition,
    ): ContentBlockData {
        $pageTypeTable = 'pages';
        $cacheIdentifier = $pageTypeTable . '-' . $pageRow['uid'] . '-' . md5(json_encode($pageRow));
        if ($this->cache->has($cacheIdentifier)) {
            $resolvedData = $this->cache->require($cacheIdentifier);
        } else {
            $this->relationResolver->setRequest($request);
            $resolvedData = $this->relationResolver->resolve(
                $contentTypeDefinition,
                $tableDefinition,
                $pageRow,
                $pageTypeTable,
            );
            // Avoid flooding cache with redundant data.
            if ($resolvedData->resolved !== $pageRow) {
                $exported = 'return ' . VarExporter::export($resolvedData) . ';';
                $this->cache->set($cacheIdentifier, $exported);
            }
        }
        $contentBlockData = $this->contentBlockDataDecorator->decorate(
            $contentTypeDefinition,
            $tableDefinition,
            $resolvedData,
        );
        return $contentBlockData;
    }
}
