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

namespace TYPO3\CMS\ContentBlocks\DataHandler;

use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\DataHandling\DataHandler;

/**
 * @internal Not part of TYPO3's public API.
 */
class ClearBackendPreviewCaches
{
    public function __construct(
        protected readonly CacheManager $cacheManager,
    ) {}

    public function processDatamap_afterAllOperations(DataHandler $dataHandler): void
    {
        // This is just a simple solution for invalidating preview caches after
        // each DataHandler operation. It is not easy to make selective picks
        // as relations can get as complex as needed.
        $this->cacheManager->flushCachesByTag('content_blocks_preview');
    }
}
