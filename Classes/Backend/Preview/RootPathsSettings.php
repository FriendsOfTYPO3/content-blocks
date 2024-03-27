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

namespace TYPO3\CMS\ContentBlocks\Backend\Preview;

use TYPO3\CMS\Backend\Utility\BackendUtility;

/**
 * @internal Not part of TYPO3's public API.
 */
class RootPathsSettings
{
    /**
     * @return array<int, string>
     */
    public function getContentBlocksPartialRootPaths(int $pageUid): array
    {
        $partialRootPaths = [];
        foreach ($this->getContentBlocksPageTsPartialRootPaths($pageUid) as $rootPath) {
            $partialRootPaths[] = $rootPath;
        }
        return $partialRootPaths;
    }

    /**
     * @return array<int, string>
     */
    public function getContentBlocksLayoutRootPaths(int $pageUid): array
    {
        $layoutRootPaths = [];
        foreach ($this->getContentBlocksPageTsLayoutRootPaths($pageUid) as $rootPath) {
            $layoutRootPaths[] = $rootPath;
        }
        return $layoutRootPaths;
    }

    /**
     * @return string[]
     */
    protected function getContentBlocksPageTsPartialRootPaths(int $pageUid): array
    {
        $contentBlocksConfig = $this->getContentBlocksPageTsConfig($pageUid);
        $contentBlocksConfigPartialRootPaths = $contentBlocksConfig['view.']['partialRootPaths.'] ?? [];
        return $contentBlocksConfigPartialRootPaths;
    }

    /**
     * @return string[]
     */
    protected function getContentBlocksPageTsLayoutRootPaths(int $pageUid): array
    {
        $contentBlocksConfig = $this->getContentBlocksPageTsConfig($pageUid);
        $contentBlocksConfigLayoutRootPaths = $contentBlocksConfig['view.']['layoutRootPaths.'] ?? [];
        return $contentBlocksConfigLayoutRootPaths;
    }

    protected function getContentBlocksPageTsConfig(int $pageUid): array
    {
        $pageTsConfig = BackendUtility::getPagesTSconfig($pageUid);
        $contentBlocksConfig = $pageTsConfig['tx_content_blocks.'] ?? [];
        return $contentBlocksConfig;
    }
}
