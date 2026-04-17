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
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Set\SetRegistry;

#[AsEventListener('RestrictContentBlockInNewContentElementWizard')]
readonly class RestrictContentBlockInNewContentElementWizard
{
    public function __construct(
        protected SetRegistry $setRegistry,
        protected ContentBlockRegistry $contentBlockRegistry,
    ) {}

    public function __invoke(ModifyNewContentElementWizardItemsEvent $event): void
    {
        $contentBlocks = $this->resolveContentBlocksRegisteredAsSiteSet($event);
        // If there is no Content Block registered as Site Set, allow all.
        if ($contentBlocks === []) {
            return;
        }
        $wizardItems = $event->getWizardItems();
        foreach ($wizardItems as $identifier => $item) {
            $typeName = $item['defaultValues']['CType'] ?? null;
            if ($typeName === null) {
                continue;
            }
            if ($this->contentBlockRegistry->getByTypeName('tt_content', $typeName) === null) {
                continue;
            }
            if (in_array($typeName, $contentBlocks, true)) {
                continue;
            }
            unset($wizardItems[$identifier]);
        }
        $event->setWizardItems($wizardItems);
    }

    /**
     * @return array<string>
     */
    protected function resolveContentBlocksRegisteredAsSiteSet(ModifyNewContentElementWizardItemsEvent $event): array
    {
        $registeredContentBlocksTypeNames = [];
        /** @var Site $site */
        $site = $event->getRequest()->getAttribute('site');
        $siteSets = $this->setRegistry->getSets(...$site->getSets());
        foreach ($siteSets as $siteSet) {
            if ($this->contentBlockRegistry->hasContentBlock($siteSet->name)) {
                $contentBlock = $this->contentBlockRegistry->getContentBlock($siteSet->name);
                if ($contentBlock->getContentType() !== ContentType::CONTENT_ELEMENT) {
                    continue;
                }
                $registeredContentBlocksTypeNames[] = $contentBlock->getYaml()['typeName'];
            }
        }
        return $registeredContentBlocksTypeNames;
    }
}
