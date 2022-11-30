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
 * class NumberFieldConfiguration
 */
class NumberFieldConfiguration extends AbstractFieldConfiguration implements FieldConfigurationInterface
{
    public int $size = 20;

    public $default = 0.0;

    public string $format = 'int';

    public array $range = [];

    public bool $required = false;

    public array $valuePicker = [];

    public array $slider = [];


    /**
     * Construct: setting from yaml file needed to create a field configuration.
     */
    public function __construct(array $settings)
    {
        $this->createFromArray($settings);
    }

    /**
     * Get TCA for this inputfield
     */
    public function getTca(): array
    {
        $formatTranslate = [
            'int' => 'integer',
            'float' => 'decimal',
            'double' => 'decimal',
        ];

        $tca = parent::getTcaTemplate();
        $tca['config'] = [
            'type' => $this->type,
            'size' => $this->size,
            'format' => $formatTranslate[$this->format],
        ];
        if ($this->default !== '') {
            $tca['config']['default'] = $this->default;
        }
        if ($this->required) {
            $tca['config']['required'] = $this->required;
        }
        if (isset($this->valuePicker['items']) && count($this->valuePicker['items']) > 0) {
            $tca['config']['valuePicker'] = $this->valuePicker;
        }
        if (isset($this->range) && count($this->range) > 0) {
            $tca['config']['range'] = $this->range;
        }
        if (isset($this->slider) && count($this->slider) > 0) {
            $tca['config']['slider'] = $this->slider;
        }
        return $tca;
    }

    /**
     * Get SQL definition for this inputfield
     */
    public function getSql(string $uniqueColumnName): string
    {
        $format = strtoupper(
            ((in_array($this->format, ['int', 'float', 'double'])) ? $this->format : 'int')
        );

        return "`$uniqueColumnName` $format(" . $this->size . ") DEFAULT 0 NOT NULL";
    }

    /**
     * Fills the properties from array infos
     */
    protected function createFromArray(array $settings): self
    {
        parent::createFromArray($settings);
        $this->type = FieldType::NUMBER->getTcaType();
        $this->size = $settings['properties']['size'] ?? $this->size;
        $this->default = $settings['properties']['default'] ?? $this->default;
        $this->format = $settings['properties']['format'] ?? $this->format;
        $this->required = (bool)($settings['properties']['required'] ?? $this->required);

        if (isset($settings['properties']['valuePicker']['items']) && is_array($settings['properties']['valuePicker']['items'])) {
            $tempPickerItems = [];
            foreach ($settings['properties']['valuePicker']['items'] as $key => $name) {
                $tempPickerItems[] = [$name, $key];
            }
            $this->valuePicker['items'] = $tempPickerItems;
        }
        if (isset($settings['properties']['range'])) {
            $this->range = $settings['properties']['range'];
        }
        if (isset($settings['properties']['slider'])) {
            $this->slider = $settings['properties']['slider'];
        }

        return $this;
    }

    /**
     * Get the InputFieldConfiguration as array
     */
    public function toArray(): array
    {
        return [
            'identifier' => $this->identifier,
            'type' => $this->type,
            'properties' => [
                'autocomplete' => $this->autocomplete,
                'default' => $this->default,
                'format' => $this->format,
                'placeholder' => $this->placeholder,
                'size' => $this->size,
                'required' => $this->required,
                'valuePicker' => $this->valuePicker,
                'range' => $this->range,
                'slider' => $this->slider,
            ],
            '_path' => $this->path,
            '_identifier' =>  $this->uniqueIdentifier,
        ];
    }

    /**
     * TODO: Idea: say what is allowed (properties and values) e.g. for backend modul inspektor of a input field.
     */
    public function getAllowedSettings(): array
    {
        return [
            'rows' => 'double',
            // property "required" is a "boolean" -> e.g. should be rendered as a checkbox
            'required' => 'boolean',
        ];
    }

    public function getTemplateHtml(int $indentation): string
    {
        return str_repeat(' ', $indentation * 4) . '<p>{' . $this->uniqueIdentifier . '}</p>' . "\n";
    }
}
