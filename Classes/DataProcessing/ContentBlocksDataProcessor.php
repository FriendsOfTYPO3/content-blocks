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

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\Core\Domain\RecordFactory;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

#[Autoconfigure(
    tags: [['name' => 'data.processor', 'identifier' => 'content-blocks']],
    public: true
)]
readonly class ContentBlocksDataProcessor implements DataProcessorInterface
{
    public function __construct(
        protected TableDefinitionCollection $tableDefinitionCollection,
        protected RecordFactory $recordFactory,
        protected ContentBlockDataDecorator $contentBlockDataDecorator,
        protected ContentTypeResolver $contentTypeResolver,
    ) {}

    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array {
        $request = $cObj->getRequest();
        $this->contentBlockDataDecorator->setRequest($request);
        $table = $cObj->getCurrentTable();
        // Fall back to ContentObjectRenderer->data if the key "data" is not populated.
        // This is the case, for example, for PAGEVIEW.
        $data = $processedData['data'] ?? $cObj->data;
        if ($data === []) {
            return $processedData;
        }
        $resolvedRecord = $this->recordFactory->createResolvedRecordFromDatabaseRow($table, $data);
        $contentBlockData = $this->contentBlockDataDecorator->decorate($resolvedRecord);
        $processedData['data'] = $contentBlockData;
        $processedData['settings']['_content_block_name'] = $contentBlockData->get('_name');
        return $processedData;
    }
}
