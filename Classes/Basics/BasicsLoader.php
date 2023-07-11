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
use Symfony\Component\Yaml\Yaml;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\Core\Package\PackageManager;

class BasicsLoader
{
    public function __construct(
        protected readonly BasicsRegistry $basicsRegistry,
        protected readonly PackageManager $packageManager,
    ) {
    }

    public function load(): void
    {
        foreach ($this->packageManager->getActivePackages() as $package) {
            $pathToBasics = $package->getPackagePath() . ContentBlockPathUtility::getRelativeBasicsPath();
            if (!is_dir($pathToBasics)) {
                continue;
            }
            $finder = new Finder();
            $finder->files()->name('*.yaml')->depth(0)->in($pathToBasics);
            foreach ($finder as $splFileInfo) {
                $yamlContent = Yaml::parseFile($splFileInfo->getPathname());
                // @todo Think about vendor for Basics and allow only one Basic per file
                if (!is_array($yamlContent) /* || strlen($yamlContent['name'] ?? '') < 3 || !str_contains($yamlContent['name'], '/') */) {
                    throw new \RuntimeException('Invalid Basics file in "' . $splFileInfo->getPathname() /* . '"' . ': Cannot find a valid name in format "vendor/package".' */, 1689095524);
                }
                foreach ($yamlContent['Basics'] ?? [] as $basic) {
                    $loadedBasic = LoadedBasic::fromArray($basic);
                    $this->basicsRegistry->register($loadedBasic);
                }
            }
        }
    }
}
