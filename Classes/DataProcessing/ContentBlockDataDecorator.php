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
use TYPO3\CMS\ContentBlocks\FieldType\FieldType;
use TYPO3\CMS\ContentBlocks\Schema\Exception\UndefinedFieldException;
use TYPO3\CMS\ContentBlocks\Schema\Exception\UndefinedSchemaException;
use TYPO3\CMS\ContentBlocks\Schema\SimpleTcaSchemaFactory;

/**
 * @internal Not part of TYPO3's public API.
 */
final class ContentBlockDataDecorator
{
    public function __construct(
        private readonly GridFactory $gridFactory,
        private readonly TableDefinitionCollection $tableDefinitionCollection,
        private readonly SimpleTcaSchemaFactory $simpleTcaSchemaFactory,
        private readonly ContentBlockDataDecoratorSession $contentBlockDataDecoratorSession,
    ) {}

    public function decorate(
        ContentTypeInterface $contentTypeDefinition,
        TableDefinition $tableDefinition,
        ResolvedRelation $resolvedRelation,
        ?PageLayoutContext $context = null,
    ): ContentBlockData {
        $identifier = $this->getRecordIdentifier($resolvedRelation->table, $resolvedRelation->raw);
        $this->contentBlockDataDecoratorSession->addContentBlockData($identifier, new ContentBlockData());
        $resolvedContentBlockDataRelation = new ResolvedContentBlockDataRelation();
        $resolvedContentBlockDataRelation->raw = $resolvedRelation->raw;
        $resolvedContentBlockDataRelation->resolved = $resolvedRelation->resolved;
        $contentBlockData = $this->buildContentBlockDataObjectRecursive(
            $contentTypeDefinition,
            $tableDefinition,
            $resolvedContentBlockDataRelation,
            $resolvedRelation->table,
            0,
            $context,
        );
        $this->contentBlockDataDecoratorSession->setContentBlockData($identifier, $contentBlockData);
        return $contentBlockData;
    }

    private function buildContentBlockDataObjectRecursive(
        ContentTypeInterface $contentTypeDefinition,
        TableDefinition $tableDefinition,
        ResolvedContentBlockDataRelation $resolvedRelation,
        string $table,
        int $depth = 0,
        ?PageLayoutContext $context = null,
    ): ContentBlockData {
        $processedContentBlockData = [];
        $grids = [];
        foreach ($contentTypeDefinition->getColumns() as $column) {
            $tcaFieldDefinition = $tableDefinition->getTcaFieldDefinitionCollection()->getField($column);
            $fieldType = $tcaFieldDefinition->getFieldType();
            $fieldTypeEnum = FieldType::tryFrom($fieldType::getName());
            if ($fieldTypeEnum->isStructureField()) {
                continue;
            }
            $resolvedField = $resolvedRelation->resolved[$tcaFieldDefinition->getUniqueIdentifier()];
            if ($this->isRelationField($resolvedField, $tcaFieldDefinition, $table)) {
                $resolvedField = $this->handleRelation(
                    $resolvedRelation,
                    $tcaFieldDefinition,
                    $depth,
                    $context,
                );
                if ($context !== null) {
                    $grids = $this->processGrid($context, $tcaFieldDefinition, $resolvedField, $table, $grids);
                }
            }
            $processedContentBlockData[$tcaFieldDefinition->getIdentifier()] = $resolvedField;
        }
        $resolvedRelation->resolved = $processedContentBlockData;
        $contentBlockDataObject = $this->buildContentBlockDataObject(
            $resolvedRelation,
            $tableDefinition->getTable(),
            $tableDefinition->getTypeField(),
            $contentTypeDefinition->getTypeName(),
            $contentTypeDefinition->getName(),
            $grids,
        );
        return $contentBlockDataObject;
    }

    private function handleRelation(
        ResolvedContentBlockDataRelation $resolvedRelation,
        TcaFieldDefinition $tcaFieldDefinition,
        int $depth,
        ?PageLayoutContext $context = null,
    ): mixed {
        $resolvedField = $resolvedRelation->resolved[$tcaFieldDefinition->getUniqueIdentifier()];
        $fieldTypeName = $tcaFieldDefinition->getFieldType()->getName();
        $fieldTypeEnum = FieldType::tryFrom($fieldTypeName);
        $resolvedField = match ($fieldTypeEnum) {
            FieldType::COLLECTION,
            FieldType::RELATION => $this->transformMultipleRelation(
                $resolvedField,
                $depth,
                $context,
            ),
            FieldType::SELECT => $this->transformSelectRelation(
                $resolvedField,
                $depth,
                $context,
            ),
            default => $resolvedField,
        };
        return $resolvedField;
    }

    private function isRelationField(mixed $resolvedField, TcaFieldDefinition $tcaFieldDefinition, string $table): bool
    {
        if ($resolvedField instanceof ResolvedRelation) {
            return true;
        }
        if (!is_array($resolvedField)) {
            return false;
        }
        $fieldType = $tcaFieldDefinition->getFieldType();
        $relationTcaTypes = ['inline', 'select', 'group'];
        if (!in_array($fieldType::getTcaType(), $relationTcaTypes, true)) {
            return false;
        }
        if ($this->getRelationTable($tcaFieldDefinition, $table) === '') {
            return false;
        }
        return true;
    }

    /**
     * @param ResolvedRelation[] $processedField
     * @return array<ContentBlockData>|ContentBlockData
     */
    private function transformSelectRelation(
        array|ResolvedRelation $processedField,
        int $depth,
        ?PageLayoutContext $context = null,
    ): array|ContentBlockData {
        if ($processedField instanceof ResolvedRelation) {
            $processedField = $this->transformSingleRelation(
                $processedField,
                $depth,
                $context,
            );
        } else {
            $processedField = $this->transformMultipleRelation(
                $processedField,
                $depth,
                $context,
            );
        }
        return $processedField;
    }

    /**
     * @return array<ContentBlockData>
     */
    private function transformMultipleRelation(
        array $processedField,
        int $depth,
        ?PageLayoutContext $context = null,
    ): array {
        foreach ($processedField as $key => $processedFieldItem) {
            $processedField[$key] = $this->transformSingleRelation(
                $processedFieldItem,
                $depth,
                $context,
            );
        }
        return $processedField;
    }

    private function transformSingleRelation(
        ResolvedRelation $item,
        int $depth,
        ?PageLayoutContext $context = null,
    ): ContentBlockData {
        $contentBlockRelation = new ResolvedContentBlockDataRelation();
        $foreignTable = $item->table;
        $contentBlockRelation->raw = $item->raw;
        $contentBlockRelation->resolved = $item->resolved;
        $hasTableDefinition = $this->tableDefinitionCollection->hasTable($foreignTable);
        $collectionTableDefinition = null;
        if ($hasTableDefinition) {
            $collectionTableDefinition = $this->tableDefinitionCollection->getTable($foreignTable);
        }
        $typeDefinition = null;
        if ($hasTableDefinition) {
            $typeDefinition = ContentTypeResolver::resolve($collectionTableDefinition, $contentBlockRelation->raw);
        }
        if ($collectionTableDefinition !== null && $typeDefinition !== null) {
            $identifier = $this->getRecordIdentifier($foreignTable, $contentBlockRelation->raw);
            if ($this->contentBlockDataDecoratorSession->hasContentBlockData($identifier)) {
                $contentBlockData = $this->contentBlockDataDecoratorSession->getContentBlockData($identifier);
                return $contentBlockData;
            }
            $this->contentBlockDataDecoratorSession->addContentBlockData($identifier, new ContentBlockData());
            $contentBlockData = $this->buildContentBlockDataObjectRecursive(
                $typeDefinition,
                $collectionTableDefinition,
                $contentBlockRelation,
                $foreignTable,
                ++$depth,
                $context,
            );
            $this->contentBlockDataDecoratorSession->setContentBlockData($identifier, $contentBlockData);
            return $contentBlockData;
        }
        $contentBlockData = $this->buildFakeContentBlockDataObject($foreignTable, $contentBlockRelation);
        return $contentBlockData;
    }

    /**
     * @param array<RelationGrid> $grids
     */
    private function buildContentBlockDataObject(
        ResolvedContentBlockDataRelation $resolvedRelation,
        string $table,
        ?string $typeField,
        string|int $typeName,
        string $name = '',
        array $grids = [],
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
        $contentBlockData = new ContentBlockData($name, $rawData, $grids, $contentBlockDataArray);

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
    private function buildFakeContentBlockDataObject(string $table, ResolvedContentBlockDataRelation $resolvedRelation): ContentBlockData
    {
        $tcaSchema = $this->simpleTcaSchemaFactory->get($table);
        $typeField = $tcaSchema->getTypeField();
        $typeFieldIdentifier = $typeField?->getName();
        $typeName = $typeField !== null ? $resolvedRelation->raw[$typeField->getName()] : '1';
        $fakeName = 'core/' . $typeName;
        $contentBlockDataObject = $this->buildContentBlockDataObject(
            $resolvedRelation,
            $table,
            $typeFieldIdentifier,
            $typeName,
            $fakeName,
        );
        return $contentBlockDataObject;
    }

    /**
     * @param ContentBlockData|array<ContentBlockData> $resolvedField
     * @param array<string, RelationGrid> $grids
     * @return array<string, RelationGrid>
     */
    private function processGrid(
        PageLayoutContext $context,
        TcaFieldDefinition $tcaFieldDefinition,
        ContentBlockData|array $resolvedField,
        string $table,
        array $grids,
    ): array {
        $foreignTable = $this->getRelationTable($tcaFieldDefinition, $table);
        if (!is_array($resolvedField)) {
            $resolvedField = [$resolvedField];
        }
        $gridLabel = $tcaFieldDefinition->getLabelPath();
        $grid = $this->gridFactory->build(
            $context,
            $gridLabel,
            $resolvedField,
            $foreignTable,
        );
        $relationGrid = new RelationGrid();
        $relationGrid->grid = $grid;
        $relationGrid->label = $gridLabel;
        $grids[$tcaFieldDefinition->getIdentifier()] = $relationGrid;
        return $grids;
    }

    private function getRelationTable(TcaFieldDefinition $tcaFieldDefinition, string $table): string
    {
        $tcaConfig = $tcaFieldDefinition->getTca();
        $relationTable = $tcaConfig['config']['foreign_table'] ?? $tcaConfig['config']['allowed'] ?? null;
        if ($relationTable !== null) {
            return $relationTable;
        }
        $relationTable = $this->getRelationTableNative($tcaFieldDefinition, $table);
        return $relationTable;
    }

    private function getRelationTableNative(TcaFieldDefinition $tcaFieldDefinition, string $table): string
    {
        try {
            $tcaSchema = $this->simpleTcaSchemaFactory->get($table);
        } catch (UndefinedSchemaException) {
            return '';
        }
        try {
            $tcaField = $tcaSchema->getField($tcaFieldDefinition->getUniqueIdentifier());
        } catch (UndefinedFieldException) {
            return '';
        }
        $tcaConfig = $tcaField->getColumnConfig();
        $relationTable = $tcaConfig['config']['foreign_table'] ?? $tcaConfig['config']['allowed'] ?? null;
        if ($relationTable !== null) {
            return $relationTable;
        }
        return '';
    }

    private function getRecordIdentifier(string $table, array $record): string
    {
        // @todo remove _PAGES_OVERLAY_UID in v13.
        $identifier = $table . '-' . (
            $record['_PAGES_OVERLAY_UID']
            ?? $record['_LOCALIZED_UID']
            ?? $record['uid']
        );
        return $identifier;
    }
}
