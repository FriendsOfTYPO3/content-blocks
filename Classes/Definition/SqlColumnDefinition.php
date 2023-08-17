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

namespace TYPO3\CMS\ContentBlocks\Definition;

use TYPO3\CMS\ContentBlocks\FieldConfiguration\FieldConfigurationInterface;
use TYPO3\CMS\ContentBlocks\FieldConfiguration\FieldType;

/**
 * @internal Not part of TYPO3's public API.
 */
final class SqlColumnDefinition
{
    private string $column = '';
    private FieldConfigurationInterface $fieldConfiguration;

    public function __construct(array $columnDefinition)
    {
        if (!isset($columnDefinition['uniqueIdentifier'])) {
            throw new \InvalidArgumentException('Column name must not be empty.', 1629291834);
        }

        $this->column = $columnDefinition['uniqueIdentifier'];
        $this->fieldConfiguration = FieldType::from($columnDefinition['config']['type'])
            ->getFieldConfiguration($columnDefinition['config']);
    }

    public function getColumn(): string
    {
        return $this->column;
    }

    public function getSql(): string
    {
        return $this->fieldConfiguration->getSql($this->column);
    }

    public function getFieldType(): FieldType
    {
        return $this->fieldConfiguration->getFieldType();
    }
}
