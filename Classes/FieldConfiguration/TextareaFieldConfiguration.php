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
    public int $cols = 50;

    public string $default = '';

    public string $placeholder = '';

    public string $richtextConfiguration = '';

    public ?int $max = null;

    public ?int $rows = null;

    public bool $enableRichtext = false;

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
        return "`$uniqueColumnName` text";
    }

    /**
     * Fills the properties from array infos
     */
    protected function createFromArray(array $settings): self
    {
        parent::createFromArray($settings);
        $this->type = FieldType::TEXTAREA;
        $this->max = (int)($settings['properties']['max'] ?? $this->max);
        $this->rows = (int)($settings['properties']['rows'] ?? $this->rows);
        $this->cols = (int)($settings['properties']['cols'] ?? $this->cols);
        $this->default = $settings['properties']['default'] ?? $this->default;
        $this->placeholder = $settings['properties']['placeholder'] ?? $this->placeholder;
        $this->richtextConfiguration = $settings['properties']['richtextConfiguration'] ?? $this->richtextConfiguration;
        $this->enableRichtext = (bool)($settings['properties']['enableRichtext'] ?? $this->enableRichtext);
        $this->required = (bool)($settings['properties']['required'] ?? $this->required);
        $this->trim = (bool)($settings['properties']['trim'] ?? $this->trim);

        return $this;
    }

    /**
     * Get the TextareaFieldConfiguration as array
     */
    public function toArray(): array
    {
        return [
            'identifier' => $this->identifier,
            'type' => $this->type->value,
            'properties' => [
                'cols' => $this->cols,
                'default' => $this->default,
                'enableRichtext' => $this->enableRichtext,
                'max' => $this->max,
                'placeholder' => $this->placeholder,
                'richtextConfiguration' => $this->richtextConfiguration,
                'rows' => $this->rows,
                'required' => $this->required,
                'trim' => $this->trim,
            ],
            '_path' => $this->path,
            '_identifier' => $this->uniqueIdentifier,
        ];
    }

    public function getTemplateHtml(string $indentation): string
    {
        return $indentation . '<p>{' . $this->uniqueIdentifier . '}</p>' . "\n";
    }
}
