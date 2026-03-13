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

final class FlexFormFieldSchemaTest extends UnitTestCase
{
    public static function flexFormFieldSchemaValidationWorksAsExpectedDataProvider(): iterable
    {
        yield 'Only type' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'settings',
                        'type' => 'FlexForm',
                    ],
                ],
            ],
            'valid' => true,
        ];

        yield 'Valid properties' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'settings',
                        'type' => 'FlexForm',
                        'searchable' => true,
                        'fields' => [
                            (object)[
                                'identifier' => 'alignment',
                                'type' => 'Select',
                                'renderType' => 'selectSingle',
                                'items' => [
                                    (object)[
                                        'label' => 'Left',
                                        'value' => 'left',
                                    ],
                                ],
                            ],
                            (object)[
                                'identifier' => 'title',
                                'type' => 'Text',
                                'label' => 'Title',
                                'description' => 'A description',
                            ],
                        ],
                    ],
                ],
            ],
            'valid' => true,
        ];

        yield 'Cannot mix Sheets and non-Sheets' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'settings',
                        'type' => 'FlexForm',
                        'searchable' => true,
                        'fields' => [
                            (object)[
                                'identifier' => 'alignment',
                                'type' => 'Select',
                                'renderType' => 'selectSingle',
                                'items' => [
                                    (object)[
                                        'label' => 'Left',
                                        'value' => 'left',
                                    ],
                                ],
                            ],
                            (object)[
                                'identifier' => 'title',
                                'type' => 'Sheet',
                                'label' => 'Title',
                                'description' => 'A description',
                                'fields' => [
                                    (object)[
                                        'identifier' => 'field1',
                                        'type' => 'Text',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'unknown property' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'settings',
                        'type' => 'FlexForm',
                        'unknown' => 'unknown',
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'Valid sheets' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'settings',
                        'type' => 'FlexForm',
                        'fields' => [
                            (object)[
                                'identifier' => 'sheet1',
                                'type' => 'Sheet',
                                'label' => 'Sheet 1',
                                'description' => 'Description 1',
                                'linkTitle' => 'Link Title 1',
                                'fields' => [
                                    (object)[
                                        'identifier' => 'field1',
                                        'type' => 'Text',
                                    ],
                                ],
                            ],
                            (object)[
                                'identifier' => 'sheet2',
                                'type' => 'Sheet',
                                'fields' => [
                                    (object)[
                                        'identifier' => 'field2',
                                        'type' => 'Select',
                                        'renderType' => 'selectSingle',
                                        'items' => [
                                            (object)['label' => 'V', 'value' => 'v'],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'valid' => true,
        ];

        yield 'Invalid sheet property' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'settings',
                        'type' => 'FlexForm',
                        'fields' => [
                            (object)[
                                'identifier' => 'sheet1',
                                'type' => 'Sheet',
                                'unknown' => 'property',
                                'fields' => [],
                            ],
                        ],
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'Sheet cannot be inside Sheet' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'settings',
                        'type' => 'FlexForm',
                        'fields' => [
                            (object)[
                                'identifier' => 'sheet1',
                                'type' => 'Sheet',
                                'fields' => [
                                    (object)[
                                        'identifier' => 'nested_sheet',
                                        'type' => 'Sheet',
                                        'fields' => [],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'valid' => false,
        ];
    }

    #[Test]
    #[DataProvider('flexFormFieldSchemaValidationWorksAsExpectedDataProvider')]
    public function flexFormFieldSchemaValidationWorksAsExpected(object $data, bool $valid): void
    {
        $jsonSchemaValidator = new JsonSchemaValidator();

        $validationResult = $jsonSchemaValidator->isValidContentElement($data);

        self::assertSame($valid, $validationResult);
    }
}
