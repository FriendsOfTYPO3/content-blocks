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
readonly class GridFactory
{
    /**
     * @param array<ContentBlockData> $records
     */
    public function build(PageLayoutContext $context, string $columnName, iterable $records): Grid
    {
        $colPos = 0;
        $column = null;
        foreach ($records as $record) {
            if ($record->has('colPos')) {
                $colPos = $record->get('colPos');
            }
            $definition = ['name' => $columnName, 'colPos' => $colPos];
            $column ??= GeneralUtility::makeInstance(GridColumn::class, $context, $definition);
            $gridColumnItem = GeneralUtility::makeInstance(
                GridColumnItem::class,
                $context,
                $column,
                $record->toArray(),
                $record->getMainType(),
            );
            $column->addItem($gridColumnItem);
        }
        if ($column === null) {
            $definition = ['name' => $columnName, 'colPos' => $colPos];
            $column = GeneralUtility::makeInstance(GridColumn::class, $context, $definition);
        }
        $row = GeneralUtility::makeInstance(GridRow::class, $context);
        $row->addColumn($column);
        $grid = GeneralUtility::makeInstance(Grid::class, $context);
        $grid->addRow($row);
        return $grid;
    }
}
