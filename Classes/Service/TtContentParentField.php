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

use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;

/**
 * @internal
 */
final class TtContentParentField
{
    public function __construct(
        private readonly TableDefinitionCollection $tableDefinitionCollection
    ) {}

    /**
     * @return string[]
     */
    public function getAllFieldNames(): array
    {
        if (!$this->tableDefinitionCollection->hasTable('tt_content')) {
            return [];
        }

        $fieldNames = [];
        foreach ($this->tableDefinitionCollection->getTable('tt_content')->getParentReferences() as $parentReference) {
            $fieldConfiguration = $parentReference->getFieldConfiguration()->getTca()['config'] ?? [];
            if (($fieldConfiguration['foreign_table'] ?? '') === 'tt_content') {
                $fieldNames[] = $fieldConfiguration['foreign_field'] ?? '';
            }
        }

        return array_values(array_unique(array_filter($fieldNames)));
    }
}
