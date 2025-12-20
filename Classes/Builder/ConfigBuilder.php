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

namespace TYPO3\CMS\ContentBlocks\Builder;

use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;
use TYPO3\CMS\ContentBlocks\Definition\Factory\UniqueIdentifierCreator;

readonly class ConfigBuilder
{
    public function build(ContentType $contentType, string $vendor, string $name, ?string $title, null|string|int $typeName, array $defaultConfig): array
    {
        $title ??= $vendor . '/' . $name;
        $yamlConfiguration = match ($contentType) {
            ContentType::CONTENT_ELEMENT => $this->createContentBlockContentElementConfiguration($vendor, $name, $title, $typeName, $defaultConfig),
            ContentType::PAGE_TYPE => $this->createContentBlockPageTypeConfiguration($vendor, $name, $title, $typeName, $defaultConfig),
            ContentType::RECORD_TYPE => $this->createContentBlockRecordTypeConfiguration($vendor, $name, $title, $typeName, $defaultConfig),
            ContentType::FILE_TYPE => $this->createContentBlockFileTypeConfiguration($vendor, $name, $typeName, $defaultConfig),
        };
        return $yamlConfiguration;
    }

    protected function createContentBlockContentElementConfiguration(string $vendor, string $name, string $title, ?string $typeName, array $defaultConfig): array
    {
        $fullName = $vendor . '/' . $name;
        $description = 'Description for ' . ContentType::CONTENT_ELEMENT->getHumanReadable() . ' ' . $fullName;
        $configuration = [
            'table' => 'tt_content',
            'typeField' => 'CType',
            'name' => $fullName,
            'typeName' => UniqueIdentifierCreator::createContentTypeIdentifier($fullName),
            'title' => $title,
            'description' => $description,
            'group' => 'default',
            'prefixFields' => true,
            'prefixType' => 'full',
        ];
        if ($typeName !== '' && $typeName !== null) {
            $configuration['typeName'] = $typeName;
        }
        $mergedConfig = $this->mergeDefaultConfig($defaultConfig, $configuration);
        $mergedConfig['fields'] = [
            [
                'identifier' => 'header',
                'useExistingField' => true,
            ],
        ];
        return $mergedConfig;
    }

    protected function createContentBlockPageTypeConfiguration(string $vendor, string $name, string $title, int $typeName, array $defaultConfig): array
    {
        $fullName = $vendor . '/' . $name;
        $configuration = [
            'table' => 'pages',
            'typeField' => 'doktype',
            'name' => $fullName,
            'title' => $title,
            'typeName' => $typeName,
            'prefixFields' => true,
            'prefixType' => 'full',
        ];
        $mergedConfig = $this->mergeDefaultConfig($defaultConfig, $configuration);
        return $mergedConfig;
    }

    protected function createContentBlockRecordTypeConfiguration(string $vendor, string $name, string $title, ?string $typeName, array $defaultConfig): array
    {
        $fullName = $vendor . '/' . $name;
        $vendorWithoutSeparator = str_replace('-', '', $vendor);
        $nameWithoutSeparator = str_replace('-', '', $name);
        // "tx_" is prepended per default for better grouping in the New Record view.
        // Otherwise, this would be listed in "System Records".
        $table = 'tx_' . $vendorWithoutSeparator . '_' . $nameWithoutSeparator;
        $labelField = 'title';
        $configuration = [
            'name' => $fullName,
            'table' => $table,
            'title' => $title,
            'prefixFields' => false,
            'labelField' => $labelField,
            'security' => [
                'ignorePageTypeRestriction' => true,
            ],
        ];
        if ($typeName !== '' && $typeName !== null) {
            $configuration['typeName'] = $typeName;
        }
        $mergedConfig = $this->mergeDefaultConfig($defaultConfig, $configuration);
        $mergedConfig['fields'] = [
            [
                'identifier' => $labelField,
                'type' => 'Text',
                'label' => 'Title',
            ],
        ];
        return $mergedConfig;
    }

    protected function createContentBlockFileTypeConfiguration(string $vendor, string $name, ?string $typeName, array $defaultConfig): array
    {
        $fullName = $vendor . '/' . $name;
        $configuration = [
            'name' => $fullName,
            'table' => 'sys_file_reference',
        ];
        if ($typeName !== '' && $typeName !== null) {
            $configuration['typeName'] = $typeName;
        }
        $mergedConfig = $this->mergeDefaultConfig($defaultConfig, $configuration);
        $mergedConfig['fields'] = [
            [
                'identifier' => 'image_overlay_palette',
                'type' => 'Palette',
                'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette',
                'fields' => [
                    [
                        'identifier' => 'alternative',
                        'useExistingField' => true,
                    ],
                    [
                        'identifier' => 'description',
                        'useExistingField' => true,
                    ],
                    [
                        'type' => 'Linebreak',
                    ],
                    [
                        'identifier' => 'link',
                        'useExistingField' => true,
                    ],
                    [
                        'identifier' => 'title',
                        'useExistingField' => true,
                    ],
                    [
                        'type' => 'Linebreak',
                    ],
                    [
                        'identifier' => 'example_custom_field',
                        'type' => 'Text',
                        'label' => 'My custom Field',
                    ],
                    [
                        'type' => 'Linebreak',
                    ],
                    [
                        'identifier' => 'crop',
                        'useExistingField' => true,
                    ],
                ],
            ],
        ];
        return $mergedConfig;
    }

    private function mergeDefaultConfig(array $defaultConfig, array $additionalConfig): array
    {
        $mergedConfig = array_replace($additionalConfig, $defaultConfig);
        return $mergedConfig;
    }
}
