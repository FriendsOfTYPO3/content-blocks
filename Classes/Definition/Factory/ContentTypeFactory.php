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

namespace TYPO3\CMS\ContentBlocks\Definition\Factory;

use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentElementDefinition;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentTypeIcon;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentTypeInterface;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\PageIconSet;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\PageTypeDefinition;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\RecordTypeDefinition;

/**
 * @internal Not part of TYPO3's public API.
 */
final class ContentTypeFactory
{
    public function create(array $typeDefinition, string $table): ContentTypeInterface
    {
        if (!isset($typeDefinition['identifier']) || $typeDefinition['identifier'] === '') {
            throw new \InvalidArgumentException('Type identifier must not be empty.', 1629292395);
        }

        if ($table === '') {
            throw new \InvalidArgumentException('Type table must not be empty.', 1668858103);
        }
        $contentType = ContentType::getByTable($table);
        return match ($contentType) {
            ContentType::CONTENT_ELEMENT => $this->createContentElementDefinition($typeDefinition, $table),
            ContentType::PAGE_TYPE => $this->createPageTypeDefinition($typeDefinition, $table),
            // @todo It's not ideal that FileType reuses RecordTypeDefinition.
            // @todo It actually only needs showItems, overrideColumns and typeName. Create new interface?
            ContentType::FILE_TYPE, ContentType::RECORD_TYPE => $this->createRecordTypeDefinition($typeDefinition, $table)
        };
    }

    private function createRecordTypeDefinition(array $typeDefinition, string $table): RecordTypeDefinition
    {
        $arguments = $this->prepareCommonArguments($typeDefinition);
        $arguments['table'] = $table;
        $recordTypeDefinition = new RecordTypeDefinition(...$arguments);
        return $recordTypeDefinition;
    }

    private function createContentElementDefinition(array $typeDefinition, string $table): ContentElementDefinition
    {
        $arguments = $this->prepareCommonArguments($typeDefinition);
        $arguments['table'] = $table;
        $arguments['saveAndClose'] = $typeDefinition['saveAndClose'] ?? false;
        $contentElementDefinition = new ContentElementDefinition(...$arguments);
        return $contentElementDefinition;
    }

    private function createPageTypeDefinition(array $typeDefinition, string $table): PageTypeDefinition
    {
        $arguments = $this->prepareCommonArguments($typeDefinition);
        $arguments['table'] = $table;
        $iconHideInMenu = ContentTypeIcon::fromArray($typeDefinition['typeIconHideInMenu'] ?? []);
        $iconRoot = ContentTypeIcon::fromArray($typeDefinition['typeIconRoot'] ?? []);
        $pageIconSet = new PageIconSet($iconHideInMenu, $iconRoot);
        $arguments['pageIconSet'] = $pageIconSet;
        $pageTypeDefinition = new PageTypeDefinition(...$arguments);
        return $pageTypeDefinition;
    }

    private function prepareCommonArguments(array $typeDefinition): array
    {
        $arguments = [];
        $arguments['identifier'] = $typeDefinition['identifier'];
        $arguments['title'] = $typeDefinition['title'];
        $arguments['description'] = $typeDefinition['description'];
        $arguments['typeName'] = $typeDefinition['typeName'];
        $arguments['columns'] = $typeDefinition['columns'] ?? [];
        $arguments['showItems'] = $typeDefinition['showItems'] ?? [];
        $arguments['overrideColumns'] = $typeDefinition['overrideColumns'] ?? [];
        $arguments['vendor'] = $typeDefinition['vendor'] ?? '';
        $arguments['package'] = $typeDefinition['package'] ?? '';
        $arguments['priority'] = $typeDefinition['priority'] ?? 0;
        $arguments['typeIcon'] = ContentTypeIcon::fromArray($typeDefinition['typeIcon'] ?? []);
        $arguments['languagePathTitle'] = $typeDefinition['languagePathTitle'] ?? null;
        $arguments['languagePathDescription'] = $typeDefinition['languagePathDescription'] ?? null;
        $arguments['group'] = $typeDefinition['group'];
        return $arguments;
    }
}
