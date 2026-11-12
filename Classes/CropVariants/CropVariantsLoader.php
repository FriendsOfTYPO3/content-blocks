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

namespace TYPO3\CMS\ContentBlocks\CropVariants;

use Symfony\Component\Finder\Finder;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\ContentBlocks\Yaml\ContentBlocksYamlParserInterface;
use TYPO3\CMS\Core\Package\PackageManager;

/**
 * Loader for central Crop Variants. A Crop Variant is defined once and can be
 * referenced by its identifier in the `cropVariants` option of File fields.
 *
 * Crop Variants are loaded from these folders inside extensions:
 *
 * host_extension
 * |__ ContentBlocks
 *     |__ CropVariants
 *         |__ CropVariantA.yaml
 *         |__ CropVariantB.yaml
 *         |__ SubFolder
 *             |__ CropVariantC.yaml
 *
 * The folder `CropVariants` may contain any number of YAML files which must
 * define an `identifier`. All other options are the Crop Variant configuration
 * as known from the TCA option `cropVariants`.
 *
 * Crop Variants are only needed while compiling Content Blocks. The resolved
 * configuration is part of the cached Content Block definition, hence there is
 * no dedicated cache entry.
 *
 * @internal Not part of TYPO3's public API.
 */
readonly class CropVariantsLoader
{
    public function __construct(
        protected PackageManager $packageManager,
        protected ContentBlocksYamlParserInterface $contentBlocksYamlLoader,
    ) {}

    public function loadUncached(): CropVariantsRegistry
    {
        $cropVariantsRegistry = new CropVariantsRegistry();
        foreach ($this->packageManager->getActivePackages() as $package) {
            $pathToCropVariants = $package->getPackagePath() . ContentBlockPathUtility::getRelativeCropVariantsPath();
            if (!is_dir($pathToCropVariants)) {
                continue;
            }
            $finder = new Finder();
            $finder->files()->name('*.yaml')->in($pathToCropVariants);
            foreach ($finder as $splFileInfo) {
                $yamlContent = $this->contentBlocksYamlLoader->parse($splFileInfo->getPathname());
                if (!is_array($yamlContent) || ($yamlContent['identifier'] ?? '') === '') {
                    throw new \RuntimeException('Invalid Crop Variants file in "' . $splFileInfo->getPathname() . '"' . ': Cannot find an identifier.', 1794501605);
                }
                $identifier = (string)$yamlContent['identifier'];
                unset($yamlContent['identifier']);
                $loadedCropVariant = new LoadedCropVariant(
                    hostExtension: $package->getPackageKey(),
                    identifier: $identifier,
                    configuration: $yamlContent,
                    fileName: $splFileInfo->getFilename(),
                );
                $cropVariantsRegistry->register($loadedCropVariant);
            }
        }
        return $cropVariantsRegistry;
    }
}
