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

namespace TYPO3\CMS\ContentBlocks\Cache;

use TYPO3\CMS\ContentBlocks\Basics\BasicsLoader;
use TYPO3\CMS\ContentBlocks\Definition\Factory\TableDefinitionCollectionFactory;
use TYPO3\CMS\ContentBlocks\Loader\ContentBlockLoader;

/**
 * The Content Blocks cache is lazy and needs to be initialized
 * as soon as bootstrapping is complete.
 */
class InitializeContentBlockCache
{
    public function __construct(
        protected readonly BasicsLoader $basicsLoader,
        protected readonly ContentBlockLoader $contentBlockLoader,
        protected readonly TableDefinitionCollectionFactory $tableDefinitionCollectionFactory,
    ) {}

    public function __invoke(): void
    {
        $this->basicsLoader->initializeCache();
        $this->contentBlockLoader->initializeCache();
        $this->tableDefinitionCollectionFactory->initializeCache();
    }
}
