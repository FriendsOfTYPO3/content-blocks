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

final class PageTypeSchemaTest extends UnitTestCase
{
    public static function pageTypeSchemaValidationWorksAsExpectedDataProvider(): iterable
    {
        yield 'Basic valid page type' => [
            'data' => (object)[
                'name' => 'json/page-test',
                'typeName' => 123,
                'group' => 'default',
            ],
            'valid' => true,
        ];

        yield 'Valid group enum: links' => [
            'data' => (object)[
                'name' => 'json/page-test',
                'typeName' => 123,
                'group' => 'links',
            ],
            'valid' => true,
        ];

        yield 'Valid group enum: special' => [
            'data' => (object)[
                'name' => 'json/page-test',
                'typeName' => 123,
                'group' => 'special',
            ],
            'valid' => true,
        ];

        yield 'Valid custom group string' => [
            'data' => (object)[
                'name' => 'json/page-test',
                'typeName' => 123,
                'group' => 'custom-group',
            ],
            'valid' => true,
        ];

        yield 'Valid integer typeName' => [
            'data' => (object)[
                'name' => 'json/page-test',
                'typeName' => 9999,
            ],
            'valid' => true,
        ];

        yield 'Invalid typeName: string' => [
            'data' => (object)[
                'name' => 'json/page-test',
                'typeName' => '123',
            ],
            'valid' => false,
        ];

        yield 'Additional properties not allowed' => [
            'data' => (object)[
                'name' => 'json/page-test',
                'typeName' => 123,
                'unknown' => 'prop',
            ],
            'valid' => false,
        ];

        yield 'Inherited properties from content-type: fields' => [
            'data' => (object)[
                'name' => 'json/page-test',
                'typeName' => 123,
                'fields' => [
                    (object)[
                        'identifier' => 'title',
                        'type' => 'Text',
                    ],
                ],
            ],
            'valid' => true,
        ];
    }

    #[Test]
    #[DataProvider('pageTypeSchemaValidationWorksAsExpectedDataProvider')]
    public function pageTypeSchemaValidationWorksAsExpected(object $data, bool $valid): void
    {
        $jsonSchemaValidator = new JsonSchemaValidator();

        $validationResult = $jsonSchemaValidator->isValidPageType($data);

        self::assertSame($valid, $validationResult);
    }
}
