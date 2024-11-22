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

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\Core\Cache\Frontend\PhpFrontend;
use TYPO3\CMS\Core\Package\PackageManager;

/**
 * Main bootstrap loader for Basics. Basics are like partials for
 * Content Blocks. They can contain an arbitrary set of fields.
 * These may contain Tabs, Palettes or even a single field.
 *
 * Basics are loaded from these folders inside extensions:
 *
 * host_extension
 * |__ ContentBlocks
 *     |__ Basics
 *         |__ BasicA.yaml
 *         |__ BasicB.yaml
 *         |__ SubFolder
 *             |__ BasicC.yaml
 *
 * The folder `Basics` may contain any number of YAML files which must
 * define an `identifier` and a `fields` list. It is possible to structure
 * Basics in sub-folders.
 *
 * Basics are cached in a dedicated cache entry.
 *
 * @internal Not part of TYPO3's public API.
 */
class BasicsLoader
{
    protected BasicsRegistry $basicsRegistry;

    public function __construct(
        protected readonly PackageManager $packageManager,
        #[Autowire(service: 'cache.core')]
        protected readonly PhpFrontend $cache,
    ) {}

    public function load(): BasicsRegistry
    {
        if (isset($this->basicsRegistry)) {
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
        $this->basicsRegistry = $this->loadUncached();
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
            $finder->files()->name('*.yaml')->in($pathToBasics);
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

    protected function getFromCache(): false|array
    {
        return $this->cache->require('ContentBlocks_Basics');
    }

    protected function setCache(): void
    {
        $cache = array_map(fn(LoadedBasic $basic): array => $basic->toArray(), $this->basicsRegistry->getAllBasics());
        $this->cache->set('ContentBlocks_Basics', 'return ' . var_export($cache, true) . ';');
    }
}
