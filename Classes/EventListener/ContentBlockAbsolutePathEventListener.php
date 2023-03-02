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

use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\Core\Utility\Event\AbsoluteFileNameResolvingEvent;

class ContentBlockAbsolutePathEventListener
{
    public function __invoke(AbsoluteFileNameResolvingEvent $event): void
    {
        if (ContentBlockPathUtility::isContentBlockPath($event->getFileName())) {
            $parts = explode('/', substr($event->getFileName(), 3));
            $vendor = $parts[0];
            $package = $parts[1];
            $path = implode('/', array_slice($parts, 2));
            $event->setAbsolutePath(ContentBlockPathUtility::getAbsoluteContentBlockPath($package, $vendor) . '/' . $path);
        }
    }
}
