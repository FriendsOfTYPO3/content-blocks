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
#[FieldType(name: 'Folder', tcaType: 'folder')]
final class FolderFieldType extends AbstractFieldType
{
    use WithCommonProperties;

    private string $default = '';
    private bool $readOnly = false;
    private int $size = 0;
    private int $maxitems = 0;
    private int $minitems = 0;
    private int $autoSizeMax = 0;
    private bool $multiple = false;
    private bool $hideMoveIcons = false;
    private bool $hideDeleteIcon = false;
    private array $elementBrowserEntryPoints = [];
    private string $relationship = '';

    public function createFromArray(array $settings): FolderFieldType
    {
        $self = clone $this;
        $self->setCommonProperties($settings);
        $self->default = (string)($settings['default'] ?? $self->default);
        $self->readOnly = (bool)($settings['readOnly'] ?? $self->readOnly);
        $self->size = (int)($settings['size'] ?? $self->size);
        $self->maxitems = (int)($settings['maxitems'] ?? $self->maxitems);
        $self->minitems = (int)($settings['minitems'] ?? $self->minitems);
        $self->autoSizeMax = (int)($settings['autoSizeMax'] ?? $self->autoSizeMax);
        $self->multiple = (bool)($settings['multiple'] ?? $self->multiple);
        $self->hideMoveIcons = (bool)($settings['hideMoveIcons'] ?? $self->hideMoveIcons);
        $self->hideDeleteIcon = (bool)($settings['hideDeleteIcon'] ?? $self->hideDeleteIcon);
        $self->elementBrowserEntryPoints = (array)($settings['elementBrowserEntryPoints'] ?? $self->elementBrowserEntryPoints);
        $self->relationship = (string)($settings['relationship'] ?? $self->relationship);

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
        if ($this->autoSizeMax > 0) {
            $config['autoSizeMax'] = $this->autoSizeMax;
        }
        if ($this->multiple) {
            $config['multiple'] = true;
        }
        if ($this->hideMoveIcons) {
            $config['hideMoveIcons'] = true;
        }
        if ($this->hideDeleteIcon) {
            $config['hideDeleteIcon'] = true;
        }
        if ($this->elementBrowserEntryPoints !== []) {
            $config['elementBrowserEntryPoints'] = $this->elementBrowserEntryPoints;
        }
        if ($this->relationship !== '') {
            $config['relationship'] = $this->relationship;
        }
        $tca['config'] = array_replace($tca['config'] ?? [], $config);
        return $tca;
    }
}
