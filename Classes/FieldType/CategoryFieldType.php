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
#[FieldType(name: 'Category', tcaType: 'category')]
final class CategoryFieldType extends AbstractFieldType
{
    use WithCommonProperties;

    private string|int $default = '';
    private bool $readOnly = false;
    private int $size = 0;
    private int $maxitems = 0;
    private int $minitems = 0;
    private string $exclusiveKeys = '';
    private array $treeConfig = [];
    private string $relationship = '';
    private string $foreign_table_where = '';

    public function createFromArray(array $settings): CategoryFieldType
    {
        $self = clone $this;
        $self->setCommonProperties($settings);
        $default = $settings['default'] ?? $self->default;
        if (is_string($default) || is_int($default)) {
            $self->default = $default;
        }
        $self->readOnly = (bool)($settings['readOnly'] ?? $self->readOnly);
        $self->size = (int)($settings['size'] ?? $self->size);
        $self->maxitems = (int)($settings['maxitems'] ?? $self->maxitems);
        $self->minitems = (int)($settings['minitems'] ?? $self->minitems);
        $self->exclusiveKeys = (string)($settings['exclusiveKeys'] ?? $self->exclusiveKeys);
        $self->treeConfig = (array)($settings['treeConfig'] ?? $self->treeConfig);
        $self->relationship = (string)($settings['relationship'] ?? $self->relationship);
        $self->foreign_table_where = (string)($settings['foreign_table_where'] ?? $self->foreign_table_where);

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
        if ($this->foreign_table_where !== '') {
            $config['foreign_table_where'] = $this->foreign_table_where;
        }
        $tca['config'] = array_replace($tca['config'] ?? [], $config);
        return $tca;
    }
}
