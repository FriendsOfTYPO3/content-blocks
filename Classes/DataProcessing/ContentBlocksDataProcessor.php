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

use TYPO3\CMS\ContentBlocks\Definition\ContentElementDefinition;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\ContentBlocks\Definition\TcaFieldDefinition;
use TYPO3\CMS\ContentBlocks\Enumeration\FieldType;
use TYPO3\CMS\ContentBlocks\Utility\UniqueNameUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;
use TYPO3\CMS\Frontend\Resource\FileCollector;

/**
 * Adds information about the current content block to variable "cb".
 */
class ContentBlocksDataProcessor implements DataProcessorInterface
{
    public function __construct(
        protected readonly TableDefinitionCollection $tableDefinitionCollection
    ) {
    }

    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ) {
        $ttContentDefinition = $this->tableDefinitionCollection->getTable('tt_content');
        $contentElementDefinition = $this->tableDefinitionCollection->getContentElementDefinition($processedData['data']['CType']);

        $contentBlockData = [];
        foreach ($contentElementDefinition->getColumns() as $column) {
            $tcaFieldDefinition = $ttContentDefinition->getTcaColumnsDefinition()->getField($column);
            $contentBlockData['cb'][$tcaFieldDefinition->getIdentifier()] = $this->processField($tcaFieldDefinition, $processedData['data'], $contentBlockData, 'tt_content', $contentElementDefinition);
        }

        return array_merge($processedData, $contentBlockData);
    }

    protected function processField(TcaFieldDefinition $tcaFieldDefinition, array $record, array $contentBlocksData, string $table, ContentElementDefinition $contentElementDefinition): mixed
    {
        $fieldType = $tcaFieldDefinition->getFieldType();

        // feature: use existing field
        $recordIdentifier = $tcaFieldDefinition->isUseExistingField() ? $tcaFieldDefinition->getIdentifier() : $tcaFieldDefinition->getUniqueIdentifier();

        // check if column is available
        if (!array_key_exists($recordIdentifier, $record)) {
            throw new \RuntimeException('The field ' . $recordIdentifier . ' is missing in the tt_content table. Try to compare your database schema.');
        }

        if ($fieldType->dataProcessingBehaviour() === 'skip') {
            return $contentBlocksData;
        }

        $data = $record[$recordIdentifier];

        if ($fieldType === FieldType::FILE) {
            $fileCollector = new FileCollector();
            $fileCollector->addFilesFromRelation($table, $recordIdentifier, $record);
            $data = $fileCollector->getFiles();
        }

        if ($fieldType === FieldType::COLLECTION) {
            $data = $this->processCollection($tcaFieldDefinition->getUniqueIdentifier(), $record, $tcaFieldDefinition, $contentElementDefinition);
        }

        return $data;
    }

    protected function processCollection(string $parentTable, array $record, TcaFieldDefinition $tcaFieldDefinition, ContentElementDefinition $contentElementDefinition): array
    {
        $table = UniqueNameUtility::createUniqueColumnName($contentElementDefinition->getComposerName(), $tcaFieldDefinition->getIdentifier());
        $cObj = GeneralUtility::makeInstance(ContentObjectRenderer::class);
        $cObj->start($record, $parentTable);
        $data = $cObj->getRecords($table, [
            'table' => $table,
            'select.' => [
                'pidInList' => 'this',
                'where' => '{#foreign_table_parent_uid} = ' . $record['uid'],
            ]
        ]);

        $tableDefinition = $this->tableDefinitionCollection->getTable($table);
        $contentBlockData = [];
        foreach ($data as $index => $row) {
            foreach ($tableDefinition->getTcaColumnsDefinition() as $childTcaFieldDefinition) {
                $data[$index][$childTcaFieldDefinition->getIdentifier()] = $this->processField(
                    tcaFieldDefinition: $childTcaFieldDefinition,
                    record: $row,
                    contentBlocksData: $contentBlockData,
                    table: $table,
                    contentElementDefinition: $contentElementDefinition
                );
            }
        }

        return $data;
    }
}
