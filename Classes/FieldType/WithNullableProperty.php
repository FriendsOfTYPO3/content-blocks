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
trait WithNullableProperty
{
    private bool $nullable = false;
    private null|int|float|string $default = null;

    protected function setNullableAndDefault(array $settings, string $defaultCastAsType): void
    {
        if(array_key_exists('nullable', $settings)) {
            $this->nullable = (bool) $settings['nullable'];
        }
        if(array_key_exists('default', $settings)) {
            $this->default = $this->castDefaultValue($settings['default'], $defaultCastAsType);
            return;
        }
        if($this->nullable) {
            $this->default = null;
            return;
        }
        $this->default = '';
    }

    protected function castDefaultValue(mixed $defaultValue, string $castAsType): int|float|string
    {
        $castedDefaultValue = $this->default = match ($castAsType) {
            'int' => (int) $defaultValue,
            'float' => (float) $defaultValue,
            'string' => (string) $defaultValue,
            default => throw new \RuntimeException('The castAsType <' . $castAsType . '>  is not supported.', 1741534222),
        };
        return $castedDefaultValue;
    }
}
