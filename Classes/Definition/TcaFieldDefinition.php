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
    private string $identifier = '';
    private string $label = '';
    private string $description = '';
    /**
     * @var array<string, mixed>
     */
    public array $realTca = [];

    public static function createFromArray(array $array): TcaFieldDefinition
    {
        $identifier = (string)($array['identifier'] ?? '');
        if ($identifier === '') {
            throw new \InvalidArgumentException('The identifier for a TcaFieldDefinition must not be empty', 1629277138);
        }

        $self = new self();
        return $self
            ->withIdentifier($identifier)
            ->withLabel($array['label'] ?? '')
            ->withDescription($array['description'] ?? '')
            ->withRealTca($array['realTca'] ?? []);
    }

    public function toArray(): array
    {
        return [
            'fieldType' => $this->fieldType->name,
            'identifier' => $this->identifier,
            'label' => $this->label,
            'description' => $this->description,
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

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getRealTca(): array
    {
        return $this->realTca;
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

    public function withRealTca(array $realTca): TcaFieldDefinition
    {
        $clone = clone $this;
        $clone->realTca = $realTca;
        return $clone;
    }
}
