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

namespace TYPO3\CMS\ContentBlocks\Service;

use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;

final class ContentElementParentFieldService
{
    public function __construct(
        private readonly TableDefinitionCollection $tableDefinitionCollection
    ) {}

    /**
     * @return list<string>
     */
    public function getAllFieldNames(): array
    {
        $contentElementTable = ContentType::CONTENT_ELEMENT->getTable();
        if (!$this->tableDefinitionCollection->hasTable($contentElementTable)) {
            return [];
        }

        $fieldNames = [];
        $contentElementTableDefinition = $this->tableDefinitionCollection->getTable($contentElementTable);
        foreach ($contentElementTableDefinition->getParentReferences() as $parentReference) {
            $fieldConfiguration = $parentReference->getFieldConfiguration()->getTca()['config'] ?? [];
            if (($fieldConfiguration['foreign_table'] ?? '') === $contentElementTable) {
                $foreignField = $fieldConfiguration['foreign_field'];
                $fieldNames[$foreignField] = $foreignField;
            }
        }
        $fieldNameList = array_values($fieldNames);
        return $fieldNameList;
    }
}
