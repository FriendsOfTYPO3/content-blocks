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
        $this->type = FieldType::TEXT;
        $this->size = $settings['properties']['size'] ?? $this->size;
        $this->max = $settings['properties']['max'] ?? $this->max;
        $this->default = $settings['properties']['default'] ?? $this->default;
        $this->placeholder = $settings['properties']['placeholder'] ?? $this->placeholder;
        $this->required = (bool)($settings['properties']['required'] ?? $this->required);
        $this->trim = (bool)($settings['properties']['trim'] ?? $this->trim);
        $this->autocomplete = (bool)($settings['properties']['autocomplete'] ?? $this->autocomplete);

        return $this;
    }

    /**
     * Get the InputFieldConfiguration as array
     */
    public function toArray(): array
    {
        return [
            'identifier' => $this->identifier,
            'type' => $this->type->value,
            'properties' => [
                'autocomplete' => $this->autocomplete,
                'default' => $this->default,
                'max' => $this->max,
                'placeholder' => $this->placeholder,
                'size' => $this->size,
                'required' => $this->required,
                'trim' => $this->trim,
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

    public function getTemplateHtml(string $indentation): string
    {
        return $indentation . '<p>{' . $this->uniqueIdentifier . '}</p>' . "\n";
    }
}
