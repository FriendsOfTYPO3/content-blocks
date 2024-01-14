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

/**
 * @internal Not part of TYPO3's public API.
 */
final class JsonFieldConfiguration implements FieldConfigurationInterface
{
    use WithCommonProperties;

    private FieldType $fieldType = FieldType::JSON;
    private string $default = '';
    private int $cols = 0;
    private int $rows = 0;
    private bool $enableCodeEditor = true;
    private bool $required = false;
    private bool $readOnly = false;
    private string $placeholder = '';

    public static function createFromArray(array $settings): JsonFieldConfiguration
    {
        $self = new self();
        $self->setCommonProperties($settings);
        $self->default = (string)($settings['default'] ?? $self->default);
        $self->required = (bool)($settings['required'] ?? $self->required);
        $self->readOnly = (bool)($settings['readOnly'] ?? $self->readOnly);
        $self->cols = (int)($settings['cols'] ?? $self->cols);
        $self->rows = (int)($settings['rows'] ?? $self->rows);
        $self->enableCodeEditor = (bool)($settings['enableCodeEditor'] ?? $self->enableCodeEditor);
        $self->placeholder = (string)($settings['placeholder'] ?? $self->placeholder);

        return $self;
    }

    public function getTca(): array
    {
        $tca = $this->toTca();
        $config['type'] = $this->fieldType->getTcaType();
        if ($this->default !== '') {
            $config['default'] = $this->default;
        }
        if ($this->required) {
            $config['required'] = true;
        }
        if ($this->readOnly) {
            $config['readOnly'] = true;
        }
        if ($this->cols !== 0) {
            $config['cols'] = $this->cols;
        }
        if ($this->rows !== 0) {
            $config['rows'] = $this->rows;
        }
        if (!$this->enableCodeEditor) {
            $config['enableCodeEditor'] = false;
        }
        if ($this->placeholder !== '') {
            $config['placeholder'] = $this->placeholder;
        }

        $tca['config'] = array_replace($tca['config'] ?? [], $config);
        return $tca;
    }

    public function getSql(string $uniqueColumnName): string
    {
        return '';
    }

    public function getFieldType(): FieldType
    {
        return $this->fieldType;
    }
}
