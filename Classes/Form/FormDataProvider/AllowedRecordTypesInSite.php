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

namespace TYPO3\CMS\ContentBlocks\Form\FormDataProvider;

use TYPO3\CMS\Backend\Form\FormDataProviderInterface;
use TYPO3\CMS\ContentBlocks\SiteSet\ContentBlockSiteRegistry;
use TYPO3\CMS\Core\Schema\Exception\InvalidSchemaTypeException;
use TYPO3\CMS\Core\Schema\TcaSchemaFactory;
use TYPO3\CMS\Core\Site\Entity\Site;

/**
 * @internal
 */
final readonly class AllowedRecordTypesInSite implements FormDataProviderInterface
{
    public function __construct(
        protected ContentBlockSiteRegistry $contentBlockSiteRegistry,
        protected TcaSchemaFactory $tcaSchemaFactory,
        protected AllowedRecordTypeFilter $allowedRecordTypeFilter,
    ) {}

    public function addData(array $result): array
    {
        $tableName = $result['tableName'];
        if (!$this->tcaSchemaFactory->has($tableName)) {
            return $result;
        }
        $schema = $this->tcaSchemaFactory->get($tableName);
        if ($tableName !== $schema->getName()) {
            return $result;
        }
        $site = $result['site'];
        if ($site instanceof Site === false) {
            return $result;
        }
        $contentBlocks = $this->contentBlockSiteRegistry->resolveContentBlocksRegisteredAsSiteSet($site, $tableName);
        // If there is no Content Block registered as Site Set, allow all.
        if ($contentBlocks === []) {
            return $result;
        }
        try {
            $typeField = $schema->getSubSchemaTypeInformation()->getFieldName();
        } catch (InvalidSchemaTypeException) {
            return $result;
        }
        $items = $result['processedTca']['columns'][$typeField]['config']['items'] ?? [];
        if ($items === []) {
            return $result;
        }
        $filteredItems = $this->allowedRecordTypeFilter->filterByAllowedContentBlocks($items, $contentBlocks, $tableName);
        $result['processedTca']['columns'][$typeField]['config']['items'] = $filteredItems;
        return $result;
    }
}
