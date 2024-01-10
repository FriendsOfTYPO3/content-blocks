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

use TYPO3\CMS\Backend\View\PageLayoutContext;
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
        private readonly GridFactory $gridFactory,
        private readonly RelationResolver $relationResolver,
        private readonly TableDefinitionCollection $tableDefinitionCollection,
    ) {}

    public function buildContentBlockDataObjectRecursive(
        ContentTypeInterface $contentTypeDefinition,
        TableDefinition $tableDefinition,
        array $data,
        string $table,
        int $depth = 0,
        ?PageLayoutContext $context = null
    ): ContentBlockData {
        $processedContentBlockData = [];
        $grids = [];
        foreach ($contentTypeDefinition->getColumns() as $column) {
            $tcaFieldDefinition = $tableDefinition->getTcaFieldDefinitionCollection()->getField($column);
            $fieldType = $tcaFieldDefinition->getFieldType();
            if (!$tcaFieldDefinition->getFieldType()->isRenderable()) {
                continue;
            }
            // RelationResolver already processes the fields recursively. Run it only on root level.
            $processedField = $depth === 0
                ? $this->relationResolver->processField($tcaFieldDefinition, $contentTypeDefinition, $data, $table)
                : $data[$tcaFieldDefinition->getUniqueIdentifier()];

            if (is_array($processedField) && $fieldType->isRelation()) {
                $processedField = match ($fieldType) {
                    FieldType::COLLECTION => $this->transformCollectionRelation($processedField, $tcaFieldDefinition, $table, $depth, $context),
                    FieldType::SELECT => $this->transformSelectRelation($processedField, $tcaFieldDefinition, $table, $depth, $context),
                    FieldType::RELATION => $this->transformRelationRelation($processedField, $tcaFieldDefinition, $table, $depth, $context),
                    default => $processedField,
                };
                if ($context !== null) {
                    $grids = $this->processGrid($context, $fieldType, $tcaFieldDefinition, $processedField, $grids);
                }
            }
            $processedContentBlockData[$tcaFieldDefinition->getIdentifier()] = $processedField;
        }

        return $this->buildContentBlockDataObject($data, $processedContentBlockData, $tableDefinition, $contentTypeDefinition, $grids);
    }

    private function transformCollectionRelation(array $processedField, TcaFieldDefinition $tcaFieldDefinition, string $table, int $depth, ?PageLayoutContext $context): array
    {
        $collectionTable = $tcaFieldDefinition->getTca()['config']['foreign_table'] ?? $GLOBALS['TCA'][$table]['columns'][$tcaFieldDefinition->getUniqueIdentifier()]['config']['foreign_table'] ?? '';
        if ($this->tableDefinitionCollection->hasTable($collectionTable)) {
            $collectionTableDefinition = $this->tableDefinitionCollection->getTable($collectionTable);
            foreach ($processedField as $key => $processedFieldItem) {
                $typeDefinition = ContentTypeResolver::resolve($collectionTableDefinition, $processedFieldItem);
                if ($typeDefinition === null) {
                    continue;
                }
                $processedField[$key] = $this->transformRelation($collectionTableDefinition, $typeDefinition, $processedFieldItem, $collectionTable, $depth, $context);
            }
        }
        return $processedField;
    }

    private function transformSelectRelation(array $processedField, TcaFieldDefinition $tcaFieldDefinition, string $table, int $depth, ?PageLayoutContext $context): array|ContentBlockData
    {
        if (($tcaFieldDefinition->getTca()['config']['renderType'] ?? '') === 'selectSingle') {
            $processedField = $this->transformSelectSingleRelation($processedField, $tcaFieldDefinition, $table, $depth, $context);
        } else {
            $processedField = $this->transformSelectMultipleRelation($processedField, $tcaFieldDefinition, $table, $depth, $context);
        }
        return $processedField;
    }

    private function transformSelectSingleRelation(array $processedField, TcaFieldDefinition $tcaFieldDefinition, string $table, int $depth, ?PageLayoutContext $context): array|ContentBlockData
    {
        $foreignTable = $tcaFieldDefinition->getTca()['config']['foreign_table'] ?? $GLOBALS['TCA'][$table]['columns'][$tcaFieldDefinition->getUniqueIdentifier()]['config']['foreign_table'] ?? '';
        if ($this->tableDefinitionCollection->hasTable($foreignTable)) {
            $foreignTableDefinition = $this->tableDefinitionCollection->getTable($foreignTable);
            $typeDefinition = ContentTypeResolver::resolve($foreignTableDefinition, $processedField);
            if ($typeDefinition !== null) {
                $processedField = $this->transformRelation($foreignTableDefinition, $typeDefinition, $processedField, $foreignTable, $depth, $context);
            }
        }
        return $processedField;
    }

    private function transformSelectMultipleRelation(array $processedField, TcaFieldDefinition $tcaFieldDefinition, string $table, int $depth, ?PageLayoutContext $context): array
    {
        $foreignTable = $tcaFieldDefinition->getTca()['config']['foreign_table'] ?? $GLOBALS['TCA'][$table]['columns'][$tcaFieldDefinition->getUniqueIdentifier()]['config']['foreign_table'] ?? '';
        if ($this->tableDefinitionCollection->hasTable($foreignTable)) {
            $foreignTableDefinition = $this->tableDefinitionCollection->getTable($foreignTable);
            foreach ($processedField as $key => $processedFieldItem) {
                $typeDefinition = ContentTypeResolver::resolve($foreignTableDefinition, $processedFieldItem);
                if ($typeDefinition === null) {
                    continue;
                }
                $processedField[$key] = $this->transformRelation($foreignTableDefinition, $typeDefinition, $processedFieldItem, $foreignTable, $depth, $context);
            }
        }
        return $processedField;
    }

    private function transformRelationRelation(array $processedField, TcaFieldDefinition $tcaFieldDefinition, string $table, int $depth, ?PageLayoutContext $context): array
    {
        $allowed = $tcaFieldDefinition->getTca()['config']['allowed'] ?? $GLOBALS['TCA'][$table]['columns'][$tcaFieldDefinition->getUniqueIdentifier()]['config']['allowed'] ?? '';
        // @todo what to do, if multiple tables are allowed? There is no way to find out, which record belongs to which table.
        if (!str_contains($allowed, ',') && $this->tableDefinitionCollection->hasTable($allowed)) {
            $foreignTableDefinition = $this->tableDefinitionCollection->getTable($allowed);
            foreach ($processedField as $key => $processedFieldItem) {
                $typeDefinition = ContentTypeResolver::resolve($foreignTableDefinition, $processedFieldItem);
                if ($typeDefinition === null) {
                    continue;
                }
                $processedField[$key] = $this->transformRelation($foreignTableDefinition, $typeDefinition, $processedFieldItem, $allowed, $depth, $context);
            }
        }
        return $processedField;
    }

    private function transformRelation(TableDefinition $tableDefinition, ContentTypeInterface $typeDefinition, array $fieldItem, string $foreignTable, int $depth, ?PageLayoutContext $context): ContentBlockData
    {
        $contentBlockData = $this->buildContentBlockDataObjectRecursive(
            $typeDefinition,
            $tableDefinition,
            $fieldItem,
            $foreignTable,
            ++$depth,
            $context,
        );
        return $contentBlockData;
    }

    /**
     * @param array<string, RelationGrid> $grids
     */
    private function buildContentBlockDataObject(
        array $data,
        array $processedContentBlockData,
        TableDefinition $tableDefinition,
        ContentTypeInterface $contentType,
        array $grids,
    ): ContentBlockData {
        $baseData = [
            'uid' => $data['uid'],
            'pid' => $data['pid'],
            'tableName' => $contentType->getTable(),
            'typeName' => $contentType->getTypeName(),
        ];
        // Duplicates typeName, but needed for Fluid Styled Content layout integration.
        if ($tableDefinition->hasTypeField()) {
            $baseData[$tableDefinition->getTypeField()] = $contentType->getTypeName();
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
        $contentBlockData = new ContentBlockData($contentType->getName(), $data, $grids, $contentBlockDataArray);

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
     * @param array<string, RelationGrid> $grids
     * @return array<string, RelationGrid>
     */
    private function processGrid(
        PageLayoutContext $context,
        FieldType $fieldType,
        TcaFieldDefinition $tcaFieldDefinition,
        ContentBlockData|array $processedField,
        array $grids
    ): array {
        if ($fieldType === FieldType::RELATION) {
            $tableName = (string)($tcaFieldDefinition->getFieldConfiguration()->getTca()['config']['allowed'] ?? '');
            // @todo Support for multi-table relation.
            if (str_contains($tableName, ',')) {
                $tableName = '';
            }
        } else {
            $tableName = (string)($tcaFieldDefinition->getFieldConfiguration()->getTca()['config']['foreign_table'] ?? '');
        }
        if ($tableName === '') {
            return $grids;
        }
        if (!is_array($processedField)) {
            $processedField = [$processedField];
        }

        $gridLabel = $tcaFieldDefinition->getLabelPath();

        $grid = $this->gridFactory->build(
            $context,
            $gridLabel,
            $processedField,
            $tableName
        );

        $relationGrid = new RelationGrid();
        $relationGrid->grid = $grid;
        $relationGrid->label = $gridLabel;
        $grids[$tcaFieldDefinition->getIdentifier()] = $relationGrid;
        return $grids;
    }
}
