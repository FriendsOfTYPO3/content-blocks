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

class ContentBlocksDataProcessor implements DataProcessorInterface
{
    public function __construct(
        protected readonly TableDefinitionCollection $tableDefinitionCollection,
        protected readonly RelationResolver $relationResolver,
    ) {}

    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array {
        $this->relationResolver->setRequest($cObj->getRequest());
        $table = $cObj->getCurrentTable();
        $tableDefinition = $this->tableDefinitionCollection->getTable($table);
        $contentTypeDefinition = ContentTypeResolver::resolve($tableDefinition, $processedData['data']);
        if ($contentTypeDefinition === null) {
            return $processedData;
        }
        $contentBlockDataResolver = new ContentBlockDataResolver(new GridFactory(), $this->relationResolver, $this->tableDefinitionCollection);
        $processedData['data'] = $contentBlockDataResolver->buildContentBlockDataObjectRecursive(
            $contentTypeDefinition,
            $tableDefinition,
            $processedData['data'],
            $table
        );

        return $processedData;
    }
}
