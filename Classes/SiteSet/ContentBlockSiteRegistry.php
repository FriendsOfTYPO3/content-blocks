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

namespace TYPO3\CMS\ContentBlocks\SiteSet;

use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;
use TYPO3\CMS\ContentBlocks\Loader\LoadedContentBlock;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Set\SetRegistry;

/**
 * @internal
 */
readonly class ContentBlockSiteRegistry
{
    public function __construct(
        protected SetRegistry $setRegistry,
        protected ContentBlockRegistry $contentBlockRegistry,
    ) {}

    /**
     * @return array<LoadedContentBlock>
     */
    public function resolveContentBlocksRegisteredAsSiteSet(
        Site $site,
        ContentType $contentType = ContentType::CONTENT_ELEMENT
    ): array {
        $registeredContentBlocksTypeNames = [];
        $siteSets = $this->setRegistry->getSets(...$site->getSets());
        foreach ($siteSets as $siteSet) {
            if ($this->contentBlockRegistry->hasContentBlock($siteSet->name)) {
                $contentBlock = $this->contentBlockRegistry->getContentBlock($siteSet->name);
                if ($contentBlock->getContentType() !== $contentType) {
                    continue;
                }
                $registeredContentBlocksTypeNames[] = $contentBlock;
            }
        }
        return $registeredContentBlocksTypeNames;
    }
}
