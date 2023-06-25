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

namespace TYPO3\CMS\ContentBlocks\DataProcessing;

use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

/**
 * Adds information about the current content block to variable "cb".
 *
 * @internal Not part of TYPO3's public API.
 */
class ContentBlocksDataProcessor implements DataProcessorInterface
{
    public function __construct(
        protected readonly TableDefinitionCollection $tableDefinitionCollection,
        protected readonly RelationResolver $relationResolver,
    ) {
    }

    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array {
        $this->relationResolver->setRequest($cObj->getRequest());
        $ttContentDefinition = $this->tableDefinitionCollection->getTable('tt_content');
        $contentElementDefinition = $this->tableDefinitionCollection->getContentElementDefinition($processedData['data']['CType']);

        $contentBlockData = [];
        foreach ($contentElementDefinition->getColumns() as $column) {
            $tcaFieldDefinition = $ttContentDefinition->getTcaColumnsDefinition()->getField($column);
            if (!$tcaFieldDefinition->getFieldType()->isRenderable()) {
                continue;
            }
            $contentBlockData['cb'][$tcaFieldDefinition->getIdentifier()] = $this->relationResolver->processField($tcaFieldDefinition, $contentElementDefinition, $processedData['data'], 'tt_content');
        }

        return array_merge($processedData, $contentBlockData);
    }
}
