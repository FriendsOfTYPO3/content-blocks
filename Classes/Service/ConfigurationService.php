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

use TYPO3\CMS\Core\SingletonInterface;

class ConfigurationService implements SingletonInterface
{
    /**
     * TODO: make base path for ContentBlocks configurable and
     * deliver it due to the configuration
     */
    public function getBasePath(): string
    {
        return 'typo3conf/ext/';
    }

    /**
     * Return the destination path where to write the ContentBlocks.
     * E.g. the 'contentBlocksPackageDirectory'
     *
     * TODO: make the destination path configurable.
     */
    public function getContentBlockDestinationPath(): string
    {
        return $this->getBasePath();
    }

    /**
     * Since there are dicussions of making/using 'src' or 'Resources/Private',
     * or if it should be configurable, this could be a configurable constant.
     */
    public function getContentBlocksPrivatePath()
    {
        return 'Resources/Private';
    }

    /**
     * Since there are dicussions of making/using 'dist' or 'Resources/Public',
     * or if it should be configurable, this could be a configurable constant.
     */
    public function getContentBlocksPublicPath()
    {
        return 'Resources/Public';
    }

    /**
     * If somebody wants to change that anyway in future.
     */
    public function getComposerType(): string
    {
        return 'typo3-contentblock';
    }
}
