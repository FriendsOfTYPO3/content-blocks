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

final class FieldTypeSchemaTest extends UnitTestCase
{
    public static function textareaFieldSchemaValidationWorksAsExpectedDataProvider(): iterable
    {
        yield 'Valid field type' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'bodytext',
                        'type' => 'Textarea',
                        'label' => 'Text',
                        'description' => 'My Description',
                        'useExistingField' => false,
                        'prefixField' => false,
                        'prefixType' => 'vendor',
                        'displayCond' => 'FIELD:header:=:Headline',
                        'onChange' => 'reload',
                        'exclude' => false,
                    ],
                ],
            ],
            'valid' => true,
        ];

        yield 'Display Cond array' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'bodytext',
                        'type' => 'Textarea',
                        'label' => 'Text',
                        'description' => 'My Description',
                        'useExistingField' => false,
                        'prefixField' => false,
                        'prefixType' => 'vendor',
                        'displayCond' => (object)[
                            'OR' => [
                                'FIELD:header:=:Headline',
                                'FIELD:header:=:Subheadline',
                            ],
                        ],
                        'onChange' => 'reload',
                    ],
                ],
            ],
            'valid' => true,
        ];

        yield 'Wrong value for prefixType' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'bodytext',
                        'type' => 'Textarea',
                        'prefixType' => 'wrong',
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'Wrong value for onChange' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'bodytext',
                        'type' => 'Textarea',
                        'onChange' => 'wrong',
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'Custom Field Type' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'map',
                        'type' => 'Map',
                    ],
                ],
            ],
            'valid' => true,
        ];

        yield 'type is required' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'foo',
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'type is not required with useExistingField' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'foo',
                        'useExistingField' => true,
                    ],
                ],
            ],
            'valid' => true,
        ];
    }

    #[Test]
    #[DataProvider('textareaFieldSchemaValidationWorksAsExpectedDataProvider')]
    public function textareaFieldSchemaValidationWorksAsExpected(object $data, bool $valid): void
    {
        $jsonSchemaValidator = new JsonSchemaValidator();

        $validationResult = $jsonSchemaValidator->isValidContentElement($data);

        self::assertSame($valid, $validationResult);
    }
}
