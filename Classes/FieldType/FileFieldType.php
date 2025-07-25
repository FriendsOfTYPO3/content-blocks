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

use TYPO3\CMS\Core\Resource\FileType;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @internal Not part of TYPO3's public API.
 */
#[FieldType(name: 'File', tcaType: 'file')]
final class FileFieldType extends AbstractFieldType
{
    use WithCommonProperties;

    private array|string $allowed = [];
    private array|string $disallowed = [];
    private array $appearance = [];
    private array $behaviour = [];
    private bool $readOnly = false;
    private int $minitems = 0;
    private int $maxitems = 0;
    private array $overrideChildTca = [];
    private bool $extendedPalette = true;
    private array $cropVariants = [];
    private string $relationship = '';

    public function createFromArray(array $settings): FileFieldType
    {
        $self = clone $this;
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
        $self->overrideChildTca = (array)($settings['overrideChildTca'] ?? $self->overrideChildTca);
        $self->extendedPalette = (bool)($settings['extendedPalette'] ?? $self->extendedPalette);
        $self->cropVariants = (array)($settings['cropVariants'] ?? $self->cropVariants);
        $self->relationship = (string)($settings['relationship'] ?? $self->relationship);
        return $self;
    }

    public function getTca(): array
    {
        $tca = $this->toTca();
        $config['type'] = $this->getTcaType();
        if ($this->allowed !== [] && $this->allowed !== '') {
            $config['allowed'] = $this->allowed;
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
        if ($this->maxitems === 1) {
            $config['appearance']['useSortable'] ??= false;
        }
        if ($this->overrideChildTca !== []) {
            $config['overrideChildTca'] = $this->overrideChildTca;
        }
        if (!$this->extendedPalette) {
            $basicPalette = '--palette--;;basicoverlayPalette,--palette--;;filePalette';
            $config['overrideChildTca']['types'][FileType::IMAGE->value]['showitem'] = $basicPalette;
            $config['overrideChildTca']['types'][FileType::AUDIO->value]['showitem'] = $basicPalette;
            $config['overrideChildTca']['types'][FileType::VIDEO->value]['showitem'] = $basicPalette;
        }
        if ($this->cropVariants !== []) {
            $config['overrideChildTca']['columns']['crop']['config']['cropVariants'] = $this->processCropVariants();
        }
        if ($this->relationship !== '') {
            $config['relationship'] = $this->relationship;
        }
        $tca['config'] = array_replace($tca['config'] ?? [], $config);
        return $tca;
    }

    protected function processCropVariants(): array
    {
        $cropVariants = $this->cropVariants;
        foreach ($cropVariants as $cropVariantName => $cropVariantConfig) {
            foreach ($cropVariantConfig['allowedAspectRatios'] ?? [] as $device => $aspectRatioConfig) {
                $aspectRatio = (string)($aspectRatioConfig['value'] ?? '');
                if (str_contains($aspectRatio, '/')) {
                    $parts = GeneralUtility::trimExplode('/', $aspectRatio);
                    if (count($parts) === 2) {
                        $dividend = (int)$parts[0];
                        $divisor = (int)$parts[1];
                        if ($divisor !== 0) {
                            $aspectRatio = $dividend / $divisor;
                        }
                    }
                }
                $floatValue = (float)$aspectRatio;
                $cropVariants[$cropVariantName]['allowedAspectRatios'][$device]['value'] = $floatValue;
            }
        }
        return $cropVariants;
    }
}
