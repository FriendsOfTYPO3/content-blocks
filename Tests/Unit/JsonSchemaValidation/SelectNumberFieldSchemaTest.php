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

final class SelectNumberFieldSchemaTest extends UnitTestCase
{
    public static function selectNumberFieldSchemaValidationWorksAsExpectedDataProvider(): iterable
    {
        yield 'Only type' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'select_number',
                        'type' => 'SelectNumber',
                        'items' => [
                            (object)['value' => 1],
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
                        'identifier' => 'select_number',
                        'type' => 'SelectNumber',
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
                        'identifier' => 'select_number',
                        'type' => 'SelectNumber',
                        'authMode' => 'explicitAllow',
                        'behaviour' => (object)[
                            'allowLanguageSynchronization' => true,
                        ],
                        'default' => 1,
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
                                'value' => 1,
                                'icon' => 'icon-name',
                                'group' => 'group1',
                            ],
                            (object)[
                                'label' => 'Item 2',
                                'value' => 2,
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

        yield 'value not an integer' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'select_number',
                        'type' => 'SelectNumber',
                        'items' => [
                            (object)['value' => 'string'],
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
                        'identifier' => 'select_number',
                        'type' => 'SelectNumber',
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
                        'identifier' => 'select_number',
                        'type' => 'SelectNumber',
                        'size' => 0,
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'default not an integer' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'select_number',
                        'type' => 'SelectNumber',
                        'default' => 'string',
                    ],
                ],
            ],
            'valid' => false,
        ];
    }

    #[Test]
    #[DataProvider('selectNumberFieldSchemaValidationWorksAsExpectedDataProvider')]
    public function selectNumberFieldSchemaValidationWorksAsExpected(object $data, bool $valid): void
    {
        $jsonSchemaValidator = new JsonSchemaValidator();

        $validationResult = $jsonSchemaValidator->isValidContentElement($data);

        self::assertSame($valid, $validationResult);
    }
}
