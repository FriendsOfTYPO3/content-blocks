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

use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\Core\Configuration\Loader\YamlFileLoader;
use TYPO3\CMS\Core\Package\PackageManager;

class BasicsLoader
{
    public function __construct(
        protected readonly BasicsRegistry $basicsRegistry,
        protected readonly PackageManager $packageManager,
        protected readonly YamlFileLoader $yamlFileLoader,
    ) {
    }

    public function load(): void
    {
        foreach ($this->packageManager->getActivePackages() as $package) {
            $pathToBasics = $package->getPackagePath() . ContentBlockPathUtility::getRelativeBasicsPath();
            if (is_file($pathToBasics)) {
                $yaml = $this->yamlFileLoader->load($pathToBasics);
                foreach ($yaml['Basics'] ?? [] as $basic) {
                    $loadedBasic = LoadedBasic::fromArray($basic);
                    $this->basicsRegistry->register($loadedBasic);
                }
            }
        }
    }
}
