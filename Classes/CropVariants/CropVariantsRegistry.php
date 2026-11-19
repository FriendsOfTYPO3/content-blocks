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

/**
 * Registry class for loaded Crop Variants filled by CropVariantsLoader. Used in
 * CropVariantsService to resolve Crop Variant references in config.yaml files.
 *
 * @internal Not part of TYPO3's public API.
 */
final class CropVariantsRegistry
{
    /**
     * @var LoadedCropVariant[]
     */
    private array $cropVariants = [];

    public function register(LoadedCropVariant $cropVariant): void
    {
        if ($this->hasCropVariant($cropVariant->getIdentifier())) {
            throw new \RuntimeException(
                'The Content Block Crop Variant "' . $cropVariant->getIdentifier() . '" already exists. Please choose another identifier.',
                1794501603
            );
        }
        $this->cropVariants[$cropVariant->getIdentifier()] = $cropVariant;
    }

    public function hasCropVariant(string $identifier): bool
    {
        return array_key_exists($identifier, $this->cropVariants);
    }

    public function getCropVariant(string $identifier): LoadedCropVariant
    {
        if (!$this->hasCropVariant($identifier)) {
            throw new \OutOfBoundsException('Crop Variant with identifier "' . $identifier . '" is not registered.', 1794501604);
        }
        return $this->cropVariants[$identifier];
    }

    /**
     * @return array<LoadedCropVariant>
     */
    public function getAllCropVariants(): array
    {
        return $this->cropVariants;
    }
}
