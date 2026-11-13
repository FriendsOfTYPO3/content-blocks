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

final class TabSchemaTest extends UnitTestCase
{
    public static function tabSchemaIsValidDataProvider(): iterable
    {
        yield 'only fields' => [
            'data' => (object)[
                'fields' => [
                    (object)[
                        'identifier' => 'text',
                        'type' => 'Text',
                    ],
                ],
            ],
            'valid' => true,
        ];

        yield 'identifier and label' => [
            'data' => (object)[
                'identifier' => 'settings',
                'label' => 'Settings',
                'fields' => [],
            ],
            'valid' => true,
        ];

        yield 'missing fields' => [
            'data' => (object)[
                'identifier' => 'settings',
            ],
            'valid' => false,
        ];

        yield 'unknown property' => [
            'data' => (object)[
                'title' => 'Settings',
                'fields' => [],
            ],
            'valid' => false,
        ];

        yield 'invalid identifier type' => [
            'data' => (object)[
                'identifier' => 1,
                'fields' => [],
            ],
            'valid' => false,
        ];

        yield 'invalid field' => [
            'data' => (object)[
                'fields' => [
                    (object)[
                        'type' => 'Text',
                    ],
                ],
            ],
            'valid' => false,
        ];
    }

    #[Test]
    #[DataProvider('tabSchemaIsValidDataProvider')]
    public function tabSchemaValidationWorksAsExpected(object $data, bool $valid): void
    {
        $jsonSchemaValidator = new JsonSchemaValidator();

        $validationResult = $jsonSchemaValidator->validateTab($data);

        self::assertSame($valid, $validationResult->isValid());
    }
}
