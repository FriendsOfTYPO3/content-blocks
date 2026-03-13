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

final class NumberFieldSchemaTest extends UnitTestCase
{
    public static function numberFieldSchemaValidationWorksAsExpectedDataProvider(): iterable
    {
        yield 'Only type' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'number',
                        'type' => 'Number',
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
                        'identifier' => 'number',
                        'type' => 'Number',
                        'behaviour' => (object)[
                            'allowLanguageSynchronization' => true,
                        ],
                        'autocomplete' => true,
                        'default' => 10,
                        'fieldControl' => (object)['foo' => 'bar'],
                        'fieldInformation' => (object)['foo' => 'bar'],
                        'fieldWizard' => (object)[
                            'defaultLanguageDifferences' => (object)[
                                'disabled' => false,
                            ],
                            'localizationStateSelector' => (object)[
                                'disabled' => false,
                            ],
                            'otherLanguageContent' => (object)[
                                'disabled' => false,
                            ],
                        ],
                        'format' => 'decimal',
                        'mode' => 'useOrOverridePlaceholder',
                        'nullable' => true,
                        'placeholder' => 'placeholder',
                        'range' => (object)[
                            'lower' => -10,
                            'upper' => 100,
                        ],
                        'readOnly' => false,
                        'required' => false,
                        'size' => 30,
                        'slider' => (object)[
                            'step' => 0.5,
                            'width' => 500,
                        ],
                        'valuePicker' => (object)[
                            'items' => [
                                [
                                    'Label',
                                    'Value',
                                ],
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
                        'identifier' => 'number',
                        'type' => 'Number',
                        'unknown' => 'unknown',
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'size too small' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'number',
                        'type' => 'Number',
                        'size' => 9,
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'size too large' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'number',
                        'type' => 'Number',
                        'size' => 51,
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'invalid format' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'number',
                        'type' => 'Number',
                        'format' => 'invalid',
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'invalid mode' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'number',
                        'type' => 'Number',
                        'mode' => 'invalid',
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'range lower not aa number' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'number',
                        'type' => 'Number',
                        'range' => (object)[
                            'lower' => '10.5',
                        ],
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'slider step not a number' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'number',
                        'type' => 'Number',
                        'slider' => (object)[
                            'step' => 'one',
                        ],
                    ],
                ],
            ],
            'valid' => false,
        ];
    }

    #[Test]
    #[DataProvider('numberFieldSchemaValidationWorksAsExpectedDataProvider')]
    public function numberFieldSchemaValidationWorksAsExpected(object $data, bool $valid): void
    {
        $jsonSchemaValidator = new JsonSchemaValidator();

        $validationResult = $jsonSchemaValidator->isValidContentElement($data);

        self::assertSame($valid, $validationResult);
    }
}
