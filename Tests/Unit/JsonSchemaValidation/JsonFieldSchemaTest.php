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

final class JsonFieldSchemaTest extends UnitTestCase
{
    public static function jsonFieldSchemaValidationWorksAsExpectedDataProvider(): iterable
    {
        yield 'Only type' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'json_field',
                        'type' => 'Json',
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
                        'identifier' => 'json_field',
                        'type' => 'Json',
                        'behaviour' => (object)[
                            'allowLanguageSynchronization' => true,
                        ],
                        'cols' => 40,
                        'default' => '{}',
                        'enableCodeEditor' => false,
                        'fieldControl' => (object)['foo' => 'bar'],
                        'fieldInformation' => (object)['foo' => 'bar'],
                        'fieldWizard' => (object)[
                            'defaultLanguageDifferences' => (object)[],
                            'localizationStateSelector' => (object)[],
                            'otherLanguageContent' => (object)[],
                        ],
                        'placeholder' => 'placeholder',
                        'readOnly' => true,
                        'required' => true,
                        'rows' => 10,
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
                        'identifier' => 'json_field',
                        'type' => 'Json',
                        'unknown' => 'unknown',
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'cols too small' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'json_field',
                        'type' => 'Json',
                        'cols' => 9,
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'cols too large' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'json_field',
                        'type' => 'Json',
                        'cols' => 51,
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'rows too small' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'json_field',
                        'type' => 'Json',
                        'rows' => 0,
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'rows too large' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'json_field',
                        'type' => 'Json',
                        'rows' => 21,
                    ],
                ],
            ],
            'valid' => false,
        ];
    }

    #[Test]
    #[DataProvider('jsonFieldSchemaValidationWorksAsExpectedDataProvider')]
    public function jsonFieldSchemaValidationWorksAsExpected(object $data, bool $valid): void
    {
        $jsonSchemaValidator = new JsonSchemaValidator();

        $validationResult = $jsonSchemaValidator->isValidContentElement($data);

        self::assertSame($valid, $validationResult);
    }
}
