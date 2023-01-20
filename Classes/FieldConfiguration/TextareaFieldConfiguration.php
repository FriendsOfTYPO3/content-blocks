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

final class TextareaFieldConfiguration implements FieldConfigurationInterface
{
    private FieldType $fieldType = FieldType::TEXTAREA;
    private string $default = '';
    private bool $readOnly = false;
    private bool $required = false;
    private int $max = 0;
    private int $min = 0;
    private bool $nullable = false;
    private string $mode = '';
    private string $placeholder = '';
    private string $is_in = '';
    private array $eval = [];
    private int $rows = 0;
    private int $cols = 0;
    private bool $enableTabulator = false;
    private bool $fixedFont = false;
    private string $wrap = '';
    private bool $enableRichtext = false;
    private string $richtextConfiguration = '';

    public static function createFromArray(array $settings): TextareaFieldConfiguration
    {
        $self = new self();
        $properties = $settings['properties'] ?? [];
        $self->default = (string)($properties['default'] ?? $self->default);
        $self->readOnly = (bool)($properties['readOnly'] ?? $self->readOnly);
        $self->required = (bool)($properties['required'] ?? $self->required);
        $self->max = (int)($properties['max'] ?? $self->max);
        $self->min = (int)($properties['min'] ?? $self->min);
        $self->nullable = (bool)($properties['nullable'] ?? $self->nullable);
        $self->mode = (string)($properties['mode'] ?? $self->mode);
        $self->placeholder = (string)($properties['placeholder'] ?? $self->placeholder);
        $self->is_in = (string)($properties['is_in'] ?? $self->is_in);
        $self->eval = (array)($properties['eval'] ?? $self->eval);
        $self->rows = (int)($properties['rows'] ?? $self->rows);
        $self->cols = (int)($properties['cols'] ?? $self->cols);
        $self->enableTabulator = (bool)($properties['enableTabulator'] ?? $self->enableTabulator);
        $self->fixedFont = (bool)($properties['fixedFont'] ?? $self->fixedFont);
        $self->wrap = (string)($properties['wrap'] ?? $self->wrap);
        $self->enableRichtext = (bool)($properties['enableRichtext'] ?? $self->enableRichtext);
        $self->richtextConfiguration = (string)($properties['richtextConfiguration'] ?? $self->richtextConfiguration);

        return $self;
    }

    public function getTca(string $languagePath, bool $useExistingField): array
    {
        if (!$useExistingField) {
            $tca['exclude'] = true;
        }
        $tca['label'] = 'LLL:' . $languagePath . '.label';
        $tca['description'] = 'LLL:' . $languagePath . '.description';
        $config = [
            'type' => $this->fieldType->getTcaType(),
        ];
        if ($this->default !== '') {
            $config['default'] = $this->default;
        }
        if ($this->readOnly) {
            $config['readOnly'] = true;
        }
        if ($this->required) {
            $config['required'] = true;
        }
        if ($this->max > 0) {
            $config['max'] = $this->max;
        }
        if ($this->min > 0) {
            $config['min'] = $this->min;
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
        if ($this->eval !== []) {
            $config['eval'] = implode(',', $this->eval);
        }
        if ($this->rows > 0) {
            $config['rows'] = $this->rows;
        }
        if ($this->cols > 0) {
            $config['cols'] = $this->cols;
        }
        if ($this->enableTabulator) {
            $config['enableTabulator'] = true;
        }
        if ($this->fixedFont) {
            $config['fixedFont'] = true;
        }
        if ($this->wrap !== '') {
            $config['wrap'] = $this->wrap;
        }
        if ($this->enableRichtext) {
            $config['enableRichtext'] = true;
        }
        if ($this->richtextConfiguration !== '') {
            $config['richtextConfiguration'] = $this->richtextConfiguration;
        }
        $tca['config'] = $config;
        return $tca;
    }

    public function getSql(string $uniqueColumnName): string
    {
        return "`$uniqueColumnName` text";
    }

    public function getFieldType(): FieldType
    {
        return $this->fieldType;
    }
}
