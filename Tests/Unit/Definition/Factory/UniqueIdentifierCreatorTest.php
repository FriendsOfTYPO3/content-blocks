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

namespace TYPO3\CMS\ContentBlocks\Tests\Unit\Definition\Factory;

use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;
use TYPO3\CMS\ContentBlocks\Definition\Factory\PrefixType;
use TYPO3\CMS\ContentBlocks\Definition\Factory\UniqueIdentifierCreator;
use TYPO3\CMS\ContentBlocks\Loader\LoadedContentBlock;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class UniqueIdentifierCreatorTest extends UnitTestCase
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
    public function contentBlockNameToContentTypeIdentifierTest(string $contentBlockName, string $expected): void
    {
        $contentBlock = new LoadedContentBlock(
            $contentBlockName,
            [],
            '',
            '',
            '',
            ContentType::CONTENT_ELEMENT,
        );
        self::assertSame($expected, UniqueIdentifierCreator::createContentTypeIdentifier($contentBlock));
    }

    public static function createUniqueColumnNameTestDataProvider(): iterable
    {
        yield 'simple name' => [
            'contentBlockName' => 'bar/foo',
            'prefixType' => PrefixType::FULL,
            'identifier' => 'aField',
            'expected' => 'bar_foo_aField',
        ];

        yield 'name with dashes' => [
            'contentBlockName' => 'bar-foo/foo-bar',
            'prefixType' => PrefixType::FULL,
            'identifier' => 'aField',
            'expected' => 'barfoo_foobar_aField',
        ];

        yield 'simple name vendor' => [
            'contentBlockName' => 'bar/foo',
            'prefixType' => PrefixType::VENDOR,
            'identifier' => 'aField',
            'expected' => 'bar_aField',
        ];

        yield 'name with dashes vendor' => [
            'contentBlockName' => 'bar-foo/foo-bar',
            'prefixType' => PrefixType::VENDOR,
            'identifier' => 'aField',
            'expected' => 'barfoo_aField',
        ];
    }

    /**
     * @dataProvider createUniqueColumnNameTestDataProvider
     * @test
     */
    public function createUniqueFieldIdentifierTest(string $contentBlockName, PrefixType $prefixType, string $identifier, string $expected): void
    {
        $contentBlock = new LoadedContentBlock(
            $contentBlockName,
            [],
            '',
            '',
            '',
            ContentType::CONTENT_ELEMENT,
        );
        self::assertSame($expected, UniqueIdentifierCreator::prefixIdentifier($contentBlock, $prefixType, $identifier));
    }
}
