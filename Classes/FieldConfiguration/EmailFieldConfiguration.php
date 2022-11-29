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
class EmailFieldConfiguration extends AbstractFieldConfiguration implements FieldConfigurationInterface
{
    public bool $autocomplete = false;

    public string $default = '';

    public string $placeholder = '';

    public int $size = 20;

    public bool $required = false;

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
            'type' => FieldType::EMAIL->getTcaType(),
            'size' => $this->size,
        ];
        if ($this->autocomplete) {
            $tca['config']['autocomplete'] = $this->autocomplete;
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
        $this->type = FieldType::EMAIL->value;
        $this->autocomplete = (bool)($settings['properties']['autocomplete'] ?? $this->autocomplete);
        $this->default = $settings['properties']['default'] ?? $this->default;
        $this->placeholder = $settings['properties']['placeholder'] ?? $this->placeholder;
        $this->size = $settings['properties']['size'] ?? $this->size;
        $this->required = (bool)($settings['properties']['required'] ?? $this->required);

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
            'type' => FieldType::EMAIL->value,
            'properties' => [
                'autocomplete' => $this->autocomplete,
                'default' => $this->default,
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
        return str_repeat(' ', $indentation * 4) . '<p><f:link.email email="{' . $this->uniqueIdentifier . '}" /></p>' . "\n";
    }
}
