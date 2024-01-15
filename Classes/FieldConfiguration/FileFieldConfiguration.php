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

use TYPO3\CMS\Core\Preparations\TcaPreparation;
use TYPO3\CMS\Core\Resource\AbstractFile;

/**
 * @internal Not part of TYPO3's public API.
 */
final class FileFieldConfiguration implements FieldConfigurationInterface
{
    use WithCommonProperties;

    private FieldType $fieldType = FieldType::FILE;
    private array|string $allowed = [];
    private array|string $disallowed = [];
    private array $appearance = [];
    private array $behaviour = [];
    private bool $readOnly = false;
    private int $minitems = 0;
    private int $maxitems = 0;
    private bool $extendedPalette = true;
    private array $cropVariants = [];

    public static function createFromArray(array $settings): FileFieldConfiguration
    {
        $self = new self();
        $self->setCommonProperties($settings);
        $allowed = $settings['allowed'] ?? $self->allowed;
        if (is_array($allowed) || is_string($allowed)) {
            $self->allowed = $allowed;
        }
        $disallowed = $settings['disallowed'] ?? $self->disallowed;
        if (is_array($disallowed) || is_string($disallowed)) {
            $self->disallowed = $disallowed;
        }
        $self->appearance = (array)($settings['appearance'] ?? $self->appearance);
        $self->behaviour = (array)($settings['behaviour'] ?? $self->behaviour);
        $self->readOnly = (bool)($settings['readOnly'] ?? $self->readOnly);
        $self->minitems = (int)($settings['minitems'] ?? $self->minitems);
        $self->maxitems = (int)($settings['maxitems'] ?? $self->maxitems);
        $self->extendedPalette = (bool)($settings['extendedPalette'] ?? $self->extendedPalette);
        $cropVariants = $settings['cropVariants'] ?? $self->cropVariants;
        if (is_array($cropVariants) || is_string($cropVariants)) {
            $self->cropVariants = $cropVariants;
        }
        return $self;
    }

    public function getTca(): array
    {
        $tca = $this->toTca();
        $config['type'] = $this->fieldType->getTcaType();
        if ($this->allowed !== [] && $this->allowed !== '') {
            $config['allowed'] = TcaPreparation::prepareFileExtensions($this->allowed);
        }
        if ($this->disallowed !== [] && $this->disallowed !== '') {
            $config['disallowed'] = $this->disallowed;
        }
        if ($this->appearance !== []) {
            $config['appearance'] = $this->appearance;
        }
        if ($this->behaviour !== []) {
            $config['behaviour'] = $this->behaviour;
        }
        if ($this->readOnly) {
            $config['readOnly'] = true;
        }
        if ($this->minitems > 0) {
            $config['minitems'] = $this->minitems;
        }
        if ($this->maxitems > 0) {
            $config['maxitems'] = $this->maxitems;
        }
        if (!$this->extendedPalette) {
            $config['overrideChildTca'] = [
                'types' => [
                    AbstractFile::FILETYPE_IMAGE => [
                        'showitem' => '--palette--;;basicoverlayPalette,--palette--;;filePalette',
                    ],
                    AbstractFile::FILETYPE_AUDIO => [
                        'showitem' => '--palette--;;basicoverlayPalette,--palette--;;filePalette',
                    ],
                    AbstractFile::FILETYPE_VIDEO => [
                        'showitem' => '--palette--;;basicoverlayPalette,--palette--;;filePalette',
                    ],
                ],
            ];
        }
        // Cropping:
        if ($this->cropVariants !== []) {
            $config['overrideChildTca']['columns']['crop']['config']['cropVariants'] = [];
            foreach($this->cropVariants as $device => $options) {
                $config['overrideChildTca']['columns']['crop']['config']['cropVariants'][$device] = $options;
            }
        }

        $tca['config'] = array_replace($tca['config'] ?? [], $config);
        return $tca;
    }

    public function getSql(string $uniqueColumnName): string
    {
        // @todo change to return '' for v13 release (generated automatically now).
        return "`$uniqueColumnName` int(11) UNSIGNED DEFAULT '0' NOT NULL";
    }

    public function getFieldType(): FieldType
    {
        return $this->fieldType;
    }
}
