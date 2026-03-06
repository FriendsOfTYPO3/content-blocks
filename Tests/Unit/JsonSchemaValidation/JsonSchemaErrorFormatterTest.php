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
use TYPO3\CMS\ContentBlocks\JsonSchemaValidation\JsonSchemaErrorFormatter;
use TYPO3\CMS\ContentBlocks\JsonSchemaValidation\JsonSchemaValidator;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class JsonSchemaErrorFormatterTest extends UnitTestCase
{
    public static function errorIsFormattedCorrectlyDataProvider(): iterable
    {
        yield 'Invalid Content Element typeName' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'title' => 'My Element',
                'description' => 'My Element Description',
                'group' => 'my_group',
                'typeName' => 123,
                'prefixFields' => true,
                'prefixType' => 'invalid',
                'saveAndClose' => 'false',
                'fields' => [
                    (object)[
                        'identifier' => 'bodytext',
                        'type' => 'Textarea',
                        'prefixType' => 'wrong',
                    ],
                ],
            ],
            'expected' => [
                '/typeName' => [
                    'The data (integer) must match the type: string',
                ],
                '/saveAndClose' => [
                    'The data (string) must match the type: boolean',
                ],
            ],
        ];
    }

    #[DataProvider('errorIsFormattedCorrectlyDataProvider')]
    #[Test]
    public function errorIsFormattedCorrectly(object $data, array $expected): void
    {
        $errorFormatter = new JsonSchemaErrorFormatter();
        $validator = new JsonSchemaValidator();

        $validationResult = $validator->validateContentElement($data);
        $formattedError = $errorFormatter->format($validationResult);

        self::assertSame($expected, $formattedError);
    }
}
