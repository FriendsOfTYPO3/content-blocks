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

namespace TYPO3\CMS\ContentBlocks\Tests\Unit\Validation;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\ContentBlocks\Validation\ContentBlockNameValidator;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class ContentBlockNameValidatorTest extends UnitTestCase
{
    public static function isValidDataProvider(): iterable
    {
        yield 'empty string' => [
            'string' => '',
            'expected' => false,
        ];

        yield 'one character' => [
            'string' => 'a',
            'expected' => true,
        ];

        yield 'one character dot' => [
            'string' => '.',
            'expected' => false,
        ];

        yield 'one character underscore' => [
            'string' => '_',
            'expected' => false,
        ];

        yield 'one character dash' => [
            'string' => '-',
            'expected' => false,
        ];

        yield 'normal word' => [
            'string' => 'vendor',
            'expected' => true,
        ];

        yield 'normal word separated by dashes' => [
            'string' => 'valid-vendor-name',
            'expected' => true,
        ];

        yield 'normal word separated by dots' => [
            'string' => 'valid.vendor.name',
            'expected' => false,
        ];

        yield 'normal word separated by underscore' => [
            'string' => 'valid_vendor_name',
            'expected' => false,
        ];
    }

    #[DataProvider('isValidDataProvider')]
    #[Test]
    public function isValid(string $string, bool $expected): void
    {
        self::assertSame($expected, ContentBlockNameValidator::isValid($string));
    }
}
