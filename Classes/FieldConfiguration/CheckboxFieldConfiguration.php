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
 * @internal Not part of TYPO3's public API.
 */
final class CheckboxFieldConfiguration implements FieldConfigurationInterface
{
    use WithCommonProperties;

    private FieldType $fieldType = FieldType::CHECKBOX;
    private ?string $alternativeSql = null;
    private string $renderType = '';
    private int $default = 0;
    private bool $readOnly = false;
    private bool $invertStateDisplay = false;
    private string $itemsProcFunc = '';
    private int|string $cols = 0;
    private string $eval = '';
    private array $validation = [];
    private array $items = [];

    public static function createFromArray(array $settings): CheckboxFieldConfiguration
    {
        $self = new self();
        $self->setCommonProperties($settings);
        $self->alternativeSql = $settings['alternativeSql'] ?? $self->alternativeSql;
        $self->renderType = (string)($settings['renderType'] ?? $self->renderType);
        $self->default = (int)($settings['default'] ?? $self->default);
        $self->readOnly = (bool)($settings['readOnly'] ?? $self->readOnly);
        $self->itemsProcFunc = (string)($settings['itemsProcFunc'] ?? $self->itemsProcFunc);
        $self->cols = $settings['cols'] ?? $self->cols;
        $self->eval = (string)($settings['eval'] ?? $self->eval);
        $self->validation = (array)($settings['validation'] ?? $self->validation);
        $self->items = (array)($settings['items'] ?? $self->items);
        $self->invertStateDisplay = (bool)($settings['invertStateDisplay'] ?? $self->invertStateDisplay);

        return $self;
    }

    public function getTca(): array
    {
        $tca = $this->toTca();
        $config['type'] = $this->fieldType->getTcaType();
        if ($this->renderType !== '') {
            $config['renderType'] = $this->renderType;
        }
        if ($this->default > 0) {
            $config['default'] = $this->default;
        }
        if ($this->readOnly) {
            $config['readOnly'] = true;
        }
        if ($this->itemsProcFunc !== '') {
            $config['itemsProcFunc'] = $this->itemsProcFunc;
        }
        if ($this->cols !== 0 && $this->cols !== '') {
            $config['cols'] = $this->cols;
        }
        if ($this->eval !== '') {
            $config['eval'] = $this->eval;
        }
        if ($this->validation !== []) {
            $config['validation'] = $this->validation;
        }
        if ($this->items !== []) {
            $config['items'] = $this->items;
        }
        if ($this->invertStateDisplay) {
            $config['items'][0]['invertStateDisplay'] = true;
        }
        $tca['config'] = $config;
        return $tca;
    }

    public function getSql(string $uniqueColumnName): string
    {
        if ($this->alternativeSql !== null) {
            return '`' . $uniqueColumnName . '` ' . $this->alternativeSql;
        }
        return "`$uniqueColumnName` int(11) DEFAULT '0' NOT NULL";
    }

    public function getFieldType(): FieldType
    {
        return $this->fieldType;
    }
}
