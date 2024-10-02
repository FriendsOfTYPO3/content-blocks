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
#[FieldType(name: 'Pass', tcaType: 'passthrough')]
final class PassFieldType implements FieldTypeInterface
{
    private mixed $default = '';

    public static function createFromArray(array $settings): PassFieldType
    {
        $self = new self();
        $self->default = $settings['default'] ?? $self->default;
        return $self;
    }

    public function getTca(): array
    {
        $config['type'] = 'passthrough';
        if ($this->default !== '') {
            $config['default'] = $this->default;
        }
        $tca['config'] = $config;
        return $tca;
    }

    public function getSql(string $column): string
    {
        return "`$column` VARCHAR(255) DEFAULT '' NOT NULL";
    }
}
