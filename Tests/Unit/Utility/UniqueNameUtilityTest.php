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

namespace TYPO3\CMS\ContentBlocks\Tests\Unit\Utility;

use TYPO3\CMS\ContentBlocks\Utility\UniqueNameUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class UniqueNameUtilityTest extends UnitTestCase
{
    public static function contentBlockNameToTypeIdentifierTestDataProvider(): iterable
    {
        yield 'simple name' => [
            'contentBlockName' => 'bar/foo',
            'expected' => 'bar_foo',
        ];

        yield 'name with dashes' => [
            'contentBlockName' => 'bar-foo/foo-bar',
            'expected' => 'barfoo_foobar',
        ];
    }

    /**
     * @dataProvider contentBlockNameToTypeIdentifierTestDataProvider
     * @test
     */
    public function contentBlockNameToTypeIdentifierTest(string $contentBlockName, string $expected): void
    {
        self::assertSame($expected, UniqueNameUtility::contentBlockNameToTypeIdentifier($contentBlockName));
    }

    public static function createUniqueColumnNameTestDataProvider(): iterable
    {
        yield 'simple name' => [
            'contentBlockName' => 'bar/foo',
            'identifier' => 'aField',
            'expected' => 'bar_foo_aField',
        ];

        yield 'name with dashes' => [
            'contentBlockName' => 'bar-foo/foo-bar',
            'identifier' => 'aField',
            'expected' => 'barfoo_foobar_aField',
        ];
    }

    /**
     * @dataProvider createUniqueColumnNameTestDataProvider
     * @test
     */
    public function createUniqueColumnNameTest(string $contentBlockName, string $identifier, string $expected): void
    {
        self::assertSame($expected, UniqueNameUtility::createUniqueColumnNameFromContentBlockName($contentBlockName, $identifier));
    }
}
