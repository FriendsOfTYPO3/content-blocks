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
    public function composerNameToTypeIdentifierTestDataProvider(): iterable
    {
        yield 'simple composer name' => [
            'composerName' => 'bar/foo',
            'expected' => 'bar_foo',
        ];

        yield 'with dashes' => [
            'composerName' => 'bar-foo/foo-bar',
            'expected' => 'barfoo_foobar',
        ];
    }

    /**
     * @dataProvider composerNameToTypeIdentifierTestDataProvider
     * @test
     */
    public function composerNameToTypeIdentifierTest(string $composerName, string $expected): void
    {
        self::assertSame($expected, UniqueNameUtility::composerNameToTypeIdentifier($composerName));
    }

    public function createUniqueColumnNameTestDataProvider(): iterable
    {
        yield 'simple composer name' => [
            'composerName' => 'bar/foo',
            'identifier' => 'aField',
            'expected' => 'bar_foo_aField',
        ];

        yield 'with dashes' => [
            'composerName' => 'bar-foo/foo-bar',
            'identifier' => 'aField',
            'expected' => 'barfoo_foobar_aField',
        ];
    }

    /**
     * @dataProvider createUniqueColumnNameTestDataProvider
     * @test
     */
    public function createUniqueColumnNameTest(string $composerName, string $identifier, string $expected): void
    {
        self::assertSame($expected, UniqueNameUtility::createUniqueColumnName($composerName, $identifier));
    }
}
