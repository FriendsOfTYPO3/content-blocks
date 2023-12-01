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
final class FolderFieldConfiguration implements FieldConfigurationInterface
{
    use WithCommonProperties;

    private FieldType $fieldType = FieldType::FOLDER;
    private bool $recursive = false;
    private string $default = '';
    private bool $readOnly = false;
    private int $size = 0;
    private int $maxitems = 0;
    private int $minitems = 0;
    private int $autoSizeMax = 0;
    private bool $multiple = false;
    private bool $hideMoveIcons = false;
    private array $elementBrowserEntryPoints = [];

    public static function createFromArray(array $settings): FolderFieldConfiguration
    {
        $self = new self();
        $self->setCommonProperties($settings);
        $self->recursive = (bool)($settings['recursive'] ?? $self->recursive);
        $self->default = (string)($settings['default'] ?? $self->default);
        $self->readOnly = (bool)($settings['readOnly'] ?? $self->readOnly);
        $self->size = (int)($settings['size'] ?? $self->size);
        $self->maxitems = (int)($settings['maxitems'] ?? $self->maxitems);
        $self->minitems = (int)($settings['minitems'] ?? $self->minitems);
        $self->autoSizeMax = (int)($settings['autoSizeMax'] ?? $self->autoSizeMax);
        $self->multiple = (bool)($settings['multiple'] ?? $self->multiple);
        $self->hideMoveIcons = (bool)($settings['hideMoveIcons'] ?? $self->hideMoveIcons);
        $self->elementBrowserEntryPoints = (array)($settings['elementBrowserEntryPoints'] ?? $self->elementBrowserEntryPoints);

        return $self;
    }

    public function getTca(): array
    {
        $tca = $this->toTca();
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
        if ($this->autoSizeMax > 0) {
            $config['autoSizeMax'] = $this->autoSizeMax;
        }
        if ($this->multiple) {
            $config['multiple'] = true;
        }
        if ($this->hideMoveIcons) {
            $config['hideMoveIcons'] = true;
        }
        if ($this->elementBrowserEntryPoints !== []) {
            $config['elementBrowserEntryPoints'] = $this->elementBrowserEntryPoints;
        }
        $tca['config'] = array_replace($tca['config'] ?? [], $config);
        return $tca;
    }

    public function getSql(string $uniqueColumnName): string
    {
        return "`$uniqueColumnName` text";
    }

    public function getFieldType(): FieldType
    {
        return $this->fieldType;
    }

    public function isRecursive(): bool
    {
        return $this->recursive;
    }
}
