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

final class EmailFieldSchemaTest extends UnitTestCase
{
    public static function emailFieldSchemaValidationWorksAsExpectedDataProvider(): iterable
    {
        yield 'Only type' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'email',
                        'type' => 'Email',
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
                        'identifier' => 'email',
                        'type' => 'Email',
                        'behaviour' => (object)[
                            'allowLanguageSynchronization' => true,
                        ],
                        'autocomplete' => true,
                        'default' => 'test@example.com',
                        'eval' => 'trim',
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
                        'mode' => 'useOrOverridePlaceholder',
                        'nullable' => true,
                        'placeholder' => 'placeholder',
                        'readOnly' => false,
                        'required' => false,
                        'searchable' => false,
                        'size' => 30,
                        'valuePicker' => (object)[
                            'items' => [
                                (object)[
                                    'label' => 'Label',
                                    'value' => 'Value',
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
                        'identifier' => 'email',
                        'type' => 'Email',
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
                        'identifier' => 'email',
                        'type' => 'Email',
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
                        'identifier' => 'email',
                        'type' => 'Email',
                        'size' => 51,
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'eval not a string' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'email',
                        'type' => 'Email',
                        'eval' => ['trim'],
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
                        'identifier' => 'email',
                        'type' => 'Email',
                        'mode' => 'invalid',
                    ],
                ],
            ],
            'valid' => false,
        ];
    }

    #[Test]
    #[DataProvider('emailFieldSchemaValidationWorksAsExpectedDataProvider')]
    public function emailFieldSchemaValidationWorksAsExpected(object $data, bool $valid): void
    {
        $jsonSchemaValidator = new JsonSchemaValidator();

        $validationResult = $jsonSchemaValidator->isValidContentElement($data);

        self::assertSame($valid, $validationResult);
    }
}
