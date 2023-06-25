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

use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class ContentBlockRegistryTest extends FunctionalTestCase
{
    protected bool $initializeDatabase = false;

    protected array $coreExtensionsToLoad = [
        'content_blocks',
    ];

    protected array $testExtensionsToLoad = [
        'typo3/sysext/content_blocks/Tests/Fixtures/Extensions/foo',
        'typo3/sysext/content_blocks/Tests/Fixtures/Extensions/bar',
    ];

    public static function canRetrieveContentBlockPathByNameDataProvider(): iterable
    {
        yield 'Extension path for foo' => [
            'name' => 'foo/bar',
            'expected' => 'EXT:foo/ContentBlocks/foo',
        ];

        yield 'Extension path for bar' => [
            'name' => 'foo/bar',
            'expected' => 'EXT:foo/ContentBlocks/foo',
        ];
    }

    /**
     * @test
     * @dataProvider canRetrieveContentBlockPathByNameDataProvider
     */
    public function canRetrieveContentBlockPathByName(string $name, string $expected): void
    {
        $contentBlocksRegistry = $this->get(ContentBlockRegistry::class);

        $path = $contentBlocksRegistry->getContentBlockPath($name);

        self::assertSame($expected, $path);
    }

    /**
     * @test
     */
    public function unknownNameThrowsException(): void
    {
        $contentBlocksRegistry = $this->get(ContentBlockRegistry::class);

        $this->expectException(\OutOfBoundsException::class);
        $this->expectExceptionCode(1678478902);
        $this->expectExceptionMessage('Content block with the name "not/available" is not registered.');

        $contentBlocksRegistry->getContentBlockPath('not/available');
    }
}
