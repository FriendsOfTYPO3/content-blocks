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

final class CategoryFieldConfiguration implements FieldConfigurationInterface
{
    private FieldType $fieldType = FieldType::CATEGORY;
    private string|int $default = '';
    private bool $readOnly = false;
    private int $size = 0;
    private int $maxitems = 0;
    private int $minitems = 0;
    private string $exclusiveKeys = '';
    private array $treeConfig = [];
    private string $relationship = '';

    public static function createFromArray(array $settings): CategoryFieldConfiguration
    {
        $self = new self();
        $properties = $settings['properties'] ?? [];
        $default = $properties['default'] ?? $self->default;
        if (is_string($default) || is_int($default)) {
            $self->default = $default;
        }
        $self->readOnly = (bool)($properties['readOnly'] ?? $self->readOnly);
        $self->size = (int)($properties['size'] ?? $self->size);
        $self->maxitems = (int)($properties['maxitems'] ?? $self->maxitems);
        $self->minitems = (int)($properties['minitems'] ?? $self->minitems);
        $self->exclusiveKeys = (string)($properties['exclusiveKeys'] ?? $self->exclusiveKeys);
        $self->treeConfig = (array)($properties['treeConfig'] ?? $self->treeConfig);
        $self->relationship = (string)($properties['relationship'] ?? $self->relationship);

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
        if ($this->default !== '') {
            $config['default'] = $this->default;
        }
        if ($this->readOnly) {
            $config['readOnly'] = true;
        }
        if ($this->size > 0) {
            $config['size'] = $this->size;
        }
        if ($this->maxitems > 0) {
            $config['maxitems'] = $this->maxitems;
        }
        if ($this->minitems > 0) {
            $config['minitems'] = $this->minitems;
        }
        if ($this->exclusiveKeys !== '') {
            $config['exclusiveKeys'] = $this->exclusiveKeys;
        }
        if ($this->treeConfig !== []) {
            $config['treeConfig'] = $this->treeConfig;
        }
        if ($this->relationship !== '') {
            $config['relationship'] = $this->relationship;
        }
        $tca['config'] = $config;
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
