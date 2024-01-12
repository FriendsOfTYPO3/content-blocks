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
final class UuidFieldConfiguration implements FieldConfigurationInterface
{
    use WithCommonProperties;

    private FieldType $fieldType = FieldType::UUID;
    private int $size = 30;
    private int $sizeMinimum = 10;
    private int $sizeMaximum = 50;
    private bool $enableCopyToClipboard = true;
    private int $version = 0;

    public static function createFromArray(array $settings): UuidFieldConfiguration
    {
        $self = new self();
        $self->setCommonProperties($settings);
        $self->size = (int)($settings['size'] ?? $self->size);
        $self->version = (int)($settings['version'] ?? $self->version);
        $self->enableCopyToClipboard = (bool)($settings['enableCopyToClipboard'] ?? $self->enableCopyToClipboard);

        return $self;
    }

    public function getTca(): array
    {
        $tca = $this->toTca();
        $config['type'] = $this->fieldType->getTcaType();
        if ($this->size !== 0) {
            $config['size'] = $this->size;
        }
        if ($this->version !== 0) {
            $config['version'] = $this->version;
        }
        if (!$this->enableCopyToClipboard) {
            $config['enableCopyToClipboard'] = false;
        }

        $tca['config'] = array_replace($tca['config'] ?? [], $config);
        return $tca;
    }

    public function getSql(string $uniqueColumnName): string
    {
        return "";
    }

    public function getFieldType(): FieldType
    {
        return $this->fieldType;
    }
}
