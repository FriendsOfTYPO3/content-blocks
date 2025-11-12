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
#[FieldType(name: 'Country', tcaType: 'country')]
final class CountryFieldType extends AbstractFieldType
{
    use WithCommonProperties;
    private string $default = '';
    private bool $readOnly = false;
    private bool $required = false;
    private int $size = 0;
    private array $filter = [];
    private string $labelField = '';
    private array $prioritizedCountries = [];
    private array $sortItems = [];

    public function createFromArray(array $settings): CountryFieldType
    {
        $self = clone $this;
        $self->setCommonProperties($settings);
        $self->default = (string)($settings['default'] ?? $self->default);
        $self->readOnly = (bool)($settings['readOnly'] ?? $self->readOnly);
        $self->required = (bool)($settings['required'] ?? $self->required);
        $self->size = (int)($settings['size'] ?? $self->size);
        $self->filter = (array)($settings['filter'] ?? $self->filter);
        $self->labelField = (string)($settings['labelField'] ?? $self->labelField);
        $self->prioritizedCountries = (array)($settings['prioritizedCountries'] ?? $self->prioritizedCountries);
        $self->sortItems = (array)($settings['sortItems'] ?? $self->sortItems);

        return $self;
    }

    public function getTca(): array
    {
        $tca = $this->toTca();
        $config['type'] = $this->getTcaType();
        if ($this->default !== '') {
            $config['default'] = $this->default;
        }
        if ($this->readOnly) {
            $config['readOnly'] = true;
        }
        if ($this->required) {
            $config['required'] = true;
        }
        if ($this->size !== 0) {
            $config['size'] = $this->size;
        }
        if ($this->filter !== []) {
            $config['filter'] = $this->filter;
        }
        if ($this->labelField !== '') {
            $config['labelField'] = $this->labelField;
        }
        if ($this->prioritizedCountries !== []) {
            $config['prioritizedCountries'] = $this->prioritizedCountries;
        }
        if ($this->sortItems !== []) {
            $config['sortItems'] = $this->sortItems;
        }
        $tca['config'] = array_replace($tca['config'] ?? [], $config);
        return $tca;
    }
}
