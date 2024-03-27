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

use TYPO3\CMS\ContentBlocks\Definition\FlexForm\FlexFormDefinition;

/**
 * @internal Not part of TYPO3's public API.
 */
final class FlexFormFieldType implements FieldTypeInterface
{
    use WithCommonProperties;

    /** @var FlexFormDefinition[] */
    private array $flexFormDefinitions = [];
    private string $ds_pointerField = '';
    private array $ds = [];

    public static function getName(): string
    {
        return 'FlexForm';
    }

    public static function getTcaType(): string
    {
        return 'flex';
    }

    public static function isSearchable(): bool
    {
        return true;
    }

    public static function createFromArray(array $settings): FlexFormFieldType
    {
        $self = new self();
        $self->setCommonProperties($settings);
        $self->ds_pointerField = (string)($settings['ds_pointerField'] ?? $self->ds_pointerField);
        $self->ds = (array)($settings['ds'] ?? $self->ds);
        $self->flexFormDefinitions = $settings['flexFormDefinitions'] ?? [];
        return $self;
    }

    public function getTca(): array
    {
        $tca = $this->toTca();
        $config['type'] = self::getTcaType();
        if ($this->ds_pointerField !== '') {
            $config['ds_pointerField'] = $this->ds_pointerField;
        }
        $config['ds'] = $this->ds;
        $tca['config'] = array_replace($tca['config'] ?? [], $config);
        return $tca;
    }

    public function getSql(string $column): string
    {
        return "`$column` text";
    }

    /**
     * @return FlexFormDefinition[]
     */
    public function getFlexFormDefinitions(): array
    {
        return $this->flexFormDefinitions;
    }

    public function setDataStructure(array $dataStructure): void
    {
        $this->ds = $dataStructure;
    }
}
