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
use TYPO3\CMS\ContentBlocks\Definition\ContentElementDefinition;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Sets up Fluid and applies the same DataProcessor as the frontend to the data record.
 * Wraps the backend preview in class="cb-editor".
 */
class PreviewRenderer extends StandardContentPreviewRenderer
{
    protected ContentBlocksDataProcessor $cbProcessor;
    protected ContentObjectRenderer $cObj;
    protected TableDefinitionCollection $tableDefinitionCollection;

    public function __construct(
        ContentObjectRenderer $cObj,
        ContentBlocksDataProcessor $cbProcessor,
        TableDefinitionCollection $tableDefinitionCollection
    ) {
        $this->cObj = $cObj;
        $this->cbProcessor = $cbProcessor;
        $this->tableDefinitionCollection = $tableDefinitionCollection;
    }

    public function renderPageModulePreviewContent(GridColumnItem $item): string
    {
        $record = $item->getRecord();

        /** @var ContentElementDefinition $cbConfiguration */
        // @todo implement find by cType (and table).
//        $contentElementDefinition = $this->tableDefinitionCollection->findContentElementDefinition($record['CType']);
        return '';
        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setTemplatePathAndFilename($contentElementDefinition->getPrivatePath() . 'EditorPreview.html');

        // TODO: use TypoScript configuration for paths
        $view->setPartialRootPaths(
            [
                'EXT:content_blocks/Resources/Private/Partials/',
                $contentElementDefinition->getPrivatePath() . 'Partials/',
            ]
        );
        $view->setLayoutRootPaths(
            [
                'EXT:content_blocks/Resources/Private/Layouts/',
                $contentElementDefinition->getPrivatePath() . 'Layouts/',
            ]
        );

        $view->assign('data', $record);

        $processedData = ['data' => $record];
        // TODO use TypoScript configuration for DataProcessors
        // CB configuration & Database fields
        $processedData = $this->cbProcessor
            ->process(
                $this->cObj,
                [],
                [],
                $processedData
            );

        $view->assignMultiple($processedData);

        // TODO the wrapping class should go to a proper Fluid layout
        return '<div class="cb-editor">' . $view->render() . '</div>';
    }

    public function wrapPageModulePreview(
        string $previewHeader,
        string $previewContent,
        GridColumnItem $item
    ): string {
        return $previewHeader . $previewContent;
    }
}
