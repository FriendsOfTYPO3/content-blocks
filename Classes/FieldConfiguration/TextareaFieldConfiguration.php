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

    public function getSql(string $uniqueColumnName): string
    {
        return "`$uniqueColumnName` text";
    }

    public static function createFromArray(array $settings): static
    {
        $self = parent::createFromArray($settings);
        $self->type = FieldType::TEXTAREA->getTcaType();
        $self->cols = (int)($settings['properties']['cols'] ?? $self->cols);
        $self->default = $settings['properties']['default'] ?? $self->default;
        $self->enableRichtext = (bool)($settings['properties']['enableRichtext'] ?? $self->enableRichtext);
        $self->max = (int)($settings['properties']['max'] ?? $self->max);
        $self->placeholder = $settings['properties']['placeholder'] ?? $self->placeholder;
        $self->richtextConfiguration = $settings['properties']['richtextConfiguration'] ?? $self->richtextConfiguration;
        $self->rows = (int)($settings['properties']['rows'] ?? $self->rows);
        $self->required = (bool)($settings['properties']['required'] ?? $self->required);

        return $self;
    }

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
