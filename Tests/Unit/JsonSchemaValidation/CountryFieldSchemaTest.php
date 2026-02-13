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

final class CountryFieldSchemaTest extends UnitTestCase
{
    public static function countryFieldSchemaValidationWorksAsExpectedDataProvider(): iterable
    {
        yield 'Only type' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'country',
                        'type' => 'Country',
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
                        'identifier' => 'country',
                        'type' => 'Country',
                        'behaviour' => (object)[
                            'allowLanguageSynchronization' => true,
                        ],
                        'default' => 'DE',
                        'filter' => (object)[
                            'onlyCountries' => ['DE', 'AT', 'CH'],
                            'excludeCountries' => ['FR'],
                        ],
                        'labelField' => 'iso2',
                        'prioritizedCountries' => ['DE'],
                        'fieldControl' => (object)['foo' => 'bar'],
                        'fieldInformation' => (object)['foo' => 'bar'],
                        'fieldWizard' => (object)[
                            'defaultLanguageDifferences' => (object)['disabled' => false],
                            'localizationStateSelector' => (object)['disabled' => false],
                            'otherLanguageContent' => (object)['disabled' => false],
                        ],
                        'readOnly' => false,
                        'required' => false,
                        'size' => 1,
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
                        'identifier' => 'country',
                        'type' => 'Country',
                        'unknown' => 'unknown',
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'invalid labelField' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'country',
                        'type' => 'Country',
                        'labelField' => 'invalid',
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
                        'identifier' => 'country',
                        'type' => 'Country',
                        'size' => 0,
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'filter.onlyCountries not an array' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'country',
                        'type' => 'Country',
                        'filter' => (object)[
                            'onlyCountries' => 'DE',
                        ],
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'prioritizedCountries items not a string' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'country',
                        'type' => 'Country',
                        'prioritizedCountries' => [1],
                    ],
                ],
            ],
            'valid' => false,
        ];
    }

    #[Test]
    #[DataProvider('countryFieldSchemaValidationWorksAsExpectedDataProvider')]
    public function countryFieldSchemaValidationWorksAsExpected(object $data, bool $valid): void
    {
        $jsonSchemaValidator = new JsonSchemaValidator();

        $validationResult = $jsonSchemaValidator->validateContentElement($data, 'http://typo3.org/content-element.json');

        self::assertSame($valid, $validationResult);
    }
}
