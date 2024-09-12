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
use TYPO3\CMS\ContentBlocks\FieldType\PassFieldType;
use TYPO3\CMS\Core\Collection\LazyRecordCollection;
use TYPO3\CMS\Core\Domain\Record;
use TYPO3\CMS\Core\Domain\RecordInterface;

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
        private readonly ContentTypeResolver $contentTypeResolver,
    ) {}

    public function setRequest(ServerRequestInterface $request): void
    {
        $this->request = $request;
        $this->contentObjectProcessor->setRequest($request);
    }

    public function decorate(RecordInterface $resolvedRecord, ?PageLayoutContext $context = null): ContentBlockData
    {
        $tableDefinition = $this->tableDefinitionCollection->getTable($resolvedRecord->getMainType());
        $contentTypeDefinition = $this->contentTypeResolver->resolve($resolvedRecord);
        $identifier = $this->getRecordIdentifier($resolvedRecord);
        $this->contentBlockDataDecoratorSession->addContentBlockData($identifier, new ContentBlockData());
        $resolvedContentBlockDataRelation = new ResolvedContentBlockDataRelation();
        $resolvedContentBlockDataRelation->record = $resolvedRecord;
        $resolvedContentBlockDataRelation->resolved = $resolvedRecord->toArray();
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
            // TCA type "passthrough" is not available in the record, and it won't fall back to raw record value.
            if ($fieldType instanceof PassFieldType) {
                $resolvedField = $resolvedRelation->record->getRawRecord()->get($tcaFieldDefinition->getUniqueIdentifier());
            } else {
                $resolvedField = $resolvedRelation->record->get($tcaFieldDefinition->getUniqueIdentifier());
            }
            if ($this->isRelationField($resolvedField)) {
                $resolvedField = $this->handleRelation(
                    $resolvedField,
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
            if (!is_iterable($renderedGridItemDataObjects)) {
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
        RecordInterface|LazyRecordCollection $resolvedField,
        int $depth,
        ?PageLayoutContext $context = null,
    ): ContentBlockData|LazyRecordCollection {
        if ($resolvedField instanceof LazyRecordCollection) {
            $resolvedField = $this->transformMultipleRelation(
                $resolvedField,
                $depth,
                $context,
            );
            return $resolvedField;
        }
        $resolvedField = $this->transformSelectRelation(
            $resolvedField,
            $depth,
            $context,
        );
        return $resolvedField;
    }

    private function isRelationField(mixed $resolvedField): bool
    {
        if ($resolvedField instanceof Record) {
            return true;
        }
        if ($resolvedField instanceof LazyRecordCollection) {
            return true;
        }
        return false;
    }

    private function transformSelectRelation(
        LazyRecordCollection|RecordInterface $processedField,
        int $depth,
        ?PageLayoutContext $context = null,
    ): LazyRecordCollection|ContentBlockData {
        if ($processedField instanceof Record) {
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
     * @return LazyRecordCollection<ContentBlockData>
     */
    private function transformMultipleRelation(
        LazyRecordCollection $processedField,
        int $depth,
        ?PageLayoutContext $context = null,
    ): LazyRecordCollection {
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
        RecordInterface $item,
        int $depth,
        ?PageLayoutContext $context = null,
    ): ContentBlockData {
        $contentBlockRelation = new ResolvedContentBlockDataRelation();
        $foreignTable = $item->getMainType();
        $contentBlockRelation->record = $item;
        $contentBlockRelation->resolved = $item->toArray();
        $hasTableDefinition = $this->tableDefinitionCollection->hasTable($foreignTable);
        $collectionTableDefinition = null;
        if ($hasTableDefinition) {
            $collectionTableDefinition = $this->tableDefinitionCollection->getTable($foreignTable);
        }
        $typeDefinition = null;
        if ($hasTableDefinition) {
            $typeDefinition = $this->contentTypeResolver->resolve($contentBlockRelation->record);
        }
        if ($collectionTableDefinition !== null && $typeDefinition !== null) {
            $identifier = $this->getRecordIdentifier($contentBlockRelation->record);
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
        if ($resolvedRelation->record instanceof Record === false) {
            throw new \RuntimeException('Resolved record is not a record instance');
        }
        $contentBlockData = new ContentBlockData($resolvedRelation->record, $name, $grids, $resolvedData);
        return $contentBlockData;
    }

    /**
     * If the record is not defined by Content Blocks, we build a fake
     * Content Block data object for consistent usage.
     */
    private function buildFakeContentBlockDataObject(ResolvedContentBlockDataRelation $resolvedRelation): ContentBlockData
    {
        $typeName = $resolvedRelation->record->getRecordType() ?? '1';
        $fakeName = 'core/' . $typeName;
        $contentBlockDataObject = $this->buildContentBlockDataObject(
            $resolvedRelation,
            $fakeName,
        );
        return $contentBlockDataObject;
    }

    private function getRecordIdentifier(RecordInterface $record): string
    {
        $identifier = $record->getMainType();

        if ($record instanceof Record) {
            $identifier .= '-' . $record->getOverlaidUid();
        }

        return $identifier;
    }
}
