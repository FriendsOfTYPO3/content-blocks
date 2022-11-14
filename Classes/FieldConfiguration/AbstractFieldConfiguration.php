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

use TYPO3\CMS\ContentBlocks\Enumeration\FieldType;

/**
 * Class AbstractFieldConfiguration
 */
class AbstractFieldConfiguration
{
    private array $rawData = [];

    public ?FieldType $type;

    public string $identifier = '';

    public string $uniqueIdentifier = '';

    public array $path = [];

    public bool $useExistingField = false;

    public bool $isFileField = false;

    public function __construct(array $settings)
    {
        $this->createFromArray($settings);
    }

    /**
     * Fills the properties from array infos
     *
     * (This the createFromArray method is mainly used by the constructor.)
     */
    protected function createFromArray(array $settings): self
    {
        $this->rawData = $settings;
        $this->identifier = $settings['identifier'] ?? '';
        $this->uniqueIdentifier = $settings['_identifier'] ?? '';
        $this->path = $settings['_path'] ?? $this->path;
        $this->useExistingField = (bool)($settings['properties']['required'] ?? $this->useExistingField);

        return $this;
    }

    public function getTcaTemplate(array $contentBlock): array
    {
        return [
            'exclude' => 1,
            'label' => 'LLL:' . $contentBlock['EditorInterfaceXlf'] . ':' . $contentBlock['vendor']
                        . '.' . $contentBlock['package'] . '.' . $this->uniqueIdentifier . '.label',
            'description' => 'LLL:' . $contentBlock['EditorInterfaceXlf'] . ':' . $contentBlock['vendor']
            . '.' . $contentBlock['package'] . '.' . $this->uniqueIdentifier . '.description',
            'config' => [],
        ];
    }

    /**
     * Returns the rawData.
     *
     * The raw data is the plain array which is set by the createFromArray method.
     * (This the createFromArray method is mainly used by the constructor.)
     */
    protected function getRawData(): array
    {
        return $this->rawData;
    }

    public function combinedIdentifierToArray(string $combinedIdentifier): array
    {
        return explode('.', $combinedIdentifier);
    }

    public function arrayToCombinedIdentifier(array $path): string
    {
        return implode('.', $path);
    }

    public function uniqueCombinedIdentifier(string $cType, string $combinedIdentifier): string
    {
        return $cType . '|' . $combinedIdentifier;
    }

    public function splitUniqueCombinedIdentifier($uniqueCombinedIdentifier): array
    {
        return explode('|', $uniqueCombinedIdentifier);
    }

    /**
     * Manage to have SQL compatible column names, prefixed with "cb_".
     * Result: cb_content_blockidentifier_column_path_column_name
     */
    public function uniqueColumnName(string $cType, string $combinedIdentifier): string
    {
        return 'cb_' . str_replace('-', '_', $cType) . '_' . str_replace('-', '_', str_replace('.', '_', $combinedIdentifier));
    }
}
