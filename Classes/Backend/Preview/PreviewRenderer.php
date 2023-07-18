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

use TYPO3\CMS\Backend\Preview\StandardContentPreviewRenderer;
use TYPO3\CMS\Backend\View\BackendLayout\Grid\GridColumnItem;
use TYPO3\CMS\ContentBlocks\DataProcessing\RelationResolver;
use TYPO3\CMS\ContentBlocks\Definition\ContentType;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
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
    ) {
    }

    public function renderPageModulePreviewContent(GridColumnItem $item): string
    {
        $record = $item->getRecord();
        $contentElementDefinition = $this->tableDefinitionCollection->getContentElementDefinition($record[ContentType::CONTENT_ELEMENT->getTypeField()]);
        $contentBlockPath = $this->contentBlockRegistry->getContentBlockPath($contentElementDefinition->getName());
        $contentBlockPrivatePath = $contentBlockPath . '/' . ContentBlockPathUtility::getPrivateFolderPath();

        // Fall back to standard preview rendering if EditorPreview.html does not exist.
        if (!file_exists(GeneralUtility::getFileAbsFileName($contentBlockPath . '/' . ContentBlockPathUtility::getBackendPreviewPath()))) {
            return parent::renderPageModulePreviewContent($item);
        }
        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setLayoutRootPaths([$contentBlockPrivatePath . '/Layouts']);
        $view->setPartialRootPaths([$contentBlockPrivatePath . '/Partials']);
        $view->setTemplateRootPaths([$contentBlockPrivatePath]);
        $view->setTemplate(ContentBlockPathUtility::getEditorInterfacePath());
        $view->setRequest($GLOBALS['TYPO3_REQUEST']);

        $contentElementTable = ContentType::CONTENT_ELEMENT->getTable();
        $contentElementTableDefinition = $this->tableDefinitionCollection->getTable($contentElementTable);
        $contentBlockData = [];
        foreach ($contentElementDefinition->getColumns() as $column) {
            $tcaFieldDefinition = $contentElementTableDefinition->getTcaColumnsDefinition()->getField($column);
            if (!$tcaFieldDefinition->getFieldType()->isRenderable()) {
                continue;
            }
            $contentBlockData[$tcaFieldDefinition->getIdentifier()] = $this->relationResolver->processField($tcaFieldDefinition, $contentElementDefinition, $record, $contentElementTable);
        }

        $view->assign('settings', ['name' => $contentElementDefinition->getName()]);
        $view->assign('data', $record);
        $view->assign('cb', $contentBlockData);

        return $view->render();
    }
}
