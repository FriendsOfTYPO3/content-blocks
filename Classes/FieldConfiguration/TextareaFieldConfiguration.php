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
 * class TextareaFieldConfiguration
 */
class TextareaFieldConfiguration extends AbstractFieldConfiguration implements FieldConfigurationInterface
{
    public int $cols = 24;

    public string $default = '';

    public string $placeholder = '';

    public string $richtextConfiguration = '';

    public int $max = 700;

    public int $rows = 3;

    public bool $enableRichtext = false;

    public bool $required = false;

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
            'cols' => $this->cols,
            'max' => $this->max,
            'rows' => $this->rows,
        ];
        if ($this->default !== '') {
            $tca['config']['default'] = $this->default;
        }
        if ($this->enableRichtext) {
            $tca['config']['enableRichtext'] = $this->enableRichtext;
        }
        if ($this->placeholder !== '') {
            $tca['config']['placeholder'] = $this->placeholder;
        }
        if ($this->richtextConfiguration !== '') {
            $tca['config']['richtextConfiguration'] = $this->richtextConfiguration;
        }
        if ($this->required) {
            $tca['config']['required'] = $this->required;
        }
        return $tca;
    }

    /**
     * Get SQL definition for this inputfield
     */
    public function getSql(string $uniqueColumnName): string
    {
        return "`$uniqueColumnName` text";
    }

    /**
     * Fills the properties from array infos
     */
    protected function createFromArray(array $settings): self
    {
        parent::createFromArray($settings);
        $this->type = FieldType::TEXTAREA->getTcaType();
        $this->cols = (int)($settings['properties']['cols'] ?? $this->cols);
        $this->default = $settings['properties']['default'] ?? $this->default;
        $this->enableRichtext = (bool)($settings['properties']['enableRichtext'] ?? $this->enableRichtext);
        $this->max = (int)($settings['properties']['max'] ?? $this->max);
        $this->placeholder = $settings['properties']['placeholder'] ?? $this->placeholder;
        $this->richtextConfiguration = $settings['properties']['richtextConfiguration'] ?? $this->richtextConfiguration;
        $this->rows = (int)($settings['properties']['rows'] ?? $this->rows);
        $this->required = (bool)($settings['properties']['required'] ?? $this->required);

        return $this;
    }

    /**
     * Get the TextareaFieldConfiguration as array
     */
    public function toArray(): array
    {
        return [
            'identifier' => $this->identifier,
            'type' => $this->type,
            'properties' => [
                'cols' => $this->cols,
                'default' => $this->default,
                'enableRichtext' => $this->enableRichtext,
                'max' => $this->max,
                'placeholder' => $this->placeholder,
                'richtextConfiguration' => $this->richtextConfiguration,
                'rows' => $this->rows,
                'required' => $this->required,
            ],
        ];
    }

    public function getTemplateHtml(int $indentation): string
    {
        return str_repeat(' ', $indentation * 4) . '<p>{' . $this->uniqueIdentifier . '}</p>' . "\n";
    }
}
