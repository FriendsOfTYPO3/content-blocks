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

namespace TYPO3\CMS\ContentBlocks\Loader;

use Composer\InstalledVersions;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\Core\Cache\Frontend\PhpFrontend;

class ComposerLoader extends AbstractLoader implements LoaderInterface
{
    protected ?TableDefinitionCollection $tableDefinitionCollection = null;

    public function __construct(
        protected PhpFrontend $cache
    ) {
    }

    public function load(): TableDefinitionCollection
    {
        if ($this->tableDefinitionCollection instanceof TableDefinitionCollection) {
            return $this->tableDefinitionCollection;
        }

        if ($this->cache->has('content-blocks')) {
            $contentBlocks = $this->cache->require('content-blocks');
            $contentBlocks = array_map(fn ($contentBlock) => ParsedContentBlock::fromArray($contentBlock), $contentBlocks);
            $tableDefinitionCollection = TableDefinitionCollection::createFromArray($contentBlocks);
            $this->tableDefinitionCollection = $tableDefinitionCollection;
            return $this->tableDefinitionCollection;
        }

        $result = [];
        $contentBlocks = InstalledVersions::getInstalledPackagesByType('typo3-content-block');
        foreach ($contentBlocks as $contentBlock) {
            [$vendor, $package] = explode('/', $contentBlock);
            $result[] = $this->loadPackageConfiguration($package, $vendor);
        }
        $cache = array_map(fn (ParsedContentBlock $contentBlock) => $contentBlock->toArray(), $result);
        $this->cache->set('content-blocks', 'return ' . var_export($cache, true) . ';');
        $tableDefinitionCollection = TableDefinitionCollection::createFromArray($result);
        $this->tableDefinitionCollection = $tableDefinitionCollection;
        return $this->tableDefinitionCollection;
    }
}
