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
final class FolderFieldConfiguration implements FieldConfigurationInterface
{
    private FieldType $fieldType = FieldType::FOLDER;
    private ?string $alternativeSql = null;
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
        $self->alternativeSql = $settings['alternativeSql'] ?? $self->alternativeSql;
        $properties = $settings['properties'] ?? [];
        $self->recursive = (bool)($settings['recursive'] ?? $self->recursive);
        $self->default = (string)($properties['default'] ?? $self->default);
        $self->readOnly = (bool)($properties['readOnly'] ?? $self->readOnly);
        $self->size = (int)($properties['size'] ?? $self->size);
        $self->maxitems = (int)($properties['maxitems'] ?? $self->maxitems);
        $self->minitems = (int)($properties['minitems'] ?? $self->minitems);
        $self->autoSizeMax = (int)($properties['autoSizeMax'] ?? $self->autoSizeMax);
        $self->multiple = (bool)($properties['multiple'] ?? $self->multiple);
        $self->hideMoveIcons = (bool)($properties['hideMoveIcons'] ?? $self->hideMoveIcons);
        $self->elementBrowserEntryPoints = (array)($properties['elementBrowserEntryPoints'] ?? $self->elementBrowserEntryPoints);

        return $self;
    }

    public function getTca(string $languagePath, bool $useExistingField): array
    {
        if (!$useExistingField) {
            $tca['exclude'] = true;
        }
        $tca['label'] = $languagePath . '.label';
        $tca['description'] = $languagePath . '.description';

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
        $tca['config'] = $config;
        return $tca;
    }

    public function getSql(string $uniqueColumnName): string
    {
        if ($this->alternativeSql !== null) {
            return '`' . $uniqueColumnName . '` ' . $this->alternativeSql;
        }
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
