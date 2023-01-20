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

use TYPO3\CMS\ContentBlocks\Enumeration\FieldType;
use TYPO3\CMS\ContentBlocks\FieldConfiguration\FieldConfigurationInterface;

final class TcaFieldDefinition
{
    private string $identifier = '';
    private string $uniqueIdentifier = '';
    private string $label = '';
    private string $description = '';
    private string $languagePath = '';
    private bool $useExistingField = false;
    private ?FieldConfigurationInterface $fieldConfiguration = null;

    public static function createFromArray(array $array): TcaFieldDefinition
    {
        $uniqueIdentifier = (string)($array['uniqueIdentifier'] ?? '');
        if ($uniqueIdentifier === '') {
            throw new \InvalidArgumentException('The identifier for a TcaFieldDefinition must not be empty.', 1629277138);
        }
        if (!isset($array['config']['type'])) {
            throw new \InvalidArgumentException('The type in the config for a TcaFieldDefinition must not be empty.', 1629277138);
        }

        $self = new self();
        return $self
            ->withUniqueIdentifier($uniqueIdentifier)
            ->withIdentifier($array['config']['identifier'])
            ->withLabel($array['label'] ?? '')
            ->withDescription($array['description'] ?? '')
            ->withLanguagePath($array['config']['languagePath'] ?? '')
            ->withUseExistingField($array['config']['useExistingField'] ?? false)
            ->withFieldConfiguration(FieldType::from($array['config']['type'])->getFieldConfiguration($array['config']));
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

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getTca(): array
    {
        if ($this->fieldConfiguration instanceof FieldConfigurationInterface) {
            return $this->fieldConfiguration->getTca($this->languagePath, $this->useExistingField);
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

    public function withLabel(string $label): TcaFieldDefinition
    {
        $clone = clone $this;
        $clone->label = $label;
        return $clone;
    }

    public function withDescription(string $description): TcaFieldDefinition
    {
        $clone = clone $this;
        $clone->description = $description;
        return $clone;
    }

    public function withLanguagePath(string $languagePath): TcaFieldDefinition
    {
        $clone = clone $this;
        $clone->languagePath = $languagePath;
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

    public function isUseExistingField(): bool
    {
        // @todo overthink "useExistingField" approach
        return $this->useExistingField;
    }
}
