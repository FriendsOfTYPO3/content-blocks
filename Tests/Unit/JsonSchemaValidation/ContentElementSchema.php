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

final class ContentElementSchema extends UnitTestCase
{
    public static function contentTypeSchemaIsValidDataProvider(): iterable
    {
        yield 'empty definition' => [
            'data' => (object)[],
            'valid' => false,
        ];

        yield 'only name' => [
            'data' => (object)[
                'name' => 'json/schema-test',
            ],
            'valid' => true,
        ];

        yield 'invalid name empty' => [
            'data' => (object)[
                'name' => '',
            ],
            'valid' => false,
        ];

        yield 'invalid name one word' => [
            'data' => (object)[
                'name' => 'json',
            ],
            'valid' => false,
        ];

        yield 'invalid name empty second part' => [
            'data' => (object)[
                'name' => 'json/',
            ],
            'valid' => false,
        ];

        yield 'invalid name only slash' => [
            'data' => (object)[
                'name' => '/',
            ],
            'valid' => false,
        ];

        yield 'invalid name missing vendor' => [
            'data' => (object)[
                'name' => '/schema',
            ],
            'valid' => false,
        ];

        yield 'invalid name uppercase not allowed' => [
            'data' => (object)[
                'name' => 'JSON/schema',
            ],
            'valid' => false,
        ];

        yield 'minimal name' => [
            'data' => (object)[
                'name' => 'a/b',
            ],
            'valid' => true,
        ];

        yield 'invalid properties' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'burger' => 'royale',
            ],
            'valid' => false,
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
            'valid' => true,
        ];

        yield 'Invalid prefixFields type' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'prefixFields' => 'false',
            ],
            'valid' => false,
        ];

        yield 'Invalid prefixType' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'prefixType' => 'unknown',
            ],
            'valid' => false,
        ];

        yield 'empty vendorPrefix is invalid' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'vendorPrefix' => '',
            ],
            'valid' => false,
        ];

        yield 'Negative priority is invalid' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'priority' => -1,
            ],
            'valid' => false,
        ];

        yield 'basics not an array' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'basics' => 'Basic 1',
            ],
            'valid' => false,
        ];
    }

    #[Test]
    #[DataProvider('contentTypeSchemaIsValidDataProvider')]
    public function contentTypeSchemaValidationWorksAsExpected(object $data, bool $valid): void
    {
        $jsonSchemaValidator = new JsonSchemaValidator();

        $validationResult = $jsonSchemaValidator->validate($data, 'http://typo3.org/content-element.json');

        self::assertSame($valid, $validationResult);
    }

    public static function contentElementSchemaIsValidDataProvider(): iterable
    {
        yield 'all fields' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'title' => 'My Element',
                'description' => 'My Element Description',
                'group' => 'my_group',
                'typeName' => 'my_type',
                'saveAndClose' => false,
            ],
            'valid' => true,
        ];

        yield 'Invalid Content Element typeName' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'title' => 'My Element',
                'description' => 'My Element Description',
                'group' => 'my_group',
                'typeName' => 123,
                'saveAndClose' => false,
            ],
            'valid' => false,
        ];
    }

    #[Test]
    #[DataProvider('contentElementSchemaIsValidDataProvider')]
    public function contentElementSchemaValidationWorksAsExpected(object $data, bool $valid): void
    {
        $jsonSchemaValidator = new JsonSchemaValidator();

        $validationResult = $jsonSchemaValidator->validate($data, 'http://typo3.org/content-element.json');

        self::assertSame($valid, $validationResult);
    }
}
