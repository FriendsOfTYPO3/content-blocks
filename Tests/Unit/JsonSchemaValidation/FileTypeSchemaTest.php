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

final class FileTypeSchemaTest extends UnitTestCase
{
    public static function fileTypeSchemaValidationWorksAsExpectedDataProvider(): iterable
    {
        yield 'Basic valid file type: image' => [
            'data' => (object)[
                'name' => 'vendor/my-file-type',
                'typeName' => 'image',
            ],
            'valid' => true,
        ];

        yield 'Valid typeName enum: text' => [
            'data' => (object)[
                'name' => 'vendor/my-file-type',
                'typeName' => 'text',
            ],
            'valid' => true,
        ];

        yield 'Valid typeName enum: audio' => [
            'data' => (object)[
                'name' => 'vendor/my-file-type',
                'typeName' => 'audio',
            ],
            'valid' => true,
        ];

        yield 'Valid typeName enum: video' => [
            'data' => (object)[
                'name' => 'vendor/my-file-type',
                'typeName' => 'video',
            ],
            'valid' => true,
        ];

        yield 'Valid typeName enum: application' => [
            'data' => (object)[
                'name' => 'vendor/my-file-type',
                'typeName' => 'application',
            ],
            'valid' => true,
        ];

        yield 'Invalid typeName: unknown' => [
            'data' => (object)[
                'name' => 'vendor/my-file-type',
                'typeName' => 'unknown',
            ],
            'valid' => false,
        ];

        yield 'Invalid name pattern: no slash' => [
            'data' => (object)[
                'name' => 'my-file-type',
                'typeName' => 'image',
            ],
            'valid' => false,
        ];

        yield 'Invalid name pattern: uppercase' => [
            'data' => (object)[
                'name' => 'Vendor/My-File-Type',
                'typeName' => 'image',
            ],
            'valid' => false,
        ];

        yield 'Valid prefixFields and prefixType' => [
            'data' => (object)[
                'name' => 'vendor/my-file-type',
                'typeName' => 'image',
                'prefixFields' => false,
                'prefixType' => 'vendor',
            ],
            'valid' => true,
        ];

        yield 'Invalid prefixType' => [
            'data' => (object)[
                'name' => 'vendor/my-file-type',
                'typeName' => 'image',
                'prefixType' => 'invalid',
            ],
            'valid' => false,
        ];

        yield 'Valid vendorPrefix' => [
            'data' => (object)[
                'name' => 'vendor/my-file-type',
                'typeName' => 'image',
                'vendorPrefix' => 'my-vendor',
            ],
            'valid' => true,
        ];

        yield 'Invalid vendorPrefix: empty' => [
            'data' => (object)[
                'name' => 'vendor/my-file-type',
                'typeName' => 'image',
                'vendorPrefix' => '',
            ],
            'valid' => false,
        ];

        yield 'Valid basics' => [
            'data' => (object)[
                'name' => 'vendor/my-file-type',
                'typeName' => 'image',
                'basics' => ['common', 'image'],
            ],
            'valid' => true,
        ];

        yield 'Invalid basics: not unique' => [
            'data' => (object)[
                'name' => 'vendor/my-file-type',
                'typeName' => 'image',
                'basics' => ['common', 'common'],
            ],
            'valid' => false,
        ];

        yield 'Valid fields' => [
            'data' => (object)[
                'name' => 'vendor/my-file-type',
                'typeName' => 'image',
                'fields' => [
                    (object)[
                        'identifier' => 'my_field',
                        'type' => 'Text',
                    ],
                ],
            ],
            'valid' => true,
        ];

        yield 'Additional properties not allowed' => [
            'data' => (object)[
                'name' => 'vendor/my-file-type',
                'typeName' => 'image',
                'unknown' => 'prop',
            ],
            'valid' => false,
        ];
    }

    #[Test]
    #[DataProvider('fileTypeSchemaValidationWorksAsExpectedDataProvider')]
    public function fileTypeSchemaValidationWorksAsExpected(object $data, bool $valid): void
    {
        $jsonSchemaValidator = new JsonSchemaValidator();

        $validationResult = $jsonSchemaValidator->validateContentElement($data, 'http://typo3.org/file-type.json');

        self::assertSame($valid, $validationResult);
    }
}
