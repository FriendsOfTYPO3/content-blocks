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

use TYPO3\CMS\Core\Utility\MathUtility;

/**
 * @internal Not part of TYPO3's public API.
 */
final class DateTimeFieldConfiguration implements FieldConfigurationInterface
{
    use WithCommonProperties;

    private FieldType $fieldType = FieldType::DATETIME;
    private string|int $default = '';
    private bool $readOnly = false;
    private int $size = 0;
    private bool $required = false;
    private bool $nullable = false;
    private string $mode = '';
    private string $placeholder = '';
    private array $range = [];
    private string $dbType = '';
    private bool $disableAgeDisplay = false;
    private string $format = '';

    public static function createFromArray(array $settings): DateTimeFieldConfiguration
    {
        $self = new self();
        $self->setCommonProperties($settings);
        $default = $settings['default'] ?? $self->default;
        if (is_string($default) || is_int($default)) {
            $self->default = $default;
        }
        $self->readOnly = (bool)($settings['readOnly'] ?? $self->readOnly);
        $self->size = (int)($settings['size'] ?? $self->size);
        $self->required = (bool)($settings['required'] ?? $self->required);
        $self->nullable = (bool)($settings['nullable'] ?? $self->nullable);
        $self->mode = (string)($settings['mode'] ?? $self->mode);
        $self->placeholder = (string)($settings['placeholder'] ?? $self->placeholder);
        $self->range = (array)($settings['range'] ?? $self->range);
        $self->dbType = (string)($settings['dbType'] ?? $self->dbType);
        $self->disableAgeDisplay = (bool)($settings['disableAgeDisplay'] ?? $self->disableAgeDisplay);
        $self->format = (string)($settings['format'] ?? $self->format);

        return $self;
    }

    public function getTca(): array
    {
        $tca = $this->toTca();
        $config['type'] = $this->fieldType->getTcaType();
        if ($this->default !== '') {
            $config['default'] = $this->dbType !== '' ? $this->default : $this->convertDateToTimestamp($this->default);
        }
        if ($this->readOnly) {
            $config['readOnly'] = true;
        }
        if ($this->size > 0) {
            $config['size'] = $this->size;
        }
        if ($this->required) {
            $config['required'] = true;
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
        if ($this->range !== []) {
            $lower = $this->convertDateToTimestamp($this->range['lower'] ?? 0);
            if ($lower > 0) {
                $this->range['lower'] = $lower;
            }
            $upper = $this->convertDateToTimestamp($this->range['upper'] ?? 0);
            if ($upper > 0) {
                $this->range['upper'] = $upper;
            }
            $config['range'] = $this->range;
        }
        if ($this->dbType !== '') {
            $config['dbType'] = $this->dbType;
        }
        if ($this->disableAgeDisplay) {
            $config['disableAgeDisplay'] = true;
        }
        if ($this->format !== '') {
            $config['format'] = $this->format;
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

    /**
     * Returns a timestamp as integer. Returns 0 if it could not create a timestamp.
    */
    private function convertDateToTimestamp(string|int $date): int
    {
        $isTime = $this->format === 'time';
        if (is_int($date) || MathUtility::canBeInterpretedAsInteger($date)) {
            return (int)$date;
        }
        if ($isTime && $date !== '') {
            $date = '1970-01-01 ' . $date;
        }
        if ($date !== '' && strtotime($date)) {
            return strtotime($date);
        }
        return 0;
    }
}
