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

final class DateTimeFieldSchemaTest extends UnitTestCase
{
    public static function dateTimeFieldSchemaValidationWorksAsExpectedDataProvider(): iterable
    {
        yield 'Only type' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'datetime',
                        'type' => 'DateTime',
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
                        'identifier' => 'datetime',
                        'type' => 'DateTime',
                        'behaviour' => (object)[
                            'allowLanguageSynchronization' => true,
                        ],
                        'dbType' => 'datetime',
                        'default' => '2023-01-01 12:00:00',
                        'fieldControl' => (object)['foo' => 'bar'],
                        'fieldInformation' => (object)['foo' => 'bar'],
                        'fieldWizard' => (object)[
                            'defaultLanguageDifferences' => (object)[],
                            'localizationStateSelector' => (object)[],
                            'otherLanguageContent' => (object)[],
                        ],
                        'format' => 'datetime',
                        'mode' => 'useOrOverridePlaceholder',
                        'nullable' => true,
                        'placeholder' => 'placeholder',
                        'range' => (object)[
                            'lower' => '2023-01-01',
                            'upper' => 1672574400,
                        ],
                        'readOnly' => true,
                        'required' => true,
                        'searchable' => false,
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
                        'identifier' => 'datetime',
                        'type' => 'DateTime',
                        'unknown' => 'unknown',
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'invalid dbType' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'datetime',
                        'type' => 'DateTime',
                        'dbType' => 'invalid',
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
                        'identifier' => 'datetime',
                        'type' => 'DateTime',
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
                        'identifier' => 'datetime',
                        'type' => 'DateTime',
                        'mode' => 'invalid',
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'range lower not string or integer' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'datetime',
                        'type' => 'DateTime',
                        'range' => (object)[
                            'lower' => true,
                        ],
                    ],
                ],
            ],
            'valid' => false,
        ];
    }

    #[Test]
    #[DataProvider('dateTimeFieldSchemaValidationWorksAsExpectedDataProvider')]
    public function dateTimeFieldSchemaValidationWorksAsExpected(object $data, bool $valid): void
    {
        $jsonSchemaValidator = new JsonSchemaValidator();

        $validationResult = $jsonSchemaValidator->validateContentElement($data, 'http://typo3.org/content-element.json');

        self::assertSame($valid, $validationResult);
    }
}
