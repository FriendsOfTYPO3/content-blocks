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
use TYPO3\CMS\ContentBlocks\FieldType\FieldTypeInterface;
use TYPO3\CMS\ContentBlocks\FieldType\SpecialFieldType;
use TYPO3\CMS\Core\Collection\LazyRecordCollection;
use TYPO3\CMS\Core\Domain\Record;
use TYPO3\CMS\Core\Domain\RecordInterface;
use TYPO3\CMS\Core\Domain\RecordPropertyClosure;
use TYPO3\CMS\Core\Resource\Collection\LazyFileReferenceCollection;
use TYPO3\CMS\Core\Resource\FileReference;

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
    }

    public function decorate(RecordInterface $resolvedRecord, ?PageLayoutContext $context = null): ContentBlockData
    {
        if ($resolvedRecord instanceof ContentBlockData) {
            return $resolvedRecord;
        }
        $contentTypeDefinition = $this->contentTypeResolver->resolve($resolvedRecord);
        $resolvedContentBlockDataRelation = new ResolvedContentBlockDataRelation();
        $resolvedContentBlockDataRelation->record = $resolvedRecord;
        $resolvedContentBlockDataRelation->resolved = $resolvedRecord->toArray();
        if ($contentTypeDefinition === null) {
            return $this->buildFakeContentBlockDataObject($resolvedContentBlockDataRelation);
        }
        $identifier = $this->getRecordIdentifier($resolvedRecord);
        $this->contentBlockDataDecoratorSession->addContentBlockData($identifier, new ContentBlockData());
        $tableDefinition = $this->tableDefinitionCollection->getTable($resolvedRecord->getMainType());
        $contentBlockData = $this->buildContentBlockDataObjectRecursive(
            $contentTypeDefinition,
            $tableDefinition,
            $resolvedContentBlockDataRelation,
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
        int $depth = 0,
        ?PageLayoutContext $context = null,
    ): ContentBlockData {
        $processedContentBlockData = [];
        $grids = [];
        foreach ($contentTypeDefinition->getColumns() as $column) {
            $tcaFieldDefinition = $tableDefinition->tcaFieldDefinitionCollection->getField($column);
            $fieldType = $tcaFieldDefinition->fieldType;
            if (SpecialFieldType::tryFrom($fieldType->getName()) !== null) {
                continue;
            }
            $loadedField = $this->loadField($resolvedRelation, $tcaFieldDefinition, $depth, $context);
            $processedContentBlockData[$tcaFieldDefinition->identifier] = $loadedField;
            // Exclude file relations from grids.
            if ($this->canHandleGrid($loadedField, $fieldType, $context)) {
                $grids[$tcaFieldDefinition->identifier] = $this->handleGrids($context, $loadedField, $tcaFieldDefinition);
            }
        }
        $resolvedRelation->resolved = $processedContentBlockData;
        $gridData = new ContentBlockGridData($grids);
        $contentBlockDataObject = $this->buildContentBlockDataObject(
            $resolvedRelation,
            $contentTypeDefinition->getName(),
            $gridData,
        );
        return $contentBlockDataObject;
    }

    private function loadField(
        ResolvedContentBlockDataRelation $resolvedRelation,
        TcaFieldDefinition $tcaFieldDefinition,
        int $depth,
        ?PageLayoutContext $context
    ): mixed {
        $fieldType = $tcaFieldDefinition->fieldType;
        // TCA type "passthrough" is not available in the record, and it won't fall back to raw record value.
        if ($fieldType->getTcaType() === 'passthrough') {
            $resolvedField = $resolvedRelation->record->getRawRecord()->get($tcaFieldDefinition->uniqueIdentifier);
            return $resolvedField;
        }
        // Simple field type, load eagerly.
        if ($this->isRelationField($tcaFieldDefinition) === false) {
            $resolvedField = $resolvedRelation->record->get($tcaFieldDefinition->uniqueIdentifier);
            return $resolvedField;
        }
        // Relation field type, load lazily.
        $recordPropertyClosure = new RecordPropertyClosure(
            function () use ($resolvedRelation, $tcaFieldDefinition, $depth, $context): ContentBlockData|LazyRecordCollection|LazyFileReferenceCollection|FileReference|null {
                $resolvedField = $resolvedRelation->record->get($tcaFieldDefinition->uniqueIdentifier);
                $resolvedField = $this->handleRelation(
                    $resolvedField,
                    $depth,
                    $context,
                );
                return $resolvedField;
            }
        );
        return $recordPropertyClosure;
    }

    /**
     * @return LazyRecordCollection<RenderedGridItem>|RecordPropertyClosure
     */
    private function handleGrids(
        ?PageLayoutContext $context,
        RecordPropertyClosure $recordPropertyClosure,
        TcaFieldDefinition $tcaFieldDefinition
    ): LazyRecordCollection|RecordPropertyClosure {
        if ($context === null) {
            if ($this->request === null) {
                throw new \InvalidArgumentException(
                    'ContentBlockDataDecorator is missing the request object.',
                    1756397952
                );
            }
            $this->contentObjectProcessor->setRequest($this->request);
            $initialization = function () use ($recordPropertyClosure): array {
                $renderedGridItemDataObjects = $recordPropertyClosure->instantiate();
                if ($this->isRecordObject($renderedGridItemDataObjects) === false) {
                    return [];
                }
                if (!is_iterable($renderedGridItemDataObjects)) {
                    $renderedGridItemDataObjects = [$renderedGridItemDataObjects];
                }
                $renderedGridItems = [];
                foreach ($renderedGridItemDataObjects as $contentBlockDataObject) {
                    $renderedGridItem = $this->contentObjectProcessor->processContentObject($contentBlockDataObject);
                    $renderedGridItems[] = $renderedGridItem;
                }
                return $renderedGridItems;
            };
            return new LazyRecordCollection('', $initialization);
        }
        $initialization = function () use ($tcaFieldDefinition, $recordPropertyClosure, $context): ?RelationGrid {
            $resolvedField = $recordPropertyClosure->instantiate();
            if ($resolvedField === null) {
                return null;
            }
            $relationGrid = $this->gridProcessor->processGrid(
                $context,
                $tcaFieldDefinition,
                $resolvedField
            );
            return $relationGrid;
        };
        return new RecordPropertyClosure($initialization);
    }

    private function canHandleGrid(mixed $loadedField, FieldTypeInterface $fieldType, ?PageLayoutContext $context): bool
    {
        if ($loadedField instanceof RecordPropertyClosure === false) {
            return false;
        }
        if ($fieldType->getTcaType() === 'file') {
            return false;
        }
        if ($context === null && $this->request === null) {
            return false;
        }
        return true;
    }

    private function handleRelation(
        RecordInterface|LazyRecordCollection|LazyFileReferenceCollection|FileReference|null $resolvedField,
        int $depth,
        ?PageLayoutContext $context = null,
    ): ContentBlockData|LazyRecordCollection|LazyFileReferenceCollection|FileReference|null {
        if ($resolvedField === null) {
            return null;
        }
        if ($resolvedField instanceof LazyFileReferenceCollection) {
            return $resolvedField;
        }
        if ($resolvedField instanceof FileReference) {
            return $resolvedField;
        }
        if ($resolvedField instanceof LazyRecordCollection) {
            $initialization = function () use ($resolvedField, $depth, $context): array {
                $resolvedField = $this->transformMultipleRelation(
                    $resolvedField,
                    $depth,
                    $context,
                );
                return $resolvedField;
            };
            return new LazyRecordCollection((string)$resolvedField, $initialization);
        }
        $resolvedField = $this->transformSingleRelation(
            $resolvedField,
            $depth,
            $context,
        );
        return $resolvedField;
    }

    private function isRelationField(TcaFieldDefinition $tcaFieldDefinition): bool
    {
        $tcaType = $tcaFieldDefinition->fieldType->getTcaType();
        $fieldConfig = $tcaFieldDefinition->getTca()['config'] ?? [];
        if (in_array($tcaType, ['category', 'inline', 'file'])) {
            return true;
        }
        $allowed = $fieldConfig['allowed'] ?? '';
        if ($tcaType === 'group' && $allowed !== '') {
            return true;
        }
        $foreignTable = $fieldConfig['foreign_table'] ?? '';
        if ($tcaType === 'select' && $foreignTable !== '') {
            return true;
        }
        return false;
    }

    private function isRecordObject(mixed $resolvedField): bool
    {
        if ($resolvedField instanceof RecordInterface) {
            return true;
        }
        if ($resolvedField instanceof LazyRecordCollection) {
            return true;
        }
        return false;
    }

    /**
     * @return array<ContentBlockData>
     */
    private function transformMultipleRelation(
        LazyRecordCollection $processedField,
        int $depth,
        ?PageLayoutContext $context = null,
    ): array {
        $items = [];
        foreach ($processedField as $key => $processedFieldItem) {
            $items[$key] = $this->transformSingleRelation(
                $processedFieldItem,
                $depth,
                $context,
            );
        }
        return $items;
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

    private function buildContentBlockDataObject(
        ResolvedContentBlockDataRelation $resolvedRelation,
        string $name = '',
        ?ContentBlockGridData $gridData = null,
    ): ContentBlockData {
        $resolvedData = $resolvedRelation->resolved;
        if ($resolvedRelation->record instanceof Record === false) {
            throw new \RuntimeException('Resolved record is not a record instance', 1728587332);
        }
        $contentBlockData = new ContentBlockData($resolvedRelation->record, $name, $gridData, $resolvedData);
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
