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
use TYPO3\CMS\ContentBlocks\Domain\Repository\ContentBlockConfigurationRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Sets up Fluid and applies the same DataProcessor as the frontend to the data record.
 * Wraps the backend preview in class="cb-editor".
 */
class PreviewRenderer extends StandardContentPreviewRenderer
{
    /**
     * @var ContentBlocksDataProcessor
     */
    protected $cbProcessor;

    /**
     * @var ContentObjectRenderer
     */
    protected $cObj;

    /**
     * @var ContentBlockConfigurationRepository
     */
    protected $configurationRepository;

    /**
     * @var CbContentProcessor
    */
    protected $contentProcessor;

    public function __construct(
        ContentObjectRenderer $cObj,
        ContentBlocksDataProcessor $cbProcessor,
        ContentBlockConfigurationRepository $configurationRepository
    ) {
        $this->cObj = $cObj;
        $this->cbProcessor = $cbProcessor;
        $this->configurationRepository = $configurationRepository;
    }

    /** render PageModule preview content
     *
     * @throws \Exception
     */
    public function renderPageModulePreviewContent(GridColumnItem $item): string
    {
        $record = $item->getRecord();

        $cbConfiguration = $this->configurationRepository->findContentBlockByCType($record['CType']);
        if ($cbConfiguration === null) {
            throw new \Exception(sprintf('It seems you try to render a ContentBlock which does not exists. The unknown CType is: %s. Reason: We couldn\'t find the composer package.', $record['CType']));
        }

        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setTemplatePathAndFilename($cbConfiguration->editorPreviewHtml);

        // TODO: use TypoScript configuration for paths
        // TODO: add partialRootPath to cbConf
        $view->setPartialRootPaths(
            [
                'EXT:content_blocks/Resources/Private/Partials/',
                $cbConfiguration->privatePath,
            ]
        );
        $view->setLayoutRootPaths(
            [
                'EXT:content_blocks/Resources/Private/Layouts/',
                $cbConfiguration->privatePath,
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
