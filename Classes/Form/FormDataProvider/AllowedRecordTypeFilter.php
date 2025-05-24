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

use TYPO3\CMS\Core\Schema\Struct\SelectItem;

/**
 * @internal
 */
class AllowedRecordTypeFilter
{
    /**
     * @param array<array|SelectItem> $items
     * @param string[] $allowedRecordTypes
     * @return SelectItem[]
     */
    public function filterAndSortItems(array $items, array $allowedRecordTypes): array
    {
        $filteredItems = [];
        foreach ($items as $item) {
            $selectItem = $item;
            if ($selectItem instanceof SelectItem === false) {
                $selectItem = SelectItem::fromTcaItemArray($item);
            }
            if (in_array($selectItem->getValue(), $allowedRecordTypes, true) === false) {
                continue;
            }
            $position = array_search($selectItem->getValue(), $allowedRecordTypes);
            $filteredItems[$position] = $selectItem;
        }
        ksort($filteredItems);
        $filteredItems = array_values($filteredItems);
        return $filteredItems;
    }
}
