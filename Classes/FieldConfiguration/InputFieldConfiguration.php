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

class InputFieldConfiguration extends AbstractFieldConfiguration implements FieldConfigurationInterface
{
    public string $default = '';
    public bool $readOnly = false;
    public int $size = 0;
    public bool $required = false;
    public int $max = 0;
    public bool $nullable = false;
    public string $mode = '';
    public string $placeholder = '';
    public string $is_in = '';
    public array $valuePicker = [];
    public array $eval = [];
    public bool $autocomplete = false;

    public function getTca(): array
    {
        $tca = parent::getTcaTemplate();
        $config['type'] = $this->type;
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
        if ($this->autocomplete) {
            $config['autocomplete'] = true;
        }
        if (($this->valuePicker['items'] ?? []) !== []) {
            $config['valuePicker'] = $this->valuePicker;
        }
        $tca['config'] = $config;
        return $tca;
    }

    public function getSql(string $uniqueColumnName): string
    {
        return "`$uniqueColumnName` VARCHAR(255) DEFAULT '' NOT NULL";
    }

    public static function createFromArray(array $settings): static
    {
        $self = parent::createFromArray($settings);
        $self->type = FieldType::TEXT->getTcaType();
        $self->default = $settings['properties']['default'] ?? $self->default;
        $self->readOnly = (bool)(($settings['properties']['readOnly'] ?? $self->readOnly));
        $self->size = $settings['properties']['size'] ?? $self->size;
        $self->required = (bool)(($settings['properties']['required'] ?? $self->required));
        $self->max = $settings['properties']['max'] ?? $self->max;
        $self->nullable = (bool)($settings['properties']['nullable'] ?? $self->nullable);
        $self->mode = $settings['properties']['mode'] ?? $self->mode;
        $self->placeholder = $settings['properties']['placeholder'] ?? $self->placeholder;
        $self->is_in = $settings['properties']['is_in'] ?? $self->is_in;
        $self->eval = $settings['properties']['eval'] ?? $self->eval;
        $self->autocomplete = (bool)($settings['properties']['autocomplete'] ?? $self->autocomplete);
        $self->valuePicker = $settings['properties']['valuePicker'] ?? $self->valuePicker;

        return $self;
    }

    public function toArray(): array
    {
        return [
            'identifier' => $this->identifier,
            'type' => $this->type,
            'properties' => [
                'autocomplete' => $this->autocomplete,
                'default' => $this->default,
                'max' => $this->max,
                'placeholder' => $this->placeholder,
                'size' => $this->size,
                'required' => $this->required,
            ],
            '_path' => $this->path,
            '_identifier' =>  $this->uniqueIdentifier,
        ];
    }

    public function getTemplateHtml(int $indentation): string
    {
        return str_repeat(' ', $indentation * 4) . '<p>{' . $this->uniqueIdentifier . '}</p>' . "\n";
    }
}
