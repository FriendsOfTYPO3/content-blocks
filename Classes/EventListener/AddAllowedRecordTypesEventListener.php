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

namespace TYPO3\CMS\ContentBlocks\EventListener;

use TYPO3\CMS\Backend\View\Event\ManipulateBackendLayoutColPosConfigurationForPageEvent;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinition;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\ContentBlocks\FieldType\CollectionFieldType;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Utility\GeneralUtility;

#[AsEventListener('AddAllowedRecordTypesEventListener')]
readonly class AddAllowedRecordTypesEventListener
{
    public function __construct(
        protected TableDefinitionCollection $tableDefinitionCollection,
    ) {}

    public function __invoke(ManipulateBackendLayoutColPosConfigurationForPageEvent $event): void
    {
        $allowedContentTypes = $event->configuration['allowedContentTypes'] ?? '';
        if ($allowedContentTypes === '') {
            return;
        }
        $allowedContentTypesList = GeneralUtility::trimExplode(',', $allowedContentTypes, true);
        $contentElementTableName = ContentType::CONTENT_ELEMENT->getTable();
        if (!$this->tableDefinitionCollection->hasTable($contentElementTableName)) {
            return;
        }
        $contentElementTableDefinition = $this->tableDefinitionCollection->getTable($contentElementTableName);
        $allowedChildren = [];
        foreach ($allowedContentTypesList as $allowedContentType) {
            $allowedChildren[] = $this->findAllowedRecordTypes($contentElementTableDefinition, $allowedContentType);
        }
        $allowedContentTypesList = array_merge($allowedContentTypesList, ...$allowedChildren);
        $allowedContentTypesList = array_unique($allowedContentTypesList);
        $allowedContentTypesCsv = implode(',', $allowedContentTypesList);
        $event->configuration['allowedContentTypes'] = $allowedContentTypesCsv;
    }

    /**
     * @return array<string>
     */
    protected function findAllowedRecordTypes(TableDefinition $contentElementTableDefinition, string $typeName): array
    {
        $contentElementTableName = ContentType::CONTENT_ELEMENT->getTable();
        if (!$contentElementTableDefinition->contentTypeDefinitionCollection->hasType($typeName)) {
            return [];
        }
        $allowedChildren = [];
        $contentType = $contentElementTableDefinition->contentTypeDefinitionCollection->getType($typeName);
        foreach ($contentType->getOverrideColumns() as $column) {
            if ($column->fieldType instanceof CollectionFieldType === false) {
                continue;
            }
            if ($column->getTca()['config']['foreign_table'] !== $contentElementTableName) {
                continue;
            }
            $allowedChildren[] = $column->fieldType->getAllowedRecordTypes();
        }
        $allowedChildren = array_merge([], ...$allowedChildren);
        return $allowedChildren;
    }
}
