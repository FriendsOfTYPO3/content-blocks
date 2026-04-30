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

namespace TYPO3\CMS\ContentBlocks\EventListener;

use TYPO3\CMS\Backend\Controller\Event\ModifyNewContentElementWizardItemsEvent;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;
use TYPO3\CMS\ContentBlocks\Loader\LoadedContentBlock;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\ContentBlocks\SiteSet\ContentBlockSiteRegistry;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Site\Entity\Site;

/**
 * @internal
 */
#[AsEventListener('RestrictContentBlockInNewContentElementWizard')]
readonly class RestrictContentBlockInNewContentElementWizard
{
    public function __construct(
        protected ContentBlockRegistry $contentBlockRegistry,
        protected ContentBlockSiteRegistry $contentBlockSiteRegistry,
    ) {}

    public function __invoke(ModifyNewContentElementWizardItemsEvent $event): void
    {
        /** @var Site $site */
        $site = $event->getRequest()->getAttribute('site');
        $table = ContentType::CONTENT_ELEMENT->getTable();
        $contentBlocks = $this->contentBlockSiteRegistry->resolveContentBlocksRegisteredAsSiteSet($site, ContentType::CONTENT_ELEMENT);
        // If there is no Content Block registered as Site Set, allow all.
        if ($contentBlocks === []) {
            return;
        }
        $contentBlockTypeNames = array_map(fn(LoadedContentBlock $contentBlock) => $contentBlock->getYaml()['typeName'], $contentBlocks);
        $wizardItems = $event->getWizardItems();
        foreach ($wizardItems as $identifier => $item) {
            $typeName = $item['defaultValues']['CType'] ?? null;
            if ($typeName === null) {
                continue;
            }
            if ($this->contentBlockRegistry->getByTypeName($table, $typeName) === null) {
                continue;
            }
            if (in_array($typeName, $contentBlockTypeNames, true)) {
                continue;
            }
            unset($wizardItems[$identifier]);
        }
        $event->setWizardItems($wizardItems);
    }
}
