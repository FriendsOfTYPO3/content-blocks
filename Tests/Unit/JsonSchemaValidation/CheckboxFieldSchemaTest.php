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

final class CheckboxFieldSchemaTest extends UnitTestCase
{
    public static function checkboxFieldSchemaValidationWorksAsExpectedDataProvider(): iterable
    {
        yield 'Only type' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'checkbox',
                        'type' => 'Checkbox',
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
                        'identifier' => 'checkbox',
                        'type' => 'Checkbox',
                        'behaviour' => (object)[
                            'allowLanguageSynchronization' => true,
                        ],
                        'cols' => 3,
                        'default' => 1,
                        'eval' => 'maximumRecordsChecked',
                        'fieldControl' => (object)['foo' => 'bar'],
                        'fieldInformation' => (object)['foo' => 'bar'],
                        'fieldWizard' => (object)[
                            'defaultLanguageDifferences' => (object)['foo' => 'bar'],
                            'localizationStateSelector' => (object)['foo' => 'bar'],
                            'otherLanguageContent' => (object)['foo' => 'bar'],
                        ],
                        'items' => [
                            (object)[
                                'label' => 'Item 1',
                                'invertStateDisplay' => true,
                                'iconIdentifierChecked' => 'check-icon',
                                'iconIdentifierUnchecked' => 'uncheck-icon',
                                'labelChecked' => 'Checked Label',
                                'labelUnchecked' => 'Unchecked Label',
                            ],
                        ],
                        'itemsProcFunc' => 'My\\Class->myMethod',
                        'itemsProcConfig' => (object)['foo' => 'bar'],
                        'readOnly' => true,
                        'renderType' => 'checkboxToggle',
                        'validation' => (object)[
                            'maximumRecordsChecked' => 5,
                            'maximumRecordsCheckedInPid' => 10,
                        ],
                    ],
                ],
            ],
            'valid' => true,
        ];

        yield 'cols as string inline' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'checkbox',
                        'type' => 'Checkbox',
                        'cols' => 'inline',
                    ],
                ],
            ],
            'valid' => true,
        ];

        yield 'renderType checkboxLabeledToggle' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'checkbox',
                        'type' => 'Checkbox',
                        'renderType' => 'checkboxLabeledToggle',
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
                        'identifier' => 'checkbox',
                        'type' => 'Checkbox',
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
                        'identifier' => 'checkbox',
                        'type' => 'Checkbox',
                        'cols' => 0,
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
                        'identifier' => 'checkbox',
                        'type' => 'Checkbox',
                        'cols' => 32,
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'invalid eval' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'checkbox',
                        'type' => 'Checkbox',
                        'eval' => 'invalid',
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'invalid items property' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'checkbox',
                        'type' => 'Checkbox',
                        'items' => [
                            (object)[
                                'unknown' => 'unknown',
                            ],
                        ],
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'invalid renderType' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'checkbox',
                        'type' => 'Checkbox',
                        'renderType' => 'invalid',
                    ],
                ],
            ],
            'valid' => false,
        ];
    }

    #[Test]
    #[DataProvider('checkboxFieldSchemaValidationWorksAsExpectedDataProvider')]
    public function checkboxFieldSchemaValidationWorksAsExpected(object $data, bool $valid): void
    {
        $jsonSchemaValidator = new JsonSchemaValidator();

        $validationResult = $jsonSchemaValidator->isValidContentElement($data);

        self::assertSame($valid, $validationResult);
    }
}
