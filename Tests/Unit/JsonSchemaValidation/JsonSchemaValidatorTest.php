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

final class JsonSchemaValidatorTest extends UnitTestCase
{
    public static function contentTypeSchemaIsValidDataProvider(): iterable
    {
        yield 'empty definition' => [
            'data' => (object)[],
            'expectedHasError' => true,
        ];

        yield 'only name' => [
            'data' => (object)[
                'name' => 'json/schema-test',
            ],
            'expectedHasError' => false,
        ];

        yield 'invalid name empty' => [
            'data' => (object)[
                'name' => '',
            ],
            'expectedHasError' => true,
        ];

        yield 'invalid name one word' => [
            'data' => (object)[
                'name' => 'json',
            ],
            'expectedHasError' => true,
        ];

        yield 'invalid name empty second part' => [
            'data' => (object)[
                'name' => 'json/',
            ],
            'expectedHasError' => true,
        ];

        yield 'invalid name only slash' => [
            'data' => (object)[
                'name' => '/',
            ],
            'expectedHasError' => true,
        ];

        yield 'invalid name missing vendor' => [
            'data' => (object)[
                'name' => '/schema',
            ],
            'expectedHasError' => true,
        ];

        yield 'invalid name uppercase not allowed' => [
            'data' => (object)[
                'name' => 'JSON/schema',
            ],
            'expectedHasError' => true,
        ];

        yield 'minimal name' => [
            'data' => (object)[
                'name' => 'a/b',
            ],
            'expectedHasError' => false,
        ];

        yield 'invalid properties' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'burger' => 'royale',
            ],
            'expectedHasError' => true,
        ];

        yield 'All root properties' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'title' => 'Schema',
                'prefixFields' => false,
                'prefixType' => 'full',
                'vendorPrefix' => 'json',
                'priority' => 0,
                'basics' => [
                    'Basic1',
                    'Basic2',
                ],
                'fields' => [],
            ],
            'expectedHasError' => false,
        ];

        yield 'Invalid prefixFields type' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'prefixFields' => 'false',
            ],
            'expectedHasError' => true,
        ];

        yield 'Invalid prefixType' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'prefixType' => 'unknown',
            ],
            'expectedHasError' => true,
        ];

        yield 'empty vendorPrefix is invalid' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'vendorPrefix' => '',
            ],
            'expectedHasError' => true,
        ];

        yield 'Negative priority is invalid' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'priority' => -1,
            ],
            'expectedHasError' => true,
        ];

        yield 'basics not an array' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'basics' => 'Basic 1',
            ],
            'expectedHasError' => true,
        ];
    }

    #[Test]
    #[DataProvider('contentTypeSchemaIsValidDataProvider')]
    public function contentTypeSchemaValidationWorksAsExpected(object $data, bool $expectedHasError): void
    {
        $jsonSchemaValidator = new JsonSchemaValidator();

        $hasError = $jsonSchemaValidator->validate($data, 'http://typo3.org/content-element.json');

        self::assertSame($expectedHasError, $hasError);
    }
}
