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
use TYPO3\CMS\ContentBlocks\DataProcessing\ContentBlocksDataProcessor;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\ContentBlocks\RelationResolver;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Sets up Fluid and applies the same DataProcessor as the frontend to the data record.
 * Wraps the backend preview in class="cb-editor".
 */
class PreviewRenderer extends StandardContentPreviewRenderer
{
    public function __construct(
        protected ContentObjectRenderer $cObj,
        protected ContentBlocksDataProcessor $cbProcessor,
        protected TableDefinitionCollection $tableDefinitionCollection,
        protected RelationResolver $relationResolver,
    ) {
    }

    public function renderPageModulePreviewContent(GridColumnItem $item): string
    {
        $record = $item->getRecord();

        $contentElementDefinition = $this->tableDefinitionCollection->getContentElementDefinition($record['CType']);
        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setTemplateRootPaths([ContentBlockPathUtility::getAbsoluteContentBlocksPrivatePath($contentElementDefinition->getPackage())]);
        $view->setTemplate('EditorPreview');
        $view->assign('data', $record);

        $ttContentDefinition = $this->tableDefinitionCollection->getTable('tt_content');
        $contentBlockData = [];
        foreach ($contentElementDefinition->getColumns() as $column) {
            $tcaFieldDefinition = $ttContentDefinition->getTcaColumnsDefinition()->getField($column);
            if (!$tcaFieldDefinition->getFieldType()->isRenderable()) {
                continue;
            }
            $contentBlockData[$tcaFieldDefinition->getIdentifier()] = $this->relationResolver->processField($tcaFieldDefinition, $record, 'tt_content', $contentElementDefinition);
        }
        $view->assign('cb', $contentBlockData);

        return '<div class="cb-editor">' . $view->render() . '</div>';
    }

    public function wrapPageModulePreview(string $previewHeader, string $previewContent, GridColumnItem $item): string
    {
        return $previewHeader . $previewContent;
    }
}
