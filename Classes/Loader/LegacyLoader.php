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

use Symfony\Component\Finder\Finder;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\Core\Cache\Frontend\PhpFrontend;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class LegacyLoader extends AbstractLoader implements LoaderInterface
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

        $legacyPath = ContentBlockPathUtility::getAbsoluteContentBlockLegacyPath();
        GeneralUtility::mkdir_deep($legacyPath);
        $result = [];
        $cbFinder = new Finder();
        $cbFinder->directories()->depth(0)->in($legacyPath);

        foreach ($cbFinder as $splPath) {
            if (!is_readable($splPath->getPathname() . '/composer.json')) {
                throw new \RuntimeException('Cannot read or find composer.json file in "' . $splPath->getPathname() . '"' . '/composer.json', 1674224824);
            }
            $composerJson = json_decode(file_get_contents($splPath->getPathname() . '/composer.json'), true);
            if (($composerJson['type'] ?? '') !== 'typo3-content-block') {
                continue;
            }
            [$vendor, $package] = explode('/', $composerJson['name']);
            $result[] = $this->loadPackageConfiguration($package, $vendor, $composerJson);
        }
        $cache = array_map(fn (ParsedContentBlock $contentBlock) => $contentBlock->toArray(), $result);
        $this->cache->set('content-blocks', 'return ' . var_export($cache, true) . ';');
        $tableDefinitionCollection = TableDefinitionCollection::createFromArray($result);
        $this->tableDefinitionCollection = $tableDefinitionCollection;
        return $this->tableDefinitionCollection;
    }
}
