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

final class TcaFieldDefinition
{
    private ?FieldType $fieldType = null;
    /** the identifier is the name of the columnin TCA and database */
    private string $identifier = '';
    /** the name is how to call this field in the fluid template */
    private string $name = '';
    private string $label = '';
    private string $description = '';
    /**
     * @var array<string, mixed>
     */
    private array $config = [];

    private string $languagePath = '';

    public static function createFromArray(array $array): TcaFieldDefinition
    {
        $identifier = (string)($array['identifier'] ?? '');
        if ($identifier === '') {
            throw new \InvalidArgumentException('The identifier for a TcaFieldDefinition must not be empty', 1629277138);
        }
        if (!isset($array['config']['type'])) {
            throw new \InvalidArgumentException('The type in the config for a TcaFieldDefinition must not be empty', 1629277138);
        }

        $self = new self();
        return $self
            ->withIdentifier($identifier)
            ->withName($array['config']['identifier'])
            ->withLabel($array['label'] ?? '')
            ->withDescription($array['description'] ?? '')
            ->withConfig($array['config'] ?? [])
            ->withLanguagePath($array['config']['languagePath'] ?? '')
            ->withFieldType($array['config']['type']);
    }

    public function toArray(): array
    {
        return [
            'fieldType' => $this->fieldType->name,
            'identifier' => $this->identifier,
            'label' => $this->label,
            'description' => $this->description,
            'name' => $this->name,
            'languagePath' => $this->languagePath,
        ];
    }

    public function getFieldType(): FieldType
    {
        return $this->fieldType;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getName(): string
    {
        return $this->name;
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
        return $this->fieldType
            ->getFieldTypeConfiguration($this->config)
            ->getTca();
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

    public function withConfig(array $config): TcaFieldDefinition
    {
        $clone = clone $this;
        $clone->config = $config;
        return $clone;
    }

    public function withFieldType(string $type): TcaFieldDefinition
    {
        $clone = clone $this;
        $clone->fieldType = FieldType::from($type);
        return $clone;
    }

    public function withName(string $name): TcaFieldDefinition
    {
        $clone = clone $this;
        $clone->name = $name;
        return $clone;
    }

    public function withLanguagePath(string $languagePath): TcaFieldDefinition
    {
        $clone = clone $this;
        $clone->languagePath = $languagePath;
        return $clone;
    }

    public function isUseExistingField(): bool
    {
        return $this->fieldType
            ->getFieldTypeConfiguration($this->config)
            ->useExistingField;
    }
}
