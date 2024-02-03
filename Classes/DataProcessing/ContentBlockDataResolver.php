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
use TYPO3\CMS\ContentBlocks\Definition\TcaFieldDefinition;
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
            $tcaFieldDefinition = $tableDefinition->getTcaFieldDefinitionCollection()->getField($column);
            if (!$tcaFieldDefinition->getFieldType()->isRenderable()) {
                continue;
            }
            // RelationResolver already processes the fields recursively. Run it only on root level.
            $processedField = $depth === 0
                ? $this->relationResolver->processField($tcaFieldDefinition, $contentTypeDefinition, $data, $table)
                : $data[$tcaFieldDefinition->getUniqueIdentifier()];

            if (is_array($processedField) && $tcaFieldDefinition->getFieldType()->isRelation()) {
                $processedField = match ($tcaFieldDefinition->getFieldType()) {
                    FieldType::COLLECTION,
                    FieldType::RELATION => $this->transformMultipleRelation(
                        $processedField,
                        $tcaFieldDefinition,
                        $table,
                        $depth,
                    ),
                    FieldType::SELECT => $this->transformSelectRelation(
                        $processedField,
                        $tcaFieldDefinition,
                        $table,
                        $depth,
                    ),
                    default => $processedField,
                };
            }
            $processedContentBlockData[$tcaFieldDefinition->getIdentifier()] = $processedField;
        }
        $contentBlockDataObject = $this->buildContentBlockDataObject(
            $data,
            $processedContentBlockData,
            $tableDefinition->getTable(),
            $tableDefinition->getTypeField(),
            $contentTypeDefinition->getTypeName(),
            $contentTypeDefinition->getName(),
        );
        return $contentBlockDataObject;
    }

    /**
     * @return array<ContentBlockData>|ContentBlockData
     */
    private function transformSelectRelation(
        array $processedField,
        TcaFieldDefinition $tcaFieldDefinition,
        string $table,
        int $depth
    ): array|ContentBlockData {
        if (($tcaFieldDefinition->getTca()['config']['renderType'] ?? '') === 'selectSingle') {
            $processedField = $this->transformSingleRelation($processedField, $tcaFieldDefinition, $table, $depth);
        } else {
            $processedField = $this->transformMultipleRelation($processedField, $tcaFieldDefinition, $table, $depth);
        }
        return $processedField;
    }

    /**
     * @return array<ContentBlockData>
     */
    private function transformMultipleRelation(
        array $processedField,
        TcaFieldDefinition $tcaFieldDefinition,
        string $table,
        int $depth
    ): array {
        foreach ($processedField as $key => $processedFieldItem) {
            $processedField[$key] = $this->transformSingleRelation($processedFieldItem, $tcaFieldDefinition, $table, $depth);
        }
        return $processedField;
    }

    private function transformSingleRelation(
        array $item,
        TcaFieldDefinition $tcaFieldDefinition,
        string $table,
        int $depth
    ): ContentBlockData {
        $foreignTable = $this->getForeignTable($tcaFieldDefinition, $table);
        // @todo what to do, if multiple tables are allowed? There is no way to find out, which record belongs to which table.
        if (str_contains($foreignTable, ',')) {
            throw new \InvalidArgumentException('Different tables in type Relation are not supported yet.', 1707000538);
        }
        $hasTableDefinition = $this->tableDefinitionCollection->hasTable($foreignTable);
        $collectionTableDefinition = null;
        if ($hasTableDefinition) {
            $collectionTableDefinition = $this->tableDefinitionCollection->getTable($foreignTable);
        }
        $typeDefinition = null;
        if ($hasTableDefinition) {
            $typeDefinition = ContentTypeResolver::resolve($collectionTableDefinition, $item);
        }
        if ($collectionTableDefinition !== null && $typeDefinition !== null) {
            return $this->buildContentBlockDataObjectRecursive(
                $typeDefinition,
                $collectionTableDefinition,
                $item,
                $foreignTable,
                ++$depth,
            );
        }
        $contentBlockDataObject = $this->buildFakeContentBlockDataObject($foreignTable, $item);
        return $contentBlockDataObject;
    }

    private function buildContentBlockDataObject(
        array $data,
        array $processedContentBlockData,
        string $table,
        ?string $typeField,
        string|int $typeName,
        string $name = '',
    ): ContentBlockData {
        $baseData = [
            'uid' => $data['uid'],
            'pid' => $data['pid'],
            'tableName' => $table,
            'typeName' => $typeName,
        ];
        // Duplicates typeName, but needed for Fluid Styled Content layout integration.
        if ($typeField !== null) {
            $baseData[$typeField] = $typeName;
        }
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
        $contentBlockData = new ContentBlockData($name, $data, $contentBlockDataArray);

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

    /**
     * If the record is not defined by Content Blocks, we build a fake
     * Content Block data object for consistent usage.
     */
    private function buildFakeContentBlockDataObject(string $table, array $record): ContentBlockData
    {
        $typeField = $this->resolveTypeField($table);
        $typeName = $typeField !== null ? $record[$typeField] : '1';
        $fakeName = 'core/' . $typeName;
        $contentBlockDataObject = $this->buildContentBlockDataObject(
            $record,
            $record,
            $table,
            $typeField,
            $typeName,
            $fakeName,
        );
        return $contentBlockDataObject;
    }

    private function resolveTypeField(string $table): ?string
    {
        $typeField = $GLOBALS['TCA'][$table]['ctrl']['type'] ?? null;
        return $typeField;
    }

    private function getForeignTable(TcaFieldDefinition $tcaFieldDefinition, string $table): string
    {
        $foreignTable = $tcaFieldDefinition->getTca()['config']['foreign_table']
            ?? $GLOBALS['TCA'][$table]['columns'][$tcaFieldDefinition->getUniqueIdentifier()]['config']['foreign_table']
            ?? '';
        if ($foreignTable === '') {
            $foreignTable = $this->getForeignTableAllowed($tcaFieldDefinition, $table);
        }
        return $foreignTable;
    }

    private function getForeignTableAllowed(TcaFieldDefinition $tcaFieldDefinition, string $table): string
    {
        $foreignTable = $tcaFieldDefinition->getTca()['config']['allowed']
            ?? $GLOBALS['TCA'][$table]['columns'][$tcaFieldDefinition->getUniqueIdentifier()]['config']['allowed']
            ?? '';
        return $foreignTable;
    }
}
