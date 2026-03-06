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

final class TextareaFieldSchemaTest extends UnitTestCase
{
    public static function textareaFieldSchemaValidationWorksAsExpectedDataProvider(): iterable
    {
        yield 'Only type' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'bodytext',
                        'type' => 'Textarea',
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
                        'identifier' => 'bodytext',
                        'type' => 'Textarea',
                        'behaviour' => (object)[
                            'allowLanguageSynchronization' => true,
                        ],
                        'cols' => 20,
                        'default' => 'some text',
                        'enableRichtext' => false,
                        'enableTabulator' => false,
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
                        'fixedFont' => false,
                        'is_in' => 'foo',
                        'max' => 100,
                        'min' => 100,
                        'nullable' => true,
                        'placeholder' => 'placeholder',
                        'readOnly' => false,
                        'renderType' => 'codeEditor',
                        'required' => false,
                        'rows' => 10,
                        'searchable' => false,
                        'valuePicker' => (object)[
                            'items' => [
                                (object)[
                                    'label' => 'Label',
                                    'value' => 'Value',
                                ],
                            ],
                        ],
                        'wrap' => 'virtual',
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
                        'identifier' => 'bodytext',
                        'type' => 'Textarea',
                        'unknown' => 'unknown',
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'cols not in range' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'bodytext',
                        'type' => 'Textarea',
                        'cols' => 999,
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
                        'identifier' => 'bodytext',
                        'type' => 'Textarea',
                        'eval' => ['trim'],
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'unknown renderType' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'bodytext',
                        'type' => 'Textarea',
                        'renderType' => 'unknown',
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'unknown wrap' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'bodytext',
                        'type' => 'Textarea',
                        'wrap' => 'unknown',
                    ],
                ],
            ],
            'valid' => false,
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
