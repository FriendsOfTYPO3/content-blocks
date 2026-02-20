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

final class SlugFieldSchemaTest extends UnitTestCase
{
    public static function slugFieldSchemaValidationWorksAsExpectedDataProvider(): iterable
    {
        yield 'Only type' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'slug',
                        'type' => 'Slug',
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
                        'identifier' => 'slug',
                        'type' => 'Slug',
                        'appearance' => (object)[
                            'prefix' => 'https://example.com/',
                        ],
                        'behaviour' => (object)[
                            'allowLanguageSynchronization' => true,
                        ],
                        'default' => 'default-slug',
                        'eval' => 'uniqueInSite',
                        'fallbackCharacter' => '-',
                        'fieldControl' => (object)['foo' => 'bar'],
                        'fieldInformation' => (object)['foo' => 'bar'],
                        'fieldWizard' => (object)[
                            'defaultLanguageDifferences' => (object)['foo' => 'bar'],
                            'localizationStateSelector' => (object)['foo' => 'bar'],
                            'otherLanguageContent' => (object)['foo' => 'bar'],
                        ],
                        'generatorOptions' => (object)[
                            'fields' => ['header', ['subheader', 'title', ['foo', 'bar']]],
                            'fieldSeparator' => '/',
                            'prefixParentPageSlug' => true,
                            'regexReplacements' => (object)[
                                '/[^a-z0-9]/' => '-',
                            ],
                            'replacements' => (object)[
                                '&' => 'and',
                            ],
                            'postModifiers' => ['My\\Class->modify'],
                        ],
                        'prependSlash' => true,
                        'readOnly' => false,
                        'searchable' => true,
                        'size' => 30,
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
                        'identifier' => 'slug',
                        'type' => 'Slug',
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
                        'identifier' => 'slug',
                        'type' => 'Slug',
                        'size' => 5,
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
                        'identifier' => 'slug',
                        'type' => 'Slug',
                        'size' => 100,
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'invalid generatorOptions properties' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'slug',
                        'type' => 'Slug',
                        'generatorOptions' => (object)[
                            'unknown' => 'property',
                        ],
                    ],
                ],
            ],
            'valid' => false,
        ];
    }

    #[Test]
    #[DataProvider('slugFieldSchemaValidationWorksAsExpectedDataProvider')]
    public function slugFieldSchemaValidationWorksAsExpected(object $data, bool $valid): void
    {
        $jsonSchemaValidator = new JsonSchemaValidator();

        $validationResult = $jsonSchemaValidator->validateContentElement($data, 'http://typo3.org/content-element.json');

        self::assertSame($valid, $validationResult);
    }
}
