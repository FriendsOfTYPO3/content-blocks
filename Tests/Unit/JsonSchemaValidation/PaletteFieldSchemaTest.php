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

final class PaletteFieldSchemaTest extends UnitTestCase
{
    public static function paletteFieldSchemaValidationWorksAsExpectedDataProvider(): iterable
    {
        yield 'Only type' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'palette',
                        'type' => 'Palette',
                    ],
                ],
            ],
            'valid' => true,
        ];

        yield 'Valid properties with fields' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'palette',
                        'label' => 'Palette Label',
                        'description' => 'Palette Description',
                        'type' => 'Palette',
                        'fields' => [
                            (object)[
                                'identifier' => 'text_field',
                                'type' => 'Text',
                                'label' => 'Text Field',
                            ],
                            (object)[
                                'identifier' => 'checkbox_field',
                                'type' => 'Checkbox',
                                'default' => 1,
                            ],
                        ],
                    ],
                ],
            ],
            'valid' => true,
        ];

        yield 'unknown property' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'palette',
                        'type' => 'Palette',
                        'unknown' => 'unknown',
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'invalid field in palette' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'palette',
                        'type' => 'Palette',
                        'fields' => [
                            (object)[
                                'identifier' => 'invalid_field',
                                'type' => 'Text',
                                'unknown_prop' => 'invalid',
                            ],
                        ],
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'invalid identifier in palette field' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'palette',
                        'type' => 'Palette',
                        'fields' => [
                            (object)[
                                'identifier' => 'invalid-identifier',
                                'type' => 'Text',
                            ],
                        ],
                    ],
                ],
            ],
            'valid' => false,
        ];
    }

    #[Test]
    #[DataProvider('paletteFieldSchemaValidationWorksAsExpectedDataProvider')]
    public function paletteFieldSchemaValidationWorksAsExpected(object $data, bool $valid): void
    {
        $jsonSchemaValidator = new JsonSchemaValidator();

        $validationResult = $jsonSchemaValidator->isValidContentElement($data);

        self::assertSame($valid, $validationResult);
    }
}
