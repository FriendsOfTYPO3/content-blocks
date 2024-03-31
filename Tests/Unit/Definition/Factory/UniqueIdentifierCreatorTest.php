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

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
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

    #[DataProvider('contentBlockNameToTypeIdentifierTestDataProvider')]
    #[Test]
    public function contentBlockNameToContentTypeIdentifierTest(string $contentBlockName, string $expected): void
    {
        $contentBlock = LoadedContentBlock::fromArray([
            'name' => $contentBlockName,
            'yaml' => ['table' => ContentType::CONTENT_ELEMENT->getTable()],
        ]);
        self::assertSame($expected, UniqueIdentifierCreator::createContentTypeIdentifier($contentBlock->getName()));
    }

    public static function createUniqueColumnNameTestDataProvider(): iterable
    {
        yield 'simple name' => [
            'contentBlockName' => 'bar/foo',
            'prefixType' => PrefixType::FULL,
            'vendorPrefix' => null,
            'identifier' => 'aField',
            'expected' => 'bar_foo_aField',
        ];

        yield 'name with dashes' => [
            'contentBlockName' => 'bar-foo/foo-bar',
            'prefixType' => PrefixType::FULL,
            'vendorPrefix' => null,
            'identifier' => 'aField',
            'expected' => 'barfoo_foobar_aField',
        ];

        yield 'name with dashes and vendorPrefix' => [
            'contentBlockName' => 'bar-foo/foo-bar',
            'prefixType' => PrefixType::FULL,
            'vendorPrefix' => 'tx_contentblocks',
            'identifier' => 'aField',
            'expected' => 'tx_contentblocks_foobar_aField',
        ];

        yield 'name with dashes and vendorPrefix with dashes' => [
            'contentBlockName' => 'bar-foo/foo-bar',
            'prefixType' => PrefixType::FULL,
            'vendorPrefix' => 'tx-contentblocks',
            'identifier' => 'aField',
            'expected' => 'txcontentblocks_foobar_aField',
        ];

        yield 'simple name vendor' => [
            'contentBlockName' => 'bar/foo',
            'prefixType' => PrefixType::VENDOR,
            'vendorPrefix' => null,
            'identifier' => 'aField',
            'expected' => 'bar_aField',
        ];

        yield 'name with dashes vendor' => [
            'contentBlockName' => 'bar-foo/foo-bar',
            'prefixType' => PrefixType::VENDOR,
            'vendorPrefix' => null,
            'identifier' => 'aField',
            'expected' => 'barfoo_aField',
        ];

        yield 'name with fixed vendor prefix' => [
            'contentBlockName' => 'bar-foo/foo-bar',
            'prefixType' => PrefixType::VENDOR,
            'vendorPrefix' => 'alternative_vendor',
            'identifier' => 'aField',
            'expected' => 'alternative_vendor_aField',
        ];

        yield 'name with fixed vendor prefix and dashes' => [
            'contentBlockName' => 'bar-foo/foo-bar',
            'prefixType' => PrefixType::VENDOR,
            'vendorPrefix' => 'alternative-vendor',
            'identifier' => 'aField',
            'expected' => 'alternativevendor_aField',
        ];
    }

    #[DataProvider('createUniqueColumnNameTestDataProvider')]
    #[Test]
    public function createUniqueFieldIdentifierTest(
        string $contentBlockName,
        PrefixType $prefixType,
        ?string $vendorPrefix,
        string $identifier,
        string $expected
    ): void {
        $contentBlock = LoadedContentBlock::fromArray([
            'name' => $contentBlockName,
            'yaml' => [
                'table' => ContentType::CONTENT_ELEMENT->getTable(),
                'vendorPrefix' => $vendorPrefix,
            ],
        ]);
        $result = UniqueIdentifierCreator::prefixIdentifier($contentBlock, $prefixType, $identifier);
        self::assertSame($expected, $result);
    }
}
