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

namespace TYPO3\CMS\ContentBlocks\Service;

use Composer\Util\Filesystem;
use TYPO3\CMS\ContentBlocks\Loader\ParsedContentBlock;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class PublishAssetsService
{
    /**
     * @param ParsedContentBlock[] $parsedContentBlocks
     */
    public function publishAssets(array $parsedContentBlocks): void
    {
        // Publishing assets is only necessary in composer mode
        if (!Environment::isComposerMode()) {
            return;
        }

        // @todo: decide if we can use Composer\Util\Filesystem here?
        $filesstem = new Filesystem();
        // check if the assets path exists or create it
        $assetsPath = $filesstem->normalizePath(Environment::getPublicPath() . '/_assets/cb');
        // cleanup to prepend for old symlinks
        $filesstem->removeDirectory($assetsPath);
        // recreate the assets path
        $filesstem->ensureDirectoryExists($assetsPath);
        // create symlinks for each content block
        foreach ($parsedContentBlocks as $parsedContentBlock) {
            $absolutContentBlockPublicPath = GeneralUtility::getFileAbsFileName(
                $parsedContentBlock->getPackagePath() . ContentBlockPathUtility::getPublicPathSegment()
            );
            $cbAssetsPathDestination = $assetsPath . '/' . $parsedContentBlock->getName();
            if (!$filesstem->isSymlinkedDirectory($cbAssetsPathDestination)) {
                // create "vendor" parent directory if it does not exist
                $filesstem->ensureDirectoryExists(dirname($cbAssetsPathDestination));
                // create symlink
                $filesstem->relativeSymlink($absolutContentBlockPublicPath, $cbAssetsPathDestination);
            }
        }
    }
}
