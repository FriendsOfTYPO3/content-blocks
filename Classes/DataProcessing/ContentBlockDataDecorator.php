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

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\View\PageLayoutContext;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentTypeInterface;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinition;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\ContentBlocks\Definition\TcaFieldDefinition;
use TYPO3\CMS\ContentBlocks\FieldType\FieldType;
use TYPO3\CMS\Core\Domain\RecordFactory;

/**
 * @internal Not part of TYPO3's public API.
 */
final class ContentBlockDataDecorator
{
    protected ?ServerRequestInterface $request = null;

    public function __construct(
        private readonly TableDefinitionCollection $tableDefinitionCollection,
        private readonly ContentBlockDataDecoratorSession $contentBlockDataDecoratorSession,
        private readonly GridProcessor $gridProcessor,
        private readonly ContentObjectProcessor $contentObjectProcessor,
        private readonly RecordFactory $recordFactory,
    ) {}

    public function setRequest(ServerRequestInterface $request): void
    {
        $this->request = $request;
        $this->contentObjectProcessor->setRequest($request);
    }

    public function decorate(
        ContentTypeInterface $contentTypeDefinition,
        TableDefinition $tableDefinition,
        ResolvedRelation $resolvedRelation,
        ?PageLayoutContext $context = null,
    ): ContentBlockData {
        $identifier = $this->getRecordIdentifier($resolvedRelation->table, $resolvedRelation->raw);
        $this->contentBlockDataDecoratorSession->addContentBlockData($identifier, new ContentBlockData());
        $record = $this->recordFactory->createFromDatabaseRow($resolvedRelation->table, $resolvedRelation->raw);
        $resolvedContentBlockDataRelation = new ResolvedContentBlockDataRelation();
        $resolvedContentBlockDataRelation->record = $record;
        $resolvedContentBlockDataRelation->resolved = $resolvedRelation->resolved;
        $contentBlockData = $this->buildContentBlockDataObjectRecursive(
            $contentTypeDefinition,
            $tableDefinition,
            $resolvedContentBlockDataRelation,
            0,
            $context,
        );
        $this->contentBlockDataDecoratorSession->setContentBlockData($identifier, $contentBlockData);
        $this->gridProcessor->process();
        $this->contentObjectProcessor->process();
        return $contentBlockData;
    }

    private function buildContentBlockDataObjectRecursive(
        ContentTypeInterface $contentTypeDefinition,
        TableDefinition $tableDefinition,
        ResolvedContentBlockDataRelation $resolvedRelation,
        int $depth = 0,
        ?PageLayoutContext $context = null,
    ): ContentBlockData {
        $processedContentBlockData = [];
        $grids = [];
        foreach ($contentTypeDefinition->getColumns() as $column) {
            $tcaFieldDefinition = $tableDefinition->getTcaFieldDefinitionCollection()->getField($column);
            $fieldType = $tcaFieldDefinition->getFieldType();
            $fieldTypeEnum = FieldType::tryFrom($fieldType::getName());
            if ($fieldTypeEnum?->isStructureField()) {
                continue;
            }
            $resolvedField = $resolvedRelation->resolved[$tcaFieldDefinition->getUniqueIdentifier()];
            if ($this->isRelationField($resolvedField)) {
                $resolvedField = $this->handleRelation(
                    $resolvedRelation,
                    $tcaFieldDefinition,
                    $depth,
                    $context,
                );
                $grids = $this->handleGrids($grids, $context, $resolvedField, $tcaFieldDefinition);
            }
            $processedContentBlockData[$tcaFieldDefinition->getIdentifier()] = $resolvedField;
        }
        $resolvedRelation->resolved = $processedContentBlockData;
        $contentBlockDataObject = $this->buildContentBlockDataObject(
            $resolvedRelation,
            $contentTypeDefinition->getName(),
            $grids,
        );
        return $contentBlockDataObject;
    }

    /**
     * @param array<string, RelationGrid>|array<string, RenderedGridItem[]> $grids
     * @return array<string, RelationGrid>|array<string, RenderedGridItem[]>
     */
    private function handleGrids(
        array $grids,
        ?PageLayoutContext $context,
        mixed $resolvedField,
        TcaFieldDefinition $tcaFieldDefinition
    ): array {
        if ($context === null && $this->request !== null) {
            $renderedGridItemDataObjects = $resolvedField;
            if (!is_array($renderedGridItemDataObjects)) {
                $renderedGridItemDataObjects = [$renderedGridItemDataObjects];
            }
            foreach ($renderedGridItemDataObjects as $contentBlockDataObject) {
                $renderedGridItem = new RenderedGridItem();
                $grids[$tcaFieldDefinition->getIdentifier()][] = $renderedGridItem;
                $callback = function () use ($contentBlockDataObject, $renderedGridItem): void {
                    $this->contentObjectProcessor->processContentObject(
                        $contentBlockDataObject,
                        $renderedGridItem
                    );
                };
                $this->contentObjectProcessor->addInstruction($callback);
            }
        }
        if ($context !== null) {
            $relationGrid = new RelationGrid();
            $grids[$tcaFieldDefinition->getIdentifier()] = $relationGrid;
            $callback = function () use ($grids, $tcaFieldDefinition, $resolvedField, $context): void {
                $relationGrid = $grids[$tcaFieldDefinition->getIdentifier()];
                $this->gridProcessor->processGrid(
                    $relationGrid,
                    $context,
                    $tcaFieldDefinition,
                    $resolvedField
                );
            };
            $this->gridProcessor->addInstruction($callback);
        }
        return $grids;
    }

    private function handleRelation(
        ResolvedContentBlockDataRelation $resolvedRelation,
        TcaFieldDefinition $tcaFieldDefinition,
        int $depth,
        ?PageLayoutContext $context = null,
    ): mixed {
        $resolvedField = $resolvedRelation->resolved[$tcaFieldDefinition->getUniqueIdentifier()];
        $fieldType = $tcaFieldDefinition->getFieldType();
        $resolvedField = match ($fieldType::getTcaType()) {
            'inline', 'group', 'category' => $this->transformMultipleRelation(
                $resolvedField,
                $depth,
                $context,
            ),
            'select' => $this->transformSelectRelation(
                $resolvedField,
                $depth,
                $context,
            ),
            default => $resolvedField,
        };
        return $resolvedField;
    }

    private function isRelationField(mixed $resolvedField): bool
    {
        if ($resolvedField instanceof ResolvedRelation) {
            return true;
        }
        if (!is_array($resolvedField)) {
            return false;
        }
        if (($resolvedField[0] ?? null) instanceof ResolvedRelation) {
            return true;
        }
        return false;
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
        $record = $this->recordFactory->createFromDatabaseRow($item->table, $item->raw);
        $contentBlockRelation->record = $record;
        $contentBlockRelation->resolved = $item->resolved;
        $hasTableDefinition = $this->tableDefinitionCollection->hasTable($foreignTable);
        $collectionTableDefinition = null;
        if ($hasTableDefinition) {
            $collectionTableDefinition = $this->tableDefinitionCollection->getTable($foreignTable);
        }
        $typeDefinition = null;
        if ($hasTableDefinition) {
            $typeDefinition = ContentTypeResolver::resolve($collectionTableDefinition, $contentBlockRelation->record->getRawRecord()->toArray());
        }
        if ($collectionTableDefinition !== null && $typeDefinition !== null) {
            $identifier = $this->getRecordIdentifier($foreignTable, $contentBlockRelation->record->toArray());
            if ($this->contentBlockDataDecoratorSession->hasContentBlockData($identifier)) {
                $contentBlockData = $this->contentBlockDataDecoratorSession->getContentBlockData($identifier);
                return $contentBlockData;
            }
            $this->contentBlockDataDecoratorSession->addContentBlockData($identifier, new ContentBlockData());
            $contentBlockData = $this->buildContentBlockDataObjectRecursive(
                $typeDefinition,
                $collectionTableDefinition,
                $contentBlockRelation,
                ++$depth,
                $context,
            );
            $this->contentBlockDataDecoratorSession->setContentBlockData($identifier, $contentBlockData);
            return $contentBlockData;
        }
        $contentBlockData = $this->buildFakeContentBlockDataObject($contentBlockRelation);
        return $contentBlockData;
    }

    /**
     * @param array<string, RelationGrid>|array<string, RenderedGridItem[]> $grids
     */
    private function buildContentBlockDataObject(
        ResolvedContentBlockDataRelation $resolvedRelation,
        string $name = '',
        array $grids = [],
    ): ContentBlockData {
        $resolvedData = $resolvedRelation->resolved;
        $contentBlockData = new ContentBlockData($resolvedRelation->record, $name, $grids, $resolvedData);
        return $contentBlockData;
    }

    /**
     * If the record is not defined by Content Blocks, we build a fake
     * Content Block data object for consistent usage.
     */
    private function buildFakeContentBlockDataObject(ResolvedContentBlockDataRelation $resolvedRelation): ContentBlockData
    {
        $typeName = $resolvedRelation->record->getRecordType() !== null ? $resolvedRelation->record->getRecordType() : '1';
        $fakeName = 'core/' . $typeName;
        $contentBlockDataObject = $this->buildContentBlockDataObject(
            $resolvedRelation,
            $fakeName,
        );
        return $contentBlockDataObject;
    }

    private function getRecordIdentifier(string $table, array $record): string
    {
        $identifier = $table . '-' . ($record['_LOCALIZED_UID'] ?? $record['uid']);
        return $identifier;
    }
}
