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
    private bool $enableImageManipulation = true;

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
        $self->enableImageManipulation = (bool)($settings['enableImageManipulation'] ?? $self->enableImageManipulation);
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
        if ($this->enableImageManipulation) {
            $config['overrideChildTca'] = [
                'types' => [
                    '0' => [
                        'showitem' => '--palette--;;imageoverlayPalette,--palette--;;filePalette',
                    ],
                    AbstractFile::FILETYPE_TEXT => [
                        'showitem' => '--palette--;;imageoverlayPalette,--palette--;;filePalette',
                    ],
                    AbstractFile::FILETYPE_IMAGE => [
                        'showitem' => '--palette--;;imageoverlayPalette,--palette--;;filePalette',
                    ],
                    AbstractFile::FILETYPE_AUDIO => [
                        'showitem' => '--palette--;;audioOverlayPalette,--palette--;;filePalette',
                    ],
                    AbstractFile::FILETYPE_VIDEO => [
                        'showitem' => '--palette--;;videoOverlayPalette,--palette--;;filePalette',
                    ],
                    AbstractFile::FILETYPE_APPLICATION => [
                        'showitem' => '--palette--;;imageoverlayPalette,--palette--;;filePalette',
                    ],
                ],
            ];
        }
        $tca['config'] = $config;
        return $tca;
    }

    public function getSql(string $uniqueColumnName): string
    {
        return "`$uniqueColumnName` int(11) UNSIGNED DEFAULT '0' NOT NULL";
    }

    public function getFieldType(): FieldType
    {
        return $this->fieldType;
    }
}
