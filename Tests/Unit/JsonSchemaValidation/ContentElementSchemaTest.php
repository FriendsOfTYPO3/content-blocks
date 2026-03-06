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

final class ContentElementSchemaTest extends UnitTestCase
{
    public static function contentElementSchemaIsValidDataProvider(): iterable
    {
        yield 'all fields' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'title' => 'My Element',
                'description' => 'My Element Description',
                'group' => 'my_group',
                'typeName' => 'my_type',
                'saveAndClose' => false,
            ],
            'valid' => true,
        ];

        yield 'Invalid Content Element typeName' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'title' => 'My Element',
                'description' => 'My Element Description',
                'group' => 'my_group',
                'typeName' => 123,
                'saveAndClose' => false,
            ],
            'valid' => false,
        ];
    }

    #[Test]
    #[DataProvider('contentElementSchemaIsValidDataProvider')]
    public function contentElementSchemaValidationWorksAsExpected(object $data, bool $valid): void
    {
        $jsonSchemaValidator = new JsonSchemaValidator();

        $validationResult = $jsonSchemaValidator->isValidContentElement($data);

        self::assertSame($valid, $validationResult);
    }
}
