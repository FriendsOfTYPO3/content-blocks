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
 * class InputFieldConfiguration
 */
class InputFieldConfiguration extends AbstractFieldConfiguration implements FieldConfigurationInterface
{
    public int $size = 255;

    public string $default = '';

    public string $placeholder = '';

    public ?int $max = null;

    public bool $autocomplete = false;

    public bool $required = false;

    public bool $trim = false;

    public array $valuePicker = [];

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
        $tca = parent::getTcaTemplate();
        $tca['config'] = [
            'type' => $this->type,
            'size' => $this->size,
        ];
        if ($this->max !== null) {
            $tca['config']['max'] = $this->max;
        }
        if ($this->default !== '') {
            $tca['config']['default'] = $this->default;
        }
        if ($this->placeholder !== '') {
            $tca['config']['placeholder'] = $this->placeholder;
        }
        if ($this->required) {
            $tca['config']['required'] = $this->required;
        }
        if ($this->autocomplete) {
            $tca['config']['autocomplete'] = $this->autocomplete;
        }
        if (isset($this->valuePicker['items']) && count($this->valuePicker['items']) > 0) {
            $tca['config']['valuePicker'] = $this->valuePicker;
        }
        return $tca;
    }

    /**
     * Get SQL definition for this inputfield
     */
    public function getSql(string $uniqueColumnName): string
    {
        return "`$uniqueColumnName` VARCHAR(" . $this->size . ") DEFAULT '' NOT NULL";
    }

    /**
     * Fills the properties from array infos
     */
    protected function createFromArray(array $settings): self
    {
        parent::createFromArray($settings);
        $this->type = FieldType::TEXT->getTcaType();
        $this->size = $settings['properties']['size'] ?? $this->size;
        $this->max = $settings['properties']['max'] ?? $this->max;
        $this->default = $settings['properties']['default'] ?? $this->default;
        $this->placeholder = $settings['properties']['placeholder'] ?? $this->placeholder;
        $this->required = (bool)($settings['properties']['required'] ?? $this->required);
        $this->autocomplete = (bool)($settings['properties']['autocomplete'] ?? $this->autocomplete);

        if (isset($settings['properties']['valuePicker']['items']) && is_array($settings['properties']['valuePicker']['items'])) {
            $tempPickerItems = [];
            foreach ($settings['properties']['valuePicker']['items'] as $key => $name) {
                $tempPickerItems[] = [$name, $key];
            }
            $this->valuePicker['items'] = $tempPickerItems;
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
                'max' => $this->max,
                'placeholder' => $this->placeholder,
                'size' => $this->size,
                'required' => $this->required,
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
