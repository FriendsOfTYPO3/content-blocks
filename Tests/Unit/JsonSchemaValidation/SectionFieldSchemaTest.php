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

final class SectionFieldSchemaTest extends UnitTestCase
{
    public static function sectionFieldSchemaValidationWorksAsExpectedDataProvider(): iterable
    {
        yield 'Basic valid section' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'flex',
                        'type' => 'FlexForm',
                        'fields' => [
                            (object)[
                                'identifier' => 'section1',
                                'type' => 'Section',
                                'label' => 'A section',
                                'container' => [
                                    (object)[
                                        'identifier' => 'container1',
                                        'label' => 'A container',
                                        'fields' => [
                                            (object)[
                                                'identifier' => 'field1',
                                                'type' => 'Text',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'valid' => true,
        ];

        yield 'Valid section with multiple containers' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'flex',
                        'type' => 'FlexForm',
                        'fields' => [
                            (object)[
                                'identifier' => 'section1',
                                'type' => 'Section',
                                'container' => [
                                    (object)[
                                        'identifier' => 'c1',
                                        'fields' => [
                                            (object)['identifier' => 'f1', 'type' => 'Text'],
                                        ],
                                    ],
                                    (object)[
                                        'identifier' => 'c2',
                                        'fields' => [
                                            (object)['identifier' => 'f2', 'type' => 'Checkbox'],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'valid' => true,
        ];

        yield 'Section with various valid field types inside' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'flex',
                        'type' => 'FlexForm',
                        'fields' => [
                            (object)[
                                'identifier' => 'section1',
                                'type' => 'Section',
                                'container' => [
                                    (object)[
                                        'identifier' => 'c1',
                                        'fields' => [
                                            (object)['identifier' => 'f1', 'type' => 'Checkbox'],
                                            (object)['identifier' => 'f2', 'type' => 'Color'],
                                            (object)['identifier' => 'f3', 'type' => 'DateTime'],
                                            (object)['identifier' => 'f4', 'type' => 'Email'],
                                            (object)['identifier' => 'f5', 'type' => 'Json'],
                                            (object)['identifier' => 'f6', 'type' => 'Language'],
                                            (object)['identifier' => 'f7', 'type' => 'Link'],
                                            (object)['identifier' => 'f8', 'type' => 'Number'],
                                            (object)['identifier' => 'f9', 'type' => 'Password'],
                                            (object)['identifier' => 'f10', 'type' => 'Radio'],
                                            (object)['identifier' => 'f11', 'type' => 'Select'],
                                            (object)['identifier' => 'f13', 'type' => 'Slug'],
                                            (object)['identifier' => 'f14', 'type' => 'Text'],
                                            (object)['identifier' => 'f15', 'type' => 'Textarea'],
                                            (object)['identifier' => 'f16', 'type' => 'Uuid'],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'valid' => true,
        ];

        yield 'Missing container' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'flex',
                        'type' => 'FlexForm',
                        'fields' => [
                            (object)[
                                'identifier' => 'section1',
                                'type' => 'Section',
                            ],
                        ],
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'Unknown property at section level' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'flex',
                        'type' => 'FlexForm',
                        'fields' => [
                            (object)[
                                'identifier' => 'section1',
                                'type' => 'Section',
                                'container' => [],
                                'unknown' => 'prop',
                            ],
                        ],
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'Unknown property at container level' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'flex',
                        'type' => 'FlexForm',
                        'fields' => [
                            (object)[
                                'identifier' => 'section1',
                                'type' => 'Section',
                                'container' => [
                                    (object)[
                                        'identifier' => 'c1',
                                        'fields' => [],
                                        'unknown' => 'prop',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'Disallowed nested types (Collection) in Section' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'flex',
                        'type' => 'FlexForm',
                        'fields' => [
                            (object)[
                                'identifier' => 'section1',
                                'type' => 'Section',
                                'container' => [
                                    (object)[
                                        'identifier' => 'c1',
                                        'fields' => [
                                            (object)[
                                                'identifier' => 'f1',
                                                'type' => 'Collection',
                                                'fields' => [],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'Disallowed nested types (Section) in Section' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'flex',
                        'type' => 'FlexForm',
                        'fields' => [
                            (object)[
                                'identifier' => 'section1',
                                'type' => 'Section',
                                'container' => [
                                    (object)[
                                        'identifier' => 'c1',
                                        'fields' => [
                                            (object)[
                                                'identifier' => 'f1',
                                                'type' => 'Section',
                                                'container' => [],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'Disallowed nested types (Sheet) in Section' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'flex',
                        'type' => 'FlexForm',
                        'fields' => [
                            (object)[
                                'identifier' => 'section1',
                                'type' => 'Section',
                                'container' => [
                                    (object)[
                                        'identifier' => 'c1',
                                        'fields' => [
                                            (object)[
                                                'identifier' => 'f1',
                                                'type' => 'Sheet',
                                                'fields' => [],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'Disallowed nested types (Relation) in Section' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'flex',
                        'type' => 'FlexForm',
                        'fields' => [
                            (object)[
                                'identifier' => 'section1',
                                'type' => 'Section',
                                'container' => [
                                    (object)[
                                        'identifier' => 'c1',
                                        'fields' => [
                                            (object)[
                                                'identifier' => 'f1',
                                                'type' => 'Relation',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'valid' => false,
        ];
    }

    #[Test]
    #[DataProvider('sectionFieldSchemaValidationWorksAsExpectedDataProvider')]
    public function sectionFieldSchemaValidationWorksAsExpected(object $data, bool $valid): void
    {
        $jsonSchemaValidator = new JsonSchemaValidator();

        $validationResult = $jsonSchemaValidator->validateContentElement($data, 'http://typo3.org/content-element.json');

        self::assertSame($valid, $validationResult);
    }
}
