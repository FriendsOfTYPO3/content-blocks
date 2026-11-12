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
 * Data object for a loaded `CropVariant`. Has information about the host
 * extension, the identifier and the crop variant configuration, which is
 * passed as-is to the TCA option `cropVariants` of a File field.
 *
 * @internal Not part of TYPO3's public API.
 */
final readonly class LoadedCropVariant
{
    public function __construct(
        private string $hostExtension,
        private string $identifier,
        private array $configuration,
        private string $fileName,
    ) {}

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    public function getHostExtension(): string
    {
        return $this->hostExtension;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }
}
