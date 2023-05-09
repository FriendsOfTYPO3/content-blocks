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

use TYPO3\CMS\ContentBlocks\Enumeration\FieldType;

/**
 * @internal Not part of TYPO3's public API.
 */
final class FlexFormFieldConfiguration implements FieldConfigurationInterface
{
    private FieldType $fieldType = FieldType::FLEXFORM;
    private string $ds_pointerField = '';
    private array $ds = [];

    public static function createFromArray(array $settings): FlexFormFieldConfiguration
    {
        $self = new self();
        $self->ds_pointerField = (string)($settings['properties']['ds_pointerField'] ?? $self->ds_pointerField);
        $self->ds = (array)($settings['properties']['ds'] ?? $self->ds);
        return $self;
    }

    public function getTca(string $languagePath): array
    {
        $tca['label'] = $languagePath . '.label';
        $tca['description'] = $languagePath . '.description';
        $config['type'] = $this->fieldType->getTcaType();
        $config['ds_pointerField'] = $this->ds_pointerField;
        $config['ds'] = $this->ds;
        $tca['config'] = $config;
        return $tca;
    }

    public function getSql(string $uniqueColumnName): string
    {
        return "`$uniqueColumnName` text";
    }

    public function getFieldType(): FieldType
    {
        return $this->fieldType;
    }
}
