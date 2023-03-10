<?php

declare(strict_types=1);

/*
 * This file is part of TYPO3 CMS-based extension "b13/make" by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

namespace TYPO3\CMS\ContentBlocks;

use TYPO3\CMS\Core\Package\Exception\UnknownPackageException;
use TYPO3\CMS\Core\Package\PackageInterface;
use TYPO3\CMS\Core\Package\PackageManager;

/**
 * Resolves packages using the TYPO3 PackageManager
 */
class PackageResolver
{
    /** @var PackageManager */
    protected $packageManager;

    public function __construct(PackageManager $packageManager)
    {
        $this->packageManager = $packageManager;
    }

    public function resolvePackage(string $extensionKey): ?PackageInterface
    {
        try {
            return $this->packageManager->getPackage($extensionKey);
        } catch (UnknownPackageException $e) {
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
