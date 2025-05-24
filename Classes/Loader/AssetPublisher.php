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

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class AssetPublisher
{
    /**
     * @param LoadedContentBlock[] $loadedContentBlocks
     */
    public function publishAssets(array $loadedContentBlocks): void
    {
        $fileSystem = new Filesystem();
        foreach ($loadedContentBlocks as $loadedContentBlock) {
            $hostExtension = $loadedContentBlock->getHostExtension();
            $contentBlockExtPublicPath = $loadedContentBlock->getExtPath() . '/' . ContentBlockPathUtility::getAssetsFolder();
            $contentBlockAbsolutePublicPath = GeneralUtility::getFileAbsFileName($contentBlockExtPublicPath);
            // If the Content Block does not have an Assets folder, nothing to publish here.
            if (!file_exists($contentBlockAbsolutePublicPath)) {
                continue;
            }
            $hostAbsolutePublicContentBlockBasePath = ContentBlockPathUtility::getHostAbsolutePublicContentBlockBasePath($hostExtension);
            // Prevent symlinks from being added to git index.
            $gitIgnorePath = $hostAbsolutePublicContentBlockBasePath . '/.gitignore';
            if (!file_exists($gitIgnorePath)) {
                GeneralUtility::mkdir_deep($hostAbsolutePublicContentBlockBasePath);
                file_put_contents($gitIgnorePath, '*');
            }
            $hostAbsolutePublicContentBlockBasePathWithVendor = $hostAbsolutePublicContentBlockBasePath . '/' . $loadedContentBlock->getVendor();
            $contentBlockRelativePublicPath = $fileSystem->makePathRelative(
                $contentBlockAbsolutePublicPath,
                $hostAbsolutePublicContentBlockBasePathWithVendor
            );
            $hostAbsolutePublicContentBlockPath = ContentBlockPathUtility::getHostAbsolutePublicContentBlockPath(
                $hostExtension,
                $loadedContentBlock->getName(),
            );
            try {
                $fileSystem->symlink($contentBlockRelativePublicPath, $hostAbsolutePublicContentBlockPath);
            } catch (IOException) {
                $fileSystem->mirror($contentBlockAbsolutePublicPath, $hostAbsolutePublicContentBlockPath);
            }
        }
    }
}
