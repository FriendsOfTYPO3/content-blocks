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

namespace TYPO3\CMS\ContentBlocks\Tests\Unit\CropVariants;

use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\ContentBlocks\CropVariants\CropVariantsRegistry;
use TYPO3\CMS\ContentBlocks\CropVariants\LoadedCropVariant;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class CropVariantsRegistryTest extends UnitTestCase
{
    #[Test]
    public function registeredCropVariantCanBeRetrieved(): void
    {
        $cropVariant = new LoadedCropVariant('foo', 'vendor/desktop', ['title' => 'Desktop'], 'Desktop.yaml');
        $cropVariantsRegistry = new CropVariantsRegistry();

        $cropVariantsRegistry->register($cropVariant);

        self::assertSame($cropVariant, $cropVariantsRegistry->getCropVariant('vendor/desktop'));
    }

    #[Test]
    public function registeringDuplicateIdentifierThrowsException(): void
    {
        $cropVariantsRegistry = new CropVariantsRegistry();
        $cropVariantsRegistry->register(new LoadedCropVariant('foo', 'vendor/desktop', [], 'Desktop.yaml'));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionCode(1794501603);

        $cropVariantsRegistry->register(new LoadedCropVariant('bar', 'vendor/desktop', [], 'Desktop.yaml'));
    }
}
