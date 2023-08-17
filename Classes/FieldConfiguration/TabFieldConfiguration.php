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

namespace TYPO3\CMS\ContentBlocks\FieldConfiguration;

/**
 * @internal Not part of TYPO3's public API.
 */
final class TabFieldConfiguration implements FieldConfigurationInterface
{
    private FieldType $fieldType = FieldType::TAB;

    public static function createFromArray(array $settings): FieldConfigurationInterface
    {
        return new self();
    }

    public function getTca(): array
    {
        return [];
    }

    public function getSql(string $uniqueColumnName): string
    {
        return '';
    }

    public function getFieldType(): FieldType
    {
        return $this->fieldType;
    }
}
