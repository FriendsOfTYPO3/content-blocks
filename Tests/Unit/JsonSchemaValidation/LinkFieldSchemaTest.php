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

final class LinkFieldSchemaTest extends UnitTestCase
{
    public static function linkFieldSchemaValidationWorksAsExpectedDataProvider(): iterable
    {
        yield 'Only type' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'link',
                        'type' => 'Link',
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
                        'identifier' => 'link',
                        'type' => 'Link',
                        'allowedTypes' => ['page', 'url', 'file', 'folder', 'email', 'telephone', 'record', '*'],
                        'behaviour' => (object)[
                            'allowLanguageSynchronization' => true,
                        ],
                        'appearance' => (object)[
                            'allowedOptions' => ['class', 'params', 'target', 'title', 'rel', 'body', 'cc', 'bcc', 'subject', '*'],
                            'allowedFileExtensions' => ['gif', 'jpg', 'jpeg', 'tif', 'tiff', 'bmp', 'pcx', 'tga', 'png', 'pdf', 'ai', 'svg', 'webp', 'avif', '*'],
                            'browserTitle' => 'Browser Title',
                            'enableBrowser' => true,
                        ],
                        'autocomplete' => true,
                        'default' => 't3://page?uid=1',
                        'fieldControl' => (object)['foo' => 'bar'],
                        'fieldInformation' => (object)['foo' => 'bar'],
                        'fieldWizard' => (object)[
                            'defaultLanguageDifferences' => (object)['disabled' => false],
                            'localizationStateSelector' => (object)['disabled' => false],
                            'otherLanguageContent' => (object)['disabled' => false],
                        ],
                        'mode' => 'useOrOverridePlaceholder',
                        'nullable' => true,
                        'placeholder' => 'placeholder',
                        'readOnly' => false,
                        'required' => false,
                        'searchable' => true,
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
                        'identifier' => 'link',
                        'type' => 'Link',
                        'unknown' => 'unknown',
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'invalid appearance allowedOptions' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'link',
                        'type' => 'Link',
                        'appearance' => (object)[
                            'allowedOptions' => ['invalid'],
                        ],
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
                        'identifier' => 'link',
                        'type' => 'Link',
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
                        'identifier' => 'link',
                        'type' => 'Link',
                        'size' => 51,
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
                        'identifier' => 'link',
                        'type' => 'Link',
                        'mode' => 'invalid',
                    ],
                ],
            ],
            'valid' => false,
        ];
    }

    #[Test]
    #[DataProvider('linkFieldSchemaValidationWorksAsExpectedDataProvider')]
    public function linkFieldSchemaValidationWorksAsExpected(object $data, bool $valid): void
    {
        $jsonSchemaValidator = new JsonSchemaValidator();

        $validationResult = $jsonSchemaValidator->isValidContentElement($data);

        self::assertSame($valid, $validationResult);
    }
}
