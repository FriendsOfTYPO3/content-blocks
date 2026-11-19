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

namespace TYPO3\CMS\ContentBlocks\Tests\Unit\JsonSchemaValidation;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\ContentBlocks\JsonSchemaValidation\JsonSchemaValidator;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class CropVariantSchemaTest extends UnitTestCase
{
    public static function cropVariantSchemaValidationWorksAsExpectedDataProvider(): iterable
    {
        yield 'Valid Crop Variant' => [
            'data' => (object)[
                'identifier' => 'vendor/desktop',
                'title' => 'Desktop',
                'allowedAspectRatios' => (object)[
                    'landscape' => (object)[
                        'title' => 'Landscape',
                        'value' => '16 / 9',
                    ],
                ],
                'selectedRatio' => 'landscape',
            ],
            'valid' => true,
        ];

        yield 'Missing identifier' => [
            'data' => (object)[
                'title' => 'Desktop',
            ],
            'valid' => false,
        ];

        yield 'Empty identifier' => [
            'data' => (object)[
                'identifier' => '',
                'title' => 'Desktop',
            ],
            'valid' => false,
        ];

        yield 'Unknown property' => [
            'data' => (object)[
                'identifier' => 'vendor/desktop',
                'unknown' => 'unknown',
            ],
            'valid' => false,
        ];

        yield 'Invalid aspect ratio value' => [
            'data' => (object)[
                'identifier' => 'vendor/desktop',
                'allowedAspectRatios' => (object)[
                    'landscape' => (object)[
                        'value' => '16 by 9',
                    ],
                ],
            ],
            'valid' => false,
        ];
    }

    #[Test]
    #[DataProvider('cropVariantSchemaValidationWorksAsExpectedDataProvider')]
    public function cropVariantSchemaValidationWorksAsExpected(object $data, bool $valid): void
    {
        $jsonSchemaValidator = new JsonSchemaValidator();

        $validationResult = $jsonSchemaValidator->validateCropVariant($data);

        self::assertSame($valid, !$validationResult->hasError());
    }
}
