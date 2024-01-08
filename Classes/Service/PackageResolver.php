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

use Composer\InstalledVersions;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Package\PackageInterface;
use TYPO3\CMS\Core\Package\PackageManager;

/**
 * Resolves packages using the TYPO3 PackageManager
 *
 * @internal Not part of TYPO3's public API.
 */
class PackageResolver
{
    public function __construct(protected PackageManager $packageManager) {}

    /**
     * @return array<string, PackageInterface>
     */
    public function getAvailablePackages(): array
    {
        $packages = $this->packageManager->getAvailablePackages();
        $packages = $this->removeFrameworkExtensions($packages);
        if (Environment::isComposerMode()) {
            return $this->filterNonLocalComposerPackages($packages);
        }
        return $packages;
    }

    public function getComposerProjectVendor(): string
    {
        if (!Environment::isComposerMode()) {
            return '';
        }
        $rootPackageName = $this->getRootPackageName();
        $parts = explode('/', $rootPackageName);
        $vendor = $parts[0];
        return $vendor;
    }

    /**
     * @param array<string, PackageInterface> $packages
     * @return array<string, PackageInterface>
     */
    protected function removeFrameworkExtensions(array $packages): array
    {
        return array_filter($packages, fn(PackageInterface $package): bool => !$package->getPackageMetaData()->isFrameworkType());
    }

    /**
     * @param array<string, PackageInterface> $packages
     * @return array<string, PackageInterface>
     */
    protected function filterNonLocalComposerPackages(array $packages): array
    {
        $composerLockPath = Environment::getProjectPath() . '/composer.lock';
        if (!file_exists($composerLockPath)) {
            return $packages;
        }
        $composerLock = json_decode(file_get_contents($composerLockPath), true);
        $composerLockPackages = array_merge($composerLock['packages'] ?? [], $composerLock['packages-dev'] ?? []);
        $composerLockMap = [];
        foreach ($composerLockPackages as $package) {
            $composerLockMap[$package['name']] = $package['dist']['type'] ?? null;
        }
        $filterPackages = function (PackageInterface $package) use ($composerLockMap): bool {
            $name = $package->getValueFromComposerManifest('name');
            if (array_key_exists($name, $composerLockMap)) {
                return $composerLockMap[$name] === 'path';
            }
            return $name === $this->getRootPackageName();
        };
        return array_filter($packages, $filterPackages);
    }

    protected function getRootPackageName(): string
    {
        $rootPackageName = InstalledVersions::getRootPackage()['name'];
        return $rootPackageName;
    }
}
