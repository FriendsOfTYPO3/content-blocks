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
final class ContentBlockDataDecorator
{
    public function __construct(
        private readonly TableDefinitionCollection $tableDefinitionCollection,
    ) {}

    public function decorate(
        ContentTypeInterface $contentTypeDefinition,
        TableDefinition $tableDefinition,
        array $rawData,
        array $resolvedData,
        string $table,
    ): ContentBlockData {
        $resolvedRelation = new ResolvedRelation();
        $resolvedRelation->raw = $rawData;
        $resolvedRelation->resolved = $resolvedData;
        $contentBlockData = $this->buildContentBlockDataObjectRecursive(
            $contentTypeDefinition,
            $tableDefinition,
            $resolvedRelation,
            $table,
        );
        return $contentBlockData;
    }

    private function buildContentBlockDataObjectRecursive(
        ContentTypeInterface $contentTypeDefinition,
        TableDefinition $tableDefinition,
        ResolvedRelation $resolvedRelation,
        string $table,
        $depth = 0
    ): ContentBlockData {
        $processedContentBlockData = [];
        foreach ($contentTypeDefinition->getColumns() as $column) {
            $tcaFieldDefinition = $tableDefinition->getTcaFieldDefinitionCollection()->getField($column);
            if (!$tcaFieldDefinition->getFieldType()->isRenderable()) {
                continue;
            }
            $resolvedField = $resolvedRelation->resolved[$tcaFieldDefinition->getUniqueIdentifier()];
            $transformedRelation = null;
            if (
                is_array($resolvedField)
                && $tcaFieldDefinition->getFieldType()->isRelation()
                && $this->getForeignTable($tcaFieldDefinition, $table) !== ''
            ) {
                $transformedRelation = match ($tcaFieldDefinition->getFieldType()) {
                    FieldType::COLLECTION,
                    FieldType::RELATION => $this->transformMultipleRelation(
                        $resolvedField,
                        $tcaFieldDefinition,
                        $table,
                        $depth,
                    ),
                    FieldType::SELECT => $this->transformSelectRelation(
                        $resolvedField,
                        $tcaFieldDefinition,
                        $table,
                        $depth,
                    ),
                    default => $resolvedField,
                };
            }
            $processedContentBlockData[$tcaFieldDefinition->getIdentifier()] = $transformedRelation ?? $resolvedField;
        }
        $resolvedRelation->resolved = $processedContentBlockData;
        $contentBlockDataObject = $this->buildContentBlockDataObject(
            $resolvedRelation,
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
        $resolvedRelation = new ResolvedRelation();
        // The associated table provided kindly by RelationResolver.
        if (isset($item['_table'])) {
            $foreignTable = $item['_table'];
            unset($item['_table']);
        }
        $resolvedRelation->raw = $item['_raw'];
        unset($item['_raw']);
        $resolvedRelation->resolved = $item;
        $hasTableDefinition = $this->tableDefinitionCollection->hasTable($foreignTable);
        $collectionTableDefinition = null;
        if ($hasTableDefinition) {
            $collectionTableDefinition = $this->tableDefinitionCollection->getTable($foreignTable);
        }
        $typeDefinition = null;
        if ($hasTableDefinition) {
            $typeDefinition = ContentTypeResolver::resolve($collectionTableDefinition, $resolvedRelation->raw);
        }
        if ($collectionTableDefinition !== null && $typeDefinition !== null) {
            $contentBlockData = $this->buildContentBlockDataObjectRecursive(
                $typeDefinition,
                $collectionTableDefinition,
                $resolvedRelation,
                $foreignTable,
                ++$depth,
            );
            return $contentBlockData;
        }
        $contentBlockData = $this->buildFakeContentBlockDataObject($foreignTable, $resolvedRelation);
        return $contentBlockData;
    }

    private function buildContentBlockDataObject(
        ResolvedRelation $resolvedRelation,
        string $table,
        ?string $typeField,
        string|int $typeName,
        string $name = '',
    ): ContentBlockData {
        $rawData = $resolvedRelation->raw;
        $resolvedData = $resolvedRelation->resolved;
        $baseData = [
            'uid' => $rawData['uid'],
            'pid' => $rawData['pid'],
            'tableName' => $table,
            'typeName' => $typeName,
        ];
        // Duplicates typeName, but needed for Fluid Styled Content layout integration.
        if ($typeField !== null) {
            $baseData[$typeField] = $typeName;
        }
        if (array_key_exists('sys_language_uid', $rawData)) {
            $baseData['languageId'] = $rawData['sys_language_uid'];
        }
        if (array_key_exists('tstamp', $rawData)) {
            $baseData['updateDate'] = $rawData['tstamp'];
        }
        if (array_key_exists('crdate', $rawData)) {
            $baseData['creationDate'] = $rawData['crdate'];
        }
        $baseData = $this->enrichBaseDataWithComputedProperties($baseData, $rawData);
        $contentBlockDataArray = $baseData + $resolvedData;
        $contentBlockData = new ContentBlockData($name, $rawData, $contentBlockDataArray);

        // Add dynamic fields so that Fluid can detect them with `property_exists()`.
        foreach ($baseData as $key => $baseDataItem) {
            $contentBlockData->$key = $baseDataItem;
        }
        foreach ($resolvedData as $key => $processedContentBlockDataItem) {
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
    private function buildFakeContentBlockDataObject(string $table, ResolvedRelation $resolvedRelation): ContentBlockData
    {
        $typeField = $this->resolveTypeField($table);
        $typeName = $typeField !== null ? $resolvedRelation->raw[$typeField] : '1';
        $fakeName = 'core/' . $typeName;
        $contentBlockDataObject = $this->buildContentBlockDataObject(
            $resolvedRelation,
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
