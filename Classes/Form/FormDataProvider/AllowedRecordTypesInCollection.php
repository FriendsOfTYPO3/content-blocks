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

namespace TYPO3\CMS\ContentBlocks\Form\FormDataProvider;

use TYPO3\CMS\Backend\Form\FormDataProviderInterface;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\ContentBlocks\FieldType\CollectionFieldType;
use TYPO3\CMS\Core\Schema\TcaSchemaFactory;

final readonly class AllowedRecordTypesInCollection implements FormDataProviderInterface
{
    public function __construct(
        private TcaSchemaFactory $tcaSchemaFactory,
        private TableDefinitionCollection $tableDefinitionCollection,
        private AllowedRecordTypeFilter $allowedRecordTypeFilter,
    ) {}

    public function addData(array $result): array
    {
        $inlineParentTableName = $result['inlineParentTableName'];
        $inlineParentFieldName = $result['inlineParentFieldName'];
        $childTable = $result['tableName'];
        if (!$this->tcaSchemaFactory->has($childTable)) {
            return $result;
        }
        $tcaSchema = $this->tcaSchemaFactory->get($childTable);
        $typeField = $tcaSchema->getSubSchemaDivisorField();
        if ($typeField === null) {
            return $result;
        }
        if ($inlineParentTableName === '') {
            return $result;
        }
        if (!$this->tableDefinitionCollection->hasTable($inlineParentTableName)) {
            return $result;
        }
        $parentTableDefinition = $this->tableDefinitionCollection->getTable($inlineParentTableName);
        if (!$parentTableDefinition->tcaFieldDefinitionCollection->hasField($inlineParentFieldName)) {
            return $result;
        }
        $fieldDefinition = $parentTableDefinition->tcaFieldDefinitionCollection->getField($inlineParentFieldName);
        if ($fieldDefinition->fieldType instanceof CollectionFieldType === false) {
            return $result;
        }
        $allowedRecordTypes = $fieldDefinition->fieldType->getAllowedRecordTypes();
        if ($allowedRecordTypes === []) {
            return $result;
        }
        $typeFieldName = $typeField->getName();
        $items = $result['processedTca']['columns'][$typeFieldName]['config']['items'] ?? [];
        if ($items === []) {
            return $result;
        }
        $filteredItems = $this->allowedRecordTypeFilter->filterAndSortItems($items, $allowedRecordTypes);
        $result['processedTca']['columns'][$typeFieldName]['config']['items'] = $filteredItems;
        return $result;
    }
}
