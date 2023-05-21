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
final class RadioFieldConfiguration implements FieldConfigurationInterface
{
    use WithLabelAndDescription;

    private FieldType $fieldType = FieldType::RADIO;
    private ?string $alternativeSql = null;
    private string|int $default = '';
    private bool $readOnly = false;
    private string $itemsProcFunc = '';
    private array $items = [];

    public static function createFromArray(array $settings): RadioFieldConfiguration
    {
        $self = new self();
        $self->label = $settings['label'] ?? $self->label;
        $self->description = $settings['description'] ?? $self->description;
        $self->alternativeSql = $settings['alternativeSql'] ?? $self->alternativeSql;
        $properties = $settings['properties'] ?? [];
        $default = $properties['default'] ?? $self->default;
        if (is_string($default) || is_int($default)) {
            $self->default = $default;
        }
        $self->readOnly = (bool)($properties['readOnly'] ?? $self->readOnly);
        $self->itemsProcFunc = (string)($properties['itemsProcFunc'] ?? $self->itemsProcFunc);
        $self->items = (array)($properties['items'] ?? $self->items);

        return $self;
    }

    public function getTca(): array
    {
        $tca = $this->setLabelAndDescription();
        $config['type'] = $this->fieldType->getTcaType();
        if ($this->default !== '') {
            $config['default'] = $this->default;
        }
        if ($this->readOnly) {
            $config['readOnly'] = true;
        }
        if ($this->itemsProcFunc !== '') {
            $config['itemsProcFunc'] = $this->itemsProcFunc;
        }
        if ($this->items !== []) {
            $config['items'] = $this->items;
        }
        $tca['config'] = $config;
        return $tca;
    }

    public function getSql(string $uniqueColumnName): string
    {
        if ($this->alternativeSql !== null) {
            return '`' . $uniqueColumnName . '` ' . $this->alternativeSql;
        }
        return "`$uniqueColumnName` VARCHAR(255) DEFAULT '' NOT NULL";
    }

    public function getFieldType(): FieldType
    {
        return $this->fieldType;
    }
}
