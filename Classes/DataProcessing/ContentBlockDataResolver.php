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

use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentTypeInterface;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinition;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\ContentBlocks\FieldConfiguration\FieldType;

/**
 * @internal Not part of TYPO3's public API.
 */
final class ContentBlockDataResolver
{
    public function __construct(
        private readonly RelationResolver $relationResolver,
        private readonly TableDefinitionCollection $tableDefinitionCollection,
    ) {}

    public function buildContentBlockDataObjectRecursive(
        ContentTypeInterface $contentTypeDefinition,
        TableDefinition $tableDefinition,
        array $data,
        string $table,
        $depth = 0
    ): ContentBlockData {
        $processedContentBlockData = [];
        foreach ($contentTypeDefinition->getColumns() as $column) {
            $tcaFieldDefinition = $tableDefinition->getTcaColumnsDefinition()->getField($column);
            if (!$tcaFieldDefinition->getFieldType()->isRenderable()) {
                continue;
            }
            // RelationResolver already processes the fields recursively. Run it only on root level.
            $processedField = $depth === 0
                ? $this->relationResolver->processField($tcaFieldDefinition, $contentTypeDefinition, $data, $table)
                : $data[$tcaFieldDefinition->getUniqueIdentifier()];

            if ($tcaFieldDefinition->getFieldType() === FieldType::COLLECTION) {
                // @todo add tests
                $collectionTable = $tcaFieldDefinition->getTca()['config']['foreign_table'];
                if (is_array($processedField) && $this->tableDefinitionCollection->hasTable($collectionTable)) {
                    $collectionTableDefinition = $this->tableDefinitionCollection->getTable($collectionTable);
                    foreach ($processedField as $key => $processedFieldItem) {
                        $typeName = $collectionTableDefinition->getTypeField()
                            ? $processedFieldItem[$collectionTableDefinition->getTypeField()]
                            : '1';
                        $typeDefinition = $collectionTableDefinition->getTypeDefinitionCollection()->getType($typeName);
                        $processedField[$key] = $this->buildContentBlockDataObjectRecursive(
                            $typeDefinition,
                            $collectionTableDefinition,
                            $processedFieldItem,
                            $collectionTable,
                            ++$depth
                        );
                    }
                }
            }
            // @todo add tests, renderType selectSingle without array.
            if ($tcaFieldDefinition->getFieldType() === FieldType::SELECT && ($tcaFieldDefinition->getTca()['config']['foreign_table'] ?? '') !== '') {
                $foreignTable = $tcaFieldDefinition->getTca()['config']['foreign_table'];
                if ($this->tableDefinitionCollection->hasTable($foreignTable)) {
                    $foreignTableDefinition = $this->tableDefinitionCollection->getTable($foreignTable);
                    foreach ($processedField as $key => $processedFieldItem) {
                        $typeName = $foreignTableDefinition->getTypeField()
                            ? $processedFieldItem[$foreignTableDefinition->getTypeField()]
                            : '1';
                        $typeDefinition = $foreignTableDefinition->getTypeDefinitionCollection()->getType($typeName);
                        $processedField[$key] = $this->buildContentBlockDataObjectRecursive(
                            $typeDefinition,
                            $foreignTableDefinition,
                            $processedFieldItem,
                            $foreignTable,
                            ++$depth
                        );
                    }
                }
            }
            $processedContentBlockData[$tcaFieldDefinition->getIdentifier()] = $processedField;
        }

        return $this->buildContentBlockDataObject($data, $processedContentBlockData, $contentTypeDefinition);
    }

    private function buildContentBlockDataObject(
        array $data,
        array $processedContentBlockData,
        ContentTypeInterface $contentType,
    ): ContentBlockData {
        $baseData = [
            'uid' => $data['uid'],
            'pid' => $data['pid'],
            'typeName' => $contentType->getTypeName(),
            'tableName' => $contentType->getTable(),
        ];
        if (array_key_exists('sys_language_uid', $data)) {
            $baseData['languageId'] = $data['sys_language_uid'];
        }
        if (array_key_exists('tstamp', $data)) {
            $baseData['updateDate'] = $data['tstamp'];
        }
        if (array_key_exists('crdate', $data)) {
            $baseData['creationDate'] = $data['crdate'];
        }
        $baseData = $this->enrichBaseDataWithComputedProperties($baseData, $data);
        $contentBlockDataArray = $baseData + $processedContentBlockData;
        $contentBlockData = new ContentBlockData($contentType->getName(), $data, $contentBlockDataArray);

        // Add dynamic fields so that Fluid can detect them with `property_exists()`.
        foreach ($baseData as $key => $baseDataItem) {
            $contentBlockData->$key = $baseDataItem;
        }
        foreach ($processedContentBlockData as $key => $processedContentBlockDataItem) {
            $contentBlockData->$key = $processedContentBlockDataItem;
        }
        return $contentBlockData;
    }

    private function enrichBaseDataWithComputedProperties(array $baseData, array $data): array
    {
        $computedProperties = [
            'localizedUid' => '_LOCALIZED_UID',
            'originalUid' => '_ORIG_uid',
            'originalPid' => '_ORIG_pid',
        ];
        $baseDataWithComputedProperties = $baseData;
        foreach ($computedProperties as $key => $computedProperty) {
            if (array_key_exists($computedProperty, $data)) {
                $baseDataWithComputedProperties[$key] = $data[$computedProperty];
            }
        }
        return $baseDataWithComputedProperties;
    }
}
