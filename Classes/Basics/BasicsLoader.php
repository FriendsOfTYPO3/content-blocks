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

namespace TYPO3\CMS\ContentBlocks\Basics;

use Symfony\Component\Finder\Finder;
use Symfony\Component\VarExporter\LazyObjectInterface;
use Symfony\Component\Yaml\Yaml;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\Core\Cache\Frontend\PhpFrontend;
use TYPO3\CMS\Core\Package\PackageManager;

/**
 * @internal Not part of TYPO3's public API.
 */
class BasicsLoader
{
    protected BasicsRegistry $basicsRegistry;

    public function __construct(
        protected readonly PackageManager $packageManager,
        protected readonly LazyObjectInterface|PhpFrontend $cache,
    ) {}

    public function load(): BasicsRegistry
    {
        if (!$this->cache->isLazyObjectInitialized()) {
            $this->basicsRegistry = $this->basicsRegistry ?? $this->loadUncached();
            return $this->basicsRegistry;
        }
        if (is_array($basics = $this->getFromCache())) {
            $this->basicsRegistry = new BasicsRegistry();
            foreach ($basics as $basic) {
                $loadedBasic = LoadedBasic::fromArray($basic);
                $this->basicsRegistry->register($loadedBasic);
            }
            return $this->basicsRegistry;
        }
        $this->basicsRegistry = $this->basicsRegistry ?? $this->loadUncached();
        $this->setCache();
        return $this->basicsRegistry;
    }

    public function loadUncached(): BasicsRegistry
    {
        $basicsRegistry = new BasicsRegistry();
        foreach ($this->packageManager->getActivePackages() as $package) {
            $pathToBasics = $package->getPackagePath() . ContentBlockPathUtility::getRelativeBasicsPath();
            if (!is_dir($pathToBasics)) {
                continue;
            }
            $finder = new Finder();
            $finder->files()->name('*.yaml')->depth(0)->in($pathToBasics);
            foreach ($finder as $splFileInfo) {
                $yamlContent = Yaml::parseFile($splFileInfo->getPathname());
                if (!is_array($yamlContent) || ($yamlContent['identifier'] ?? '') === '') {
                    throw new \RuntimeException('Invalid Basics file in "' . $splFileInfo->getPathname() . '"' . ': Cannot find an identifier.', 1689095524);
                }
                $loadedBasic = LoadedBasic::fromArray($yamlContent, $package->getPackageKey());
                $basicsRegistry->register($loadedBasic);
            }
        }
        return $basicsRegistry;
    }

    public function initializeCache(): void
    {
        $this->cache->initializeLazyObject();
        if (isset($this->basicsRegistry) && $this->getFromCache() === false) {
            $this->setCache();
        }
    }

    protected function getFromCache(): false|array
    {
        return $this->cache->require('content-blocks-basics');
    }

    protected function setCache(): void
    {
        $cache = array_map(fn(LoadedBasic $basic): array => $basic->toArray(), $this->basicsRegistry->getAllBasics());
        $this->cache->set('content-blocks-basics', 'return ' . var_export($cache, true) . ';');
    }
}
