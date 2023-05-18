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
final class TextFieldConfiguration implements FieldConfigurationInterface
{
    private FieldType $fieldType = FieldType::TEXT;
    private ?string $alternativeSql = null;
    private string $default = '';
    private bool $readOnly = false;
    private int $size = 0;
    private bool $required = false;
    private int $max = 0;
    private int $min = 0;
    private bool $nullable = false;
    private string $mode = '';
    private string $placeholder = '';
    private string $is_in = '';
    private array $valuePicker = [];
    private array $eval = [];
    private ?bool $autocomplete = null;

    public static function createFromArray(array $settings): TextFieldConfiguration
    {
        $self = new self();
        $self->alternativeSql = $settings['alternativeSql'] ?? $self->alternativeSql;
        $properties = $settings['properties'] ?? [];
        $self->default = (string)($properties['default'] ?? $self->default);
        $self->readOnly = (bool)($properties['readOnly'] ?? $self->readOnly);
        $self->size = (int)($properties['size'] ?? $self->size);
        $self->required = (bool)($properties['required'] ?? $self->required);
        $self->max = (int)($properties['max'] ?? $self->max);
        $self->min = (int)($properties['min'] ?? $self->min);
        $self->nullable = (bool)($properties['nullable'] ?? $self->nullable);
        $self->mode = (string)($properties['mode'] ?? $self->mode);
        $self->placeholder = (string)($properties['placeholder'] ?? $self->placeholder);
        $self->is_in = (string)($properties['is_in'] ?? $self->is_in);
        $self->eval = (array)($properties['eval'] ?? $self->eval);
        if (isset($properties['autocomplete'])) {
            $self->autocomplete = (bool)($properties['autocomplete'] ?? $self->autocomplete);
        }
        $self->valuePicker = (array)($properties['valuePicker'] ?? $self->valuePicker);

        return $self;
    }

    public function getTca(): array
    {
        $config['type'] = $this->fieldType->getTcaType();
        if ($this->size !== 0) {
            $config['size'] = $this->size;
        }
        if ($this->default !== '') {
            $config['default'] = $this->default;
        }
        if ($this->readOnly) {
            $config['readOnly'] = true;
        }
        if ($this->max > 0) {
            $config['max'] = $this->max;
        }
        if ($this->min > 0) {
            $config['min'] = $this->min;
        }
        if ($this->nullable) {
            $config['nullable'] = true;
        }
        if ($this->mode !== '') {
            $config['mode'] = $this->mode;
        }
        if ($this->placeholder !== '') {
            $config['placeholder'] = $this->placeholder;
        }
        if ($this->is_in !== '') {
            $config['is_in'] = $this->is_in;
        }
        if ($this->required) {
            $config['required'] = true;
        }
        if ($this->eval !== []) {
            $config['eval'] = implode(',', $this->eval);
        }
        if (isset($this->autocomplete)) {
            $config['autocomplete'] = $this->autocomplete;
        }
        if (($this->valuePicker['items'] ?? []) !== []) {
            $config['valuePicker'] = $this->valuePicker;
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
