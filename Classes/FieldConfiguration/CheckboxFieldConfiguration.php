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
 * class CheckboxFieldConfiguration
 */
class CheckboxFieldConfiguration extends AbstractFieldConfiguration implements FieldConfigurationInterface
{
    public string $default = '';

    public array $items = [];

    public bool $invertStateDisplay = false;

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
        ];
        if ($this->invertStateDisplay) {
            $tca['config']['invertStateDisplay'] = $this->invertStateDisplay;
        }
        if (isset($this->items) && count($this->items) > 0) {
            $tca['config']['items'] = $this->items;
        }
        return $tca;
    }

    /**
     * Get SQL definition for this inputfield
     */
    public function getSql(string $uniqueColumnName): string
    {
        return "`$uniqueColumnName` VARCHAR(255) DEFAULT '' NOT NULL";
    }

    /**
     * Fills the properties from array infos
     */
    protected function createFromArray(array $settings): self
    {
        parent::createFromArray($settings);
        $this->type = FieldType::CHECKBOX->getTcaType();
        $this->default = $settings['properties']['default'] ?? $this->default;
        $this->invertStateDisplay = (bool)($settings['properties']['invertStateDisplay'] ?? $this->invertStateDisplay);

        if (isset($settings['properties']['items']) && is_array($settings['properties']['items'])) {
            $items = [];
            foreach ($settings['properties']['items'] as $key => $value) {
                $items[] = [ $value, $key];
            }
            $this->items = $items;
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
                'default' => $this->default,
                'invertStateDisplay' => $this->invertStateDisplay,
                'items' => $this->items,
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
