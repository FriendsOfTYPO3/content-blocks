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
#[FieldType(name: 'Radio', tcaType: 'radio')]
final class RadioFieldType extends AbstractFieldType
{
    use WithCommonProperties;
    use WithCustomProperties;

    private string|int $default = '';
    private bool $readOnly = false;
    private string $itemsProcFunc = '';
    private array $itemsProcessors = [];
    private array $items = [];

    public function createFromArray(array $settings): RadioFieldType
    {
        $self = clone $this;
        $self->setCommonProperties($settings);
        $default = $settings['default'] ?? $self->default;
        if (is_string($default) || is_int($default)) {
            $self->default = $default;
        }
        $self->readOnly = (bool)($settings['readOnly'] ?? $self->readOnly);
        $self->itemsProcFunc = (string)($settings['itemsProcFunc'] ?? $self->itemsProcFunc);
        $self->itemsProcessors = (array)($settings['itemsProcessors'] ?? $self->itemsProcessors);
        $self->items = (array)($settings['items'] ?? $self->items);
        $self->setCustomProperties($settings);

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
        if ($this->itemsProcFunc !== '') {
            $config['itemsProcFunc'] = $this->itemsProcFunc;
        }
        if ($this->itemsProcessors !== []) {
            $config['itemsProcessors'] = $this->itemsProcessors;
        }
        if ($this->items !== []) {
            $config['items'] = $this->items;
        }
        $config = $this->mergeCustomProperties($config);
        $tca['config'] = array_replace($tca['config'] ?? [], $config);
        return $tca;
    }
}
