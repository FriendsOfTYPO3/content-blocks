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

use TYPO3\CMS\Backend\Controller\Event\ModifyNewRecordCreationLinksEvent;
use TYPO3\CMS\ContentBlocks\Loader\LoadedContentBlock;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\ContentBlocks\SiteSet\ContentBlockSiteRegistry;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Site\Entity\Site;

#[AsEventListener('RestrictContentBlockInNewRecordView')]
readonly class RestrictContentBlockInNewRecordView
{
    public function __construct(
        protected ContentBlockRegistry $contentBlockRegistry,
        protected ContentBlockSiteRegistry $contentBlockSiteRegistry,
    ) {}

    public function __invoke(ModifyNewRecordCreationLinksEvent $event): void
    {
        /** @var Site $site */
        $site = $event->request->getAttribute('site');
        foreach ($event->groupedCreationLinks as $groupName => $group) {
            if ($groupName === 'pages' || $groupName === 'content') {
                continue;
            }
            foreach ($group['items'] as $mainType => $item) {
                $contentBlocks = $this->contentBlockSiteRegistry->resolveContentBlocksRegisteredAsSiteSet($site, $mainType);
                if (array_key_exists('types', $item)) {
                    // If there is no Content Block registered as Site Set, allow all.
                    if ($contentBlocks === []) {
                        continue;
                    }
                    foreach ($item['types'] as $type => $typeItem) {
                        $isRecordAllowed = $this->isRecordAllowed($contentBlocks, $mainType, $type);
                        if ($isRecordAllowed === false) {
                            unset($event->groupedCreationLinks[$groupName]['items'][$mainType]['types'][$type]);
                            if ($event->groupedCreationLinks[$groupName]['items'][$mainType]['types'] === []) {
                                unset($event->groupedCreationLinks[$groupName]['items'][$mainType]);
                            }
                        }
                    }
                    continue;
                }
                $isRecordAllowed = $this->isRecordAllowed($contentBlocks, $mainType);
                if ($isRecordAllowed === false) {
                    unset($event->groupedCreationLinks[$groupName]['items'][$mainType]);
                }
            }
        }
    }

    /**
     * @param array<LoadedContentBlock> $registeredContentBlocks
     */
    protected function isRecordAllowed(array $registeredContentBlocks, string $mainType, ?string $subType = null): bool
    {
        $contentBlock = $this->contentBlockRegistry->getByTypeName($mainType, $subType ?? '1');
        if ($contentBlock === null) {
            return true;
        }
        if ($subType === null) {
            $contentBlockTableNames = array_map(fn(LoadedContentBlock $contentBlock) => $contentBlock->getYaml()['table'], $registeredContentBlocks);
            $isAllowed = in_array($mainType, $contentBlockTableNames, true);
            return $isAllowed;
        }
        foreach ($registeredContentBlocks as $registeredContentBlock) {
            if ($registeredContentBlock->getYaml()['table'] !== $mainType) {
                continue;
            }
            if ($registeredContentBlock->getYaml()['typeName'] !== $subType) {
                continue;
            }
            return true;
        }
        return false;
    }
}
