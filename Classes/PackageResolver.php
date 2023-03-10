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

namespace TYPO3\CMS\ContentBlocks;

use TYPO3\CMS\Core\Package\Exception\UnknownPackageException;
use TYPO3\CMS\Core\Package\PackageInterface;
use TYPO3\CMS\Core\Package\PackageManager;

/**
 * Resolves packages using the TYPO3 PackageManager
 *
 * @internal Not part of TYPO3's public API.
 */
class PackageResolver
{
    public function __construct(protected PackageManager $packageManager)
    {
    }

    public function resolvePackage(string $extensionKey): ?PackageInterface
    {
        try {
            return $this->packageManager->getPackage($extensionKey);
        } catch (UnknownPackageException) {
            return null;
        }
    }

    /**
     * @return PackageInterface[]
     */
    public function getAvailablePackages(): array
    {
        return $this->packageManager->getAvailablePackages();
    }
}
