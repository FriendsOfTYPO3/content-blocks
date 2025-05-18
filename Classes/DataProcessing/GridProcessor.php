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

use TYPO3\CMS\Backend\View\PageLayoutContext;
use TYPO3\CMS\ContentBlocks\Definition\TcaFieldDefinition;
use TYPO3\CMS\Core\Collection\LazyRecordCollection;

/**
 * @internal Not part of TYPO3's public API.
 */
class GridProcessor
{
    private array $processingInstructions = [];

    public function __construct(
        protected readonly GridFactory $gridFactory,
    ) {}

    public function addInstruction(callable $instruction): void
    {
        $this->processingInstructions[] = $instruction;
    }

    public function process(): void
    {
        while ($instruction = array_shift($this->processingInstructions)) {
            $instruction();
        }
    }

    public function processGrid(
        RelationGrid $relationGrid,
        PageLayoutContext $context,
        TcaFieldDefinition $tcaFieldDefinition,
        ContentBlockData|LazyRecordCollection $resolvedField,
    ): void {
        if (!is_iterable($resolvedField)) {
            $resolvedField = [$resolvedField];
        }
        $gridLabel = $tcaFieldDefinition->labelPath;
        $grid = $this->gridFactory->build(
            $context,
            $gridLabel,
            $resolvedField,
        );
        $relationGrid->grid = $grid;
        $relationGrid->label = $gridLabel;
    }
}
