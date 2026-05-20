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
#[FieldType(name: 'SelectText', tcaType: 'select')]
final class SelectTextFieldType extends AbstractFieldType
{
    use WithCommonProperties;
    use WithCustomProperties;

    private string $default = '';
    private bool $readOnly = false;
    private int $size = 0;
    private string $authMode = '';
    private bool $disableNoMatchingValueElement = false;
    private array $itemGroups = [];
    private array $items = [];
    private array $sortItems = [];
    private int $dbFieldLength = 0;

    public function createFromArray(array $settings): SelectTextFieldType
    {
        $self = clone $this;
        $self->setCommonProperties($settings);
        $default = $settings['default'] ?? $self->default;
        if (is_string($default)) {
            $self->default = $default;
        }
        $self->readOnly = (bool)($settings['readOnly'] ?? $self->readOnly);
        $self->size = (int)($settings['size'] ?? $self->size);
        $self->authMode = (string)($settings['authMode'] ?? $self->authMode);
        $self->disableNoMatchingValueElement = (bool)($settings['disableNoMatchingValueElement'] ?? $self->disableNoMatchingValueElement);
        $self->itemGroups = (array)($settings['itemGroups'] ?? $self->itemGroups);
        $self->items = (array)($settings['items'] ?? $self->items);
        $self->sortItems = (array)($settings['sortItems'] ?? $self->sortItems);
        $self->dbFieldLength = (int)($settings['dbFieldLength'] ?? $self->dbFieldLength);
        $self->setCustomProperties($settings);

        return $self;
    }

    public function getTca(): array
    {
        $tca = $this->toTca();
        $config['type'] = $this->getTcaType();
        $config['renderType'] = 'selectSingle';
        if ($this->default !== '') {
            $config['default'] = $this->default;
        }
        if ($this->readOnly) {
            $config['readOnly'] = true;
        }
        if ($this->size > 0) {
            $config['size'] = $this->size;
        }
        if ($this->authMode !== '') {
            $config['authMode'] = $this->authMode;
        }
        if ($this->disableNoMatchingValueElement) {
            $config['disableNoMatchingValueElement'] = true;
        }
        if ($this->itemGroups !== []) {
            $config['itemGroups'] = $this->itemGroups;
        }
        $config['items'] = $this->items;
        if ($this->sortItems !== []) {
            $config['sortItems'] = $this->sortItems;
        }
        if ($this->dbFieldLength !== 0) {
            $config['dbFieldLength'] = $this->dbFieldLength;
        }
        $config = $this->mergeCustomProperties($config);
        $tca['config'] = array_replace($tca['config'] ?? [], $config);
        return $tca;
    }

    public function getNonOverridableOptions(): array
    {
        return ['renderType'];
    }
}
