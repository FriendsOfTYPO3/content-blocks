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

namespace TYPO3\CMS\ContentBlocks\Tests\Functional\Registry;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class ContentBlockRegistryTest extends FunctionalTestCase
{
    protected bool $initializeDatabase = false;

    protected array $testExtensionsToLoad = [
        'typo3conf/ext/content_blocks/Tests/Fixtures/Extensions/test_content_blocks_a',
        'typo3conf/ext/content_blocks/Tests/Fixtures/Extensions/test_content_blocks_b',
        'typo3conf/ext/content_blocks',
    ];

    protected function setUp(): void
    {
        parent::setUp();
    }

    public static function canRetrieveContentBlockPathByNameDataProvider(): iterable
    {
        yield 'Extension path A' => [
            'name' => 'typo3tests/content-element-a',
            'expected' => 'EXT:test_content_blocks_a/ContentBlocks/ContentElements/content-element-a',
        ];

        yield 'Extension path B' => [
            'name' => 'typo3tests/content-element-b',
            'expected' => 'EXT:test_content_blocks_b/ContentBlocks/ContentElements/content-element-b',
        ];
    }

    #[DataProvider('canRetrieveContentBlockPathByNameDataProvider')]
    #[Test]
    public function canRetrieveContentBlockPathByName(string $name, string $expected): void
    {
        $contentBlocksRegistry = $this->get(ContentBlockRegistry::class);

        $path = $contentBlocksRegistry->getContentBlockExtPath($name);

        self::assertSame($expected, $path);
    }

    #[Test]
    public function canRetrieveContentBlockByTableRecordWithType(): void
    {
        $contentBlocksRegistry = $this->get(ContentBlockRegistry::class);

        $table = 'tt_content';
        $record = ['CType' => 'typo3tests_contentelementa'];
        $contentBlock = $contentBlocksRegistry->getFromRawRecord($table, $record);

        self::assertNotNull($contentBlock);
    }

    #[Test]
    public function canRetrieveContentBlockByTableRecordWithoutType(): void
    {
        $contentBlocksRegistry = $this->get(ContentBlockRegistry::class);

        $table = 'test_record';
        $contentBlock = $contentBlocksRegistry->getFromRawRecord($table);

        self::assertNotNull($contentBlock);
    }

    #[Test]
    public function unknownNameThrowsException(): void
    {
        $contentBlocksRegistry = $this->get(ContentBlockRegistry::class);

        $this->expectException(\OutOfBoundsException::class);
        $this->expectExceptionCode(1678478902);
        $this->expectExceptionMessage('Content block with the name "not/available" is not registered.');

        $contentBlocksRegistry->getContentBlockExtPath('not/available');
    }
}
