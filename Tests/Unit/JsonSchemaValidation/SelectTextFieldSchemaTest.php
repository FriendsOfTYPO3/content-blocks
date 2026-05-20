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

final class SelectTextFieldSchemaTest extends UnitTestCase
{
    public static function SelectTextFieldSchemaValidationWorksAsExpectedDataProvider(): iterable
    {
        yield 'Only type' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'select_text',
                        'type' => 'SelectText',
                        'items' => [
                            (object)['value' => 'Option 1'],
                        ],
                    ],
                ],
            ],
            'valid' => true,
        ];

        yield 'Missing items' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'select_text',
                        'type' => 'SelectText',
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'Valid properties' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'select_text',
                        'alias' => 'SelectText',
                        'type' => 'SelectText',
                        'authMode' => 'explicitAllow',
                        'behaviour' => (object)[
                            'allowLanguageSynchronization' => true,
                        ],
                        'default' => 'Option 1',
                        'disableNoMatchingValueElement' => true,
                        'fieldControl' => (object)['foo' => 'bar'],
                        'fieldInformation' => (object)['foo' => 'bar'],
                        'fieldWizard' => (object)[
                            'defaultLanguageDifferences' => (object)['foo' => 'bar'],
                            'localizationStateSelector' => (object)['foo' => 'bar'],
                            'otherLanguageContent' => (object)['foo' => 'bar'],
                        ],
                        'itemGroups' => (object)[
                            'group1' => 'Group 1',
                        ],
                        'items' => [
                            (object)[
                                'label' => 'Item 1',
                                'value' => 'Option 1',
                                'icon' => 'icon-name',
                                'group' => 'group1',
                            ],
                            (object)[
                                'label' => 'Item 2',
                                'value' => 'Option 2',
                            ],
                        ],
                        'readOnly' => false,
                        'size' => 1,
                        'sortItems' => (object)[
                            'label' => 'asc',
                            'value' => 'desc',
                        ],
                    ],
                ],
            ],
            'valid' => true,
        ];

        yield 'value not a string' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'select_text',
                        'type' => 'SelectText',
                        'items' => [
                            (object)['value' => 1],
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
                        'identifier' => 'select_text',
                        'type' => 'SelectText',
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
                        'identifier' => 'select_text',
                        'type' => 'SelectText',
                        'size' => 0,
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'default not a string' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'select_text',
                        'type' => 'SelectText',
                        'default' => 1,
                    ],
                ],
            ],
            'valid' => false,
        ];
    }

    #[Test]
    #[DataProvider('SelectTextFieldSchemaValidationWorksAsExpectedDataProvider')]
    public function SelectTextFieldSchemaValidationWorksAsExpected(object $data, bool $valid): void
    {
        $jsonSchemaValidator = new JsonSchemaValidator();

        $validationResult = $jsonSchemaValidator->isValidContentElement($data);

        self::assertSame($valid, $validationResult);
    }
}
