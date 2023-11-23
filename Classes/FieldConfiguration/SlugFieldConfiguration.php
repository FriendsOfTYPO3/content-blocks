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
final class SlugFieldConfiguration implements FieldConfigurationInterface
{
    use WithCommonProperties;

    private FieldType $fieldType = FieldType::SLUG;
    private bool $readOnly = false;
    private int $size = 0;
    private array $appearance = [];
    private string $eval = '';
    private string $fallbackCharacter = '';
    private array $generatorOptions = [];
    private bool $prependSlash = false;

    public static function createFromArray(array $settings): FieldConfigurationInterface
    {
        $self = new self();
        $self->setCommonProperties($settings);
        $self->readOnly = (bool)($settings['readOnly'] ?? $self->readOnly);
        $self->size = (int)($settings['size'] ?? $self->size);
        $self->appearance = (array)($settings['appearance'] ?? $self->appearance);
        $self->eval = (string)($settings['eval'] ?? $self->eval);
        $self->fallbackCharacter = (string)($settings['fallbackCharacter'] ?? $self->fallbackCharacter);
        $self->generatorOptions = (array)($settings['generatorOptions'] ?? $self->generatorOptions);
        $self->prependSlash = (bool)($settings['prependSlash'] ?? $self->prependSlash);
        return $self;
    }

    public function getTca(): array
    {
        $tca = $this->toTca();
        $config['type'] = $this->fieldType->getTcaType();
        if ($this->readOnly) {
            $config['readOnly'] = true;
        }
        if ($this->size > 0) {
            $config['size'] = $this->size;
        }
        if ($this->appearance !== []) {
            $config['appearance'] = $this->appearance;
        }
        if ($this->eval !== '') {
            $config['eval'] = $this->eval;
        }
        if ($this->fallbackCharacter !== '') {
            $config['fallbackCharacter'] = $this->fallbackCharacter;
        }
        if ($this->generatorOptions !== []) {
            $config['generatorOptions'] = $this->generatorOptions;
        }
        if ($this->prependSlash) {
            $config['prependSlash'] = $this->prependSlash;
        }
        $tca['config'] = array_replace($tca['config'] ?? [], $config);
        return $tca;
    }

    public function getSql(string $uniqueColumnName): string
    {
        return '';
    }

    public function getFieldType(): FieldType
    {
        return $this->fieldType;
    }
}
