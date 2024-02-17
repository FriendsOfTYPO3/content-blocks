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

use TYPO3\CMS\Backend\View\BackendLayout\Grid\Grid;
use TYPO3\CMS\Backend\View\BackendLayout\Grid\GridColumn;
use TYPO3\CMS\Backend\View\BackendLayout\Grid\GridColumnItem;
use TYPO3\CMS\Backend\View\BackendLayout\Grid\GridRow;
use TYPO3\CMS\Backend\View\PageLayoutContext;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @internal Not part of TYPO3's public API.
 */
class GridFactory
{
    /**
     * @param array<ContentBlockData> $records
     */
    public function build(PageLayoutContext $context, string $columnName, array $records, string $tableName): Grid
    {
        $definition = ['name' => $columnName];
        $column = GeneralUtility::makeInstance(GridColumn::class, $context, $definition, $tableName);
        foreach ($records as $record) {
            $gridColumnItem = GeneralUtility::makeInstance(
                GridColumnItem::class,
                $context,
                $column,
                $record->_raw,
                $tableName,
            );
            $column->addItem($gridColumnItem);
        }
        $row = GeneralUtility::makeInstance(GridRow::class, $context);
        $row->addColumn($column);
        $grid = GeneralUtility::makeInstance(Grid::class, $context);
        $grid->addRow($row);
        return $grid;
    }
}
