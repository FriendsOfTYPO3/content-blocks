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

namespace TYPO3\CMS\ContentBlocks\FieldType;

/**
 * @internal Not part of TYPO3's public API.
 */
#[FieldType(name: 'Text', tcaType: 'input')]
final class TextFieldType extends AbstractFieldType
{
    use WithCommonProperties;
    use WithNullableProperty;
    use WithSearchableProperty;

    private bool $readOnly = false;
    private int $size = 0;
    private bool $required = false;
    private int $max = 0;
    private int $min = 0;
    private string $mode = '';
    private string $placeholder = '';
    private string $is_in = '';
    private array $valuePicker = [];
    private array $eval = [];
    private ?bool $autocomplete = null;

    public function createFromArray(array $settings): TextFieldType
    {
        $self = clone $this;
        $self->setCommonProperties($settings);
        $self->setSearchable($settings);
        $self->setNullableAndDefault($settings, 'string');
        $self->readOnly = (bool)($settings['readOnly'] ?? $self->readOnly);
        $self->size = (int)($settings['size'] ?? $self->size);
        $self->required = (bool)($settings['required'] ?? $self->required);
        $self->max = (int)($settings['max'] ?? $self->max);
        $self->min = (int)($settings['min'] ?? $self->min);
        $self->mode = (string)($settings['mode'] ?? $self->mode);
        $self->placeholder = (string)($settings['placeholder'] ?? $self->placeholder);
        $self->is_in = (string)($settings['is_in'] ?? $self->is_in);
        $self->eval = (array)($settings['eval'] ?? $self->eval);
        if (isset($settings['autocomplete'])) {
            $self->autocomplete = (bool)$settings['autocomplete'];
        }
        $self->valuePicker = (array)($settings['valuePicker'] ?? $self->valuePicker);

        return $self;
    }

    public function getTca(): array
    {
        $tca = $this->toTca();
        $tca = $this->searchableToTca($tca);
        $config['type'] = $this->getTcaType();
        if ($this->size !== 0) {
            $config['size'] = $this->size;
        }
        if ($this->hasDefault) {
            $config['default'] = $this->default;
        }
        if ($this->readOnly) {
            $config['readOnly'] = true;
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
        if ($this->required) {
            $config['required'] = true;
        }
        if ($this->eval !== []) {
            $config['eval'] = implode(',', $this->eval);
        }
        if (isset($this->autocomplete)) {
            $config['autocomplete'] = $this->autocomplete;
        }
        if (($this->valuePicker['items'] ?? []) !== []) {
            $config['valuePicker'] = $this->valuePicker;
        }
        $tca['config'] = array_replace($tca['config'] ?? [], $config);
        return $tca;
    }
}
