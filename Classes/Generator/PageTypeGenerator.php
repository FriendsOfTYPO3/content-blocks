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

namespace TYPO3\CMS\ContentBlocks\Generator;

use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentTypeInterface;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\Core\Core\Event\BootCompletedEvent;
use TYPO3\CMS\Core\DataHandling\PageDoktypeRegistry;

/**
 * @internal Not part of TYPO3's public API.
 */
class PageTypeGenerator
{
    public function __construct(
        protected readonly TableDefinitionCollection $tableDefinitionCollection,
        protected readonly PageDoktypeRegistry $pageDoktypeRegistry,
    ) {}

    public function __invoke(BootCompletedEvent $event): void
    {
        if (!$this->tableDefinitionCollection->hasTable(ContentType::PAGE_TYPE->getTable())) {
            return;
        }
        $tableDefinition = $this->tableDefinitionCollection->getTable(ContentType::PAGE_TYPE->getTable());
        /** @var ContentTypeInterface $typeDefinition */
        foreach ($tableDefinition->getContentTypeDefinitionCollection() ?? [] as $typeDefinition) {
            $this->pageDoktypeRegistry->add($typeDefinition->getTypeName(), []);
        }
    }
}
