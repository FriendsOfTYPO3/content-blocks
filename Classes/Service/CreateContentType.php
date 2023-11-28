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

namespace TYPO3\CMS\ContentBlocks\Service;

use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;

/**
 * @internal Not part of TYPO3's public API.
 */
class CreateContentType
{
    public function createContentBlockContentElementConfiguration(
        string $vendor,
        string $name,
        array $fields,
        array $basics = [],
        $group = 'common',
        bool $prefixFields = true,
        string $prefixType = 'full',
        string $table = 'tt_content',
        string $typeField = 'CType',
        ?string $type = ''
    ): array {
        $configuration = [
            'name' => $vendor . '/' . $name,
            'group' => $group,
            'prefixFields' => $prefixFields,
            'prefixType' => $prefixType,
            'table' => $table,
            'typeField' => $typeField,
            'basics' => $basics,
            'fields' => $fields,
        ];
        if ($type !== '' && $type !== null) {
            $configuration['typeName'] = $type;
        }
        return $configuration;
    }

    public function createContentBlockPageTypeConfiguration(string $vendor, string $name, int $type): array
    {
        return [
            'name' => $vendor . '/' . $name,
            'typeName' => $type,
            'prefixFields' => true,
            'prefixType' => 'full',
        ];
    }

    public function createContentBlockRecordTypeConfiguration(string $vendor, string $name, ?string $type = ''): array
    {
        $vendorWithoutSeparator = str_replace('-', '', $vendor);
        $nameWithoutSeparator = str_replace('-', '', $name);
        $configuration = [
            'name' => $vendor . '/' . $name,
            'table' => 'tx_' . $vendorWithoutSeparator . '_domain_model_' . $nameWithoutSeparator,
            'prefixFields' => false,
            'labelField' => 'title',
        ];
        if ($type !== '' && $type !== null) {
            $configuration['typeName'] = $type;
        }
        $configuration['fields'] = [
            [
                'identifier' => 'title',
                'type' => 'Text',
            ],
        ];
        return $configuration;
    }

    public function getBasePath(array $availablePackages, string $extension, ContentType $contentType): string
    {
        return match ($contentType) {
            ContentType::CONTENT_ELEMENT => $availablePackages[$extension]->getPackagePath() . ContentBlockPathUtility::getRelativeContentElementsPath(),
            ContentType::PAGE_TYPE => $availablePackages[$extension]->getPackagePath() . ContentBlockPathUtility::getRelativePageTypesPath(),
            ContentType::RECORD_TYPE => $availablePackages[$extension]->getPackagePath() . ContentBlockPathUtility::getRelativeRecordTypesPath()
        };
    }
}
