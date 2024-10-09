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
#[FieldType(name: 'Language', tcaType: 'language')]
final class LanguageFieldType extends AbstractFieldType
{
    use WithCommonProperties;

    private int $default = 0;
    private bool $readOnly = false;
    private bool $required = false;

    public function createFromArray(array $settings): LanguageFieldType
    {
        $self = clone $this;
        $self->setCommonProperties($settings);
        $self->default = (int)($settings['default'] ?? $self->default);
        $self->required = (bool)($settings['required'] ?? $self->required);
        $self->readOnly = (bool)($settings['readOnly'] ?? $self->readOnly);
        return $self;
    }

    public function getTca(): array
    {
        $tca = $this->toTca();
        $config['type'] = $this->getTcaType();
        if ($this->default !== 0) {
            $config['default'] = $this->default;
        }
        if ($this->required) {
            $config['required'] = true;
        }
        if ($this->readOnly) {
            $config['readOnly'] = true;
        }
        $tca['config'] = array_replace($tca['config'] ?? [], $config);
        return $tca;
    }
}
