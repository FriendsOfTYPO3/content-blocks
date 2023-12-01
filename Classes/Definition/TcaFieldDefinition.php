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

namespace TYPO3\CMS\ContentBlocks\Definition;

use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;
use TYPO3\CMS\ContentBlocks\FieldConfiguration\FieldConfigurationInterface;
use TYPO3\CMS\ContentBlocks\FieldConfiguration\FieldType;

/**
 * @internal Not part of TYPO3's public API.
 */
final class TcaFieldDefinition
{
    private ?ContentType $parentContentType = null;
    private string $identifier = '';
    private string $uniqueIdentifier = '';
    private string $labelPath = '';
    private string $descriptionPath = '';
    private bool $useExistingField = false;
    private ?FieldConfigurationInterface $fieldConfiguration = null;

    public static function createFromArray(array $array): TcaFieldDefinition
    {
        $uniqueIdentifier = (string)($array['uniqueIdentifier'] ?? '');
        if ($uniqueIdentifier === '') {
            throw new \InvalidArgumentException('The identifier for a TcaFieldDefinition must not be empty.', 1629277138);
        }
        if (!($array['type'] ?? null) instanceof FieldType) {
            throw new \InvalidArgumentException('The type in the config for a TcaFieldDefinition must not be empty.', 1629277139);
        }

        $self = new self();
        return $self
            ->withParentContentType(ContentType::getByTable($array['parentTable'] ?? ''))
            ->withUniqueIdentifier($uniqueIdentifier)
            ->withIdentifier($array['config']['identifier'])
            ->withLabelPath($array['labelPath'] ?? '')
            ->withDescriptionPath($array['descriptionPath'] ?? '')
            ->withUseExistingField($array['config']['useExistingField'] ?? false)
            ->withFieldConfiguration($array['type']->getFieldConfiguration($array['config']));
    }

    public function getFieldType(): FieldType
    {
        return $this->fieldConfiguration->getFieldType();
    }

    public function getUniqueIdentifier(): string
    {
        return $this->uniqueIdentifier;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getLabelPath(): string
    {
        return $this->labelPath;
    }

    public function getDescriptionPath(): string
    {
        return $this->descriptionPath;
    }

    public function getParentContentType(): ContentType
    {
        return $this->parentContentType;
    }

    public function useExistingField(): bool
    {
        return $this->useExistingField;
    }

    public function getTca(): array
    {
        if ($this->fieldConfiguration instanceof FieldConfigurationInterface) {
            return $this->fieldConfiguration->getTca();
        }
        return [];
    }

    public function getFieldConfiguration(): FieldConfigurationInterface
    {
        return $this->fieldConfiguration;
    }

    public function withUniqueIdentifier(string $uniqueIdentifier): TcaFieldDefinition
    {
        $clone = clone $this;
        $clone->uniqueIdentifier = $uniqueIdentifier;
        return $clone;
    }

    public function withIdentifier(string $identifier): TcaFieldDefinition
    {
        $clone = clone $this;
        $clone->identifier = $identifier;
        return $clone;
    }

    public function withLabelPath(string $labelPath): TcaFieldDefinition
    {
        $clone = clone $this;
        $clone->labelPath = $labelPath;
        return $clone;
    }

    public function withDescriptionPath(string $descriptionPath): TcaFieldDefinition
    {
        $clone = clone $this;
        $clone->descriptionPath = $descriptionPath;
        return $clone;
    }

    public function withUseExistingField(bool $useExistingField): TcaFieldDefinition
    {
        $clone = clone $this;
        $clone->useExistingField = $useExistingField;
        return $clone;
    }

    public function withFieldConfiguration(FieldConfigurationInterface $fieldConfiguration): TcaFieldDefinition
    {
        $clone = clone $this;
        $clone->fieldConfiguration = $fieldConfiguration;
        return $clone;
    }

    public function withParentContentType(ContentType $parentContentType): TcaFieldDefinition
    {
        $clone = clone $this;
        $clone->parentContentType = $parentContentType;
        return $clone;
    }
}
