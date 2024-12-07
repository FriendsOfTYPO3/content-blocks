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
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentTypeInterface;
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
            ContentType::CONTENT_ELEMENT => ContentElementDefinition::createFromArray($typeDefinition, $table),
            ContentType::PAGE_TYPE => PageTypeDefinition::createFromArray($typeDefinition, $table),
            // @todo It's not ideal that FileType reuses RecordTypeDefinition.
            // @todo It actually only needs showItems, overrideColumns and typeName. Create new interface?
            ContentType::FILE_TYPE, ContentType::RECORD_TYPE => RecordTypeDefinition::createFromArray($typeDefinition, $table)
        };
    }
}
