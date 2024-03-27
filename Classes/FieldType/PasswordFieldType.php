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
final class PasswordFieldType implements FieldTypeInterface
{
    use WithCommonProperties;

    private string $default = '';
    private bool $readOnly = false;
    private bool $required = false;
    private string $mode = '';
    private string $placeholder = '';
    private ?bool $autocomplete = null;
    private bool $nullable = false;
    private int $size = 0;
    private bool $hashed = true;
    private string $passwordPolicy = '';

    public static function getName(): string
    {
        return 'Password';
    }

    public static function getTcaType(): string
    {
        return 'password';
    }

    public static function isSearchable(): bool
    {
        return false;
    }

    public static function createFromArray(array $settings): PasswordFieldType
    {
        $self = new self();
        $self->setCommonProperties($settings);
        $self->default = (string)($settings['default'] ?? $self->default);
        $self->readOnly = (bool)($settings['readOnly'] ?? $self->readOnly);
        $self->size = (int)($settings['size'] ?? $self->size);
        $self->required = (bool)($settings['required'] ?? $self->required);
        $self->nullable = (bool)($settings['nullable'] ?? $self->nullable);
        $self->mode = (string)($settings['mode'] ?? $self->mode);
        $self->placeholder = (string)($settings['placeholder'] ?? $self->placeholder);
        if (isset($settings['autocomplete'])) {
            $self->autocomplete = (bool)$settings['autocomplete'];
        }
        $self->hashed = (bool)($settings['hashed'] ?? $self->hashed);
        $self->passwordPolicy = (string)($settings['passwordPolicy'] ?? $self->passwordPolicy);

        return $self;
    }

    public function getTca(): array
    {
        $tca = $this->toTca();
        $config['type'] = self::getTcaType();
        if ($this->size !== 0) {
            $config['size'] = $this->size;
        }
        if ($this->default !== '') {
            $config['default'] = $this->default;
        }
        if ($this->readOnly) {
            $config['readOnly'] = true;
        }
        if ($this->nullable) {
            $config['nullable'] = true;
        }
        if ($this->mode !== '') {
            $config['mode'] = $this->mode;
        }
        if ($this->placeholder !== '') {
            $config['placeholder'] = $this->placeholder;
        }
        if ($this->required) {
            $config['required'] = true;
        }
        if (isset($this->autocomplete)) {
            $config['autocomplete'] = $this->autocomplete;
        }
        if (!$this->hashed) {
            $config['hashed'] = false;
        }
        if ($this->passwordPolicy !== '') {
            $config['passwordPolicy'] = $this->passwordPolicy;
        }
        $tca['config'] = array_replace($tca['config'] ?? [], $config);
        return $tca;
    }

    public function getSql(string $column): string
    {
        $null = ' NOT NULL';
        if ($this->nullable) {
            $null = '';
        }
        return "`$column` VARCHAR(255) DEFAULT ''" . $null;
    }
}
