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

namespace TYPO3\CMS\ContentBlocks\FieldType;

/**
 * @internal Not part of TYPO3's public API.
 */
final class NumberFieldType implements FieldTypeInterface
{
    use WithCommonProperties;

    private int|float $default = 0;
    private bool $readOnly = false;
    private int $size = 0;
    private bool $required = false;
    private bool $nullable = false;
    private string $mode = '';
    private string $placeholder = '';
    private array $valuePicker = [];
    private ?bool $autocomplete = null;
    private array $range = [];
    private array $slider = [];
    private string $format = '';

    public static function createFromArray(array $settings): NumberFieldType
    {
        $self = new self();
        $self->setCommonProperties($settings);
        $self->format = (string)($settings['format'] ?? $self->format);
        $default = $settings['default'] ?? $self->default;
        $self->default = $self->format === 'decimal' ? (float)$default : (int)$default;
        $self->readOnly = (bool)($settings['readOnly'] ?? $self->readOnly);
        $self->size = (int)($settings['size'] ?? $self->size);
        $self->required = (bool)($settings['required'] ?? $self->required);
        $self->nullable = (bool)($settings['nullable'] ?? $self->nullable);
        $self->mode = (string)($settings['mode'] ?? $self->mode);
        $self->placeholder = (string)($settings['placeholder'] ?? $self->placeholder);
        $self->valuePicker = (array)($settings['valuePicker'] ?? $self->valuePicker);
        if (isset($settings['autocomplete'])) {
            $self->autocomplete = (bool)$settings['autocomplete'];
        }
        $self->range = (array)($settings['range'] ?? $self->range);
        $self->slider = (array)($settings['slider'] ?? $self->slider);

        return $self;
    }

    public function getTca(): array
    {
        $tca = $this->toTca();
        $config['type'] = self::getTcatype();
        if ($this->size !== 0) {
            $config['size'] = $this->size;
        }
        if ($this->default !== 0 && $this->default !== 0.0) {
            $config['default'] = $this->default;
        }
        if ($this->readOnly) {
            $config['readOnly'] = true;
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
        if ($this->required) {
            $config['required'] = true;
        }
        if (isset($this->autocomplete)) {
            $config['autocomplete'] = $this->autocomplete;
        }
        if (($this->valuePicker['items'] ?? []) !== []) {
            $config['valuePicker'] = $this->valuePicker;
        }
        if ($this->range !== []) {
            $config['range'] = $this->range;
        }
        if ($this->slider !== []) {
            $config['slider'] = $this->slider;
        }
        if ($this->format !== '') {
            $config['format'] = $this->format;
        }
        $tca['config'] = array_replace($tca['config'] ?? [], $config);
        return $tca;
    }

    public function getSql(string $column): string
    {
        $null = ' NOT NULL';
        if ($this->nullable) {
            $null = '';
        }
        if ($this->format === 'decimal') {
            return "`$column` decimal(10,2) DEFAULT '0.00'" . $null;
        }

        return "`$column` int(11) DEFAULT '0'" . $null;
    }

    public static function getName(): string
    {
        return 'Number';
    }

    public static function getTcaType(): string
    {
        return 'number';
    }

    public static function isSearchable(): bool
    {
        return false;
    }

    public static function isRenderable(): bool
    {
        return true;
    }

    public static function isRelation(): bool
    {
        return false;
    }

    public static function hasItems(): bool
    {
        return false;
    }
}
