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
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;
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
        $site = $event->request->getAttribute('site');
        if ($site instanceof Site === false) {
            return;
        }
        $registeredRecordTypes = $this->contentBlockSiteRegistry->resolveContentBlocksRegisteredAsSiteSet($site, ContentType::RECORD_TYPE);
        // If there are no Record Types included as Site Set, allow all.
        if ($registeredRecordTypes === []) {
            return;
        }
        foreach ($event->groupedCreationLinks as $groupName => $group) {
            if ($groupName === 'pages' || $groupName === 'content') {
                continue;
            }
            foreach ($group['items'] as $mainType => $item) {
                if (array_key_exists('types', $item)) {
                    foreach ($item['types'] as $type => $typeItem) {
                        $isRecordAllowed = $this->isRecordAllowedForSubType($registeredRecordTypes, $mainType, $type);
                        if ($isRecordAllowed === false) {
                            unset($event->groupedCreationLinks[$groupName]['items'][$mainType]['types'][$type]);
                            if ($event->groupedCreationLinks[$groupName]['items'][$mainType]['types'] === []) {
                                unset($event->groupedCreationLinks[$groupName]['items'][$mainType]);
                            }
                        }
                    }
                    continue;
                }
                $isRecordAllowed = $this->isRecordAllowedForType($registeredRecordTypes, $mainType);
                if ($isRecordAllowed === false) {
                    unset($event->groupedCreationLinks[$groupName]['items'][$mainType]);
                }
            }
        }
    }

    /**
     * @param array<LoadedContentBlock> $registeredContentBlocks
     */
    protected function isRecordAllowedForSubType(array $registeredContentBlocks, string $mainType, string $subType): bool
    {
        $contentBlock = $this->contentBlockRegistry->getByTypeName($mainType, $subType);
        if ($contentBlock === null) {
            return true;
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

    /**
     * @param array<LoadedContentBlock> $registeredContentBlocks
     */
    protected function isRecordAllowedForType(array $registeredContentBlocks, string $mainType): bool
    {
        $contentBlock = $this->contentBlockRegistry->getByTypeName($mainType, '1');
        if ($contentBlock === null) {
            return true;
        }
        $contentBlockTableNames = array_map(fn(LoadedContentBlock $contentBlock) => $contentBlock->getYaml()['table'], $registeredContentBlocks);
        $isAllowed = in_array($mainType, $contentBlockTableNames, true);
        return $isAllowed;
    }
}
