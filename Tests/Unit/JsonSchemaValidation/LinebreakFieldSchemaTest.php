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

final class LinebreakFieldSchemaTest extends UnitTestCase
{
    public static function linebreakFieldSchemaValidationWorksAsExpectedDataProvider(): iterable
    {
        yield 'Valid properties with fields' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'palette',
                        'label' => 'Palette Label',
                        'description' => 'Palette Description',
                        'type' => 'Palette',
                        'fields' => [
                            (object)[
                                'identifier' => 'text_field',
                                'type' => 'Text',
                                'label' => 'Text Field',
                            ],
                            (object)[
                                'type' => 'Linebreak',
                            ],
                            (object)[
                                'identifier' => 'checkbox_field',
                                'type' => 'Checkbox',
                                'default' => 1,
                            ],
                        ],
                    ],
                ],
            ],
            'valid' => true,
        ];

        yield 'Linebreak not allowed outside of Palette' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'text_field',
                        'type' => 'Text',
                        'label' => 'Text Field',
                    ],
                    (object)[
                        'type' => 'Linebreak',
                    ],
                    (object)[
                        'identifier' => 'checkbox_field',
                        'type' => 'Checkbox',
                        'default' => 1,
                    ],
                ],
            ],
            'valid' => false,
        ];
    }

    #[Test]
    #[DataProvider('linebreakFieldSchemaValidationWorksAsExpectedDataProvider')]
    public function linebreakFieldSchemaValidationWorksAsExpected(object $data, bool $valid): void
    {
        $jsonSchemaValidator = new JsonSchemaValidator();

        $validationResult = $jsonSchemaValidator->isValidContentElement($data);

        self::assertSame($valid, $validationResult);
    }
}
