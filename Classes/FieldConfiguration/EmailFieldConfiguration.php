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

final class EmailFieldConfiguration implements FieldConfigurationInterface
{
    private FieldType $fieldType = FieldType::EMAIL;
    private string $default = '';
    private bool $readOnly = false;
    private int $size = 0;
    private bool $required = false;
    private bool $nullable = false;
    private string $mode = '';
    private string $placeholder = '';
    private array $eval = [];
    private ?bool $autocomplete = null;

    public static function createFromArray(array $settings): EmailFieldConfiguration
    {
        $self = new self();
        $properties = $settings['properties'] ?? [];
        $self->default = (string)($settings['properties']['default'] ?? $self->default);
        $self->readOnly = (bool)($properties['readOnly'] ?? $self->readOnly);
        $self->required = (bool)(($properties['required'] ?? $self->required));
        $self->nullable = (bool)($properties['nullable'] ?? $self->nullable);
        $self->mode = (string)($properties['mode'] ?? $self->mode);
        $self->placeholder = (string)($properties['placeholder'] ?? $self->placeholder);
        $self->eval = (array)($properties['eval'] ?? $self->eval);
        if (isset($properties['autocomplete'])) {
            $self->autocomplete = (bool)($properties['autocomplete'] ?? $self->autocomplete);
        }

        return $self;
    }

    public function getTca(string $languagePath, bool $useExistingField): array
    {
        if (!$useExistingField) {
            $tca['exclude'] = true;
        }
        $tca['label'] = 'LLL:' . $languagePath . '.label';
        $tca['description'] = 'LLL:' . $languagePath . '.description';
        $config['type'] = $this->fieldType->getTcaType();
        if ($this->size !== 0) {
            $config['size'] = $this->size;
        }
        if ($this->default !== '') {
            $config['default'] = $this->default;
        }
        if ($this->readOnly) {
            $config['readOnly'] = true;
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
        if ($this->required) {
            $config['required'] = true;
        }
        if ($this->eval !== []) {
            $config['eval'] = implode(',', $this->eval);
        }
        if (isset($this->autocomplete)) {
            $config['autocomplete'] = $this->autocomplete;
        }
        $tca['config'] = $config;
        return $tca;
    }

    public function getSql(string $uniqueColumnName): string
    {
        return "`$uniqueColumnName` VARCHAR(255) DEFAULT '' NOT NULL";
    }

    public function getFieldType(): FieldType
    {
        return $this->fieldType;
    }
}
