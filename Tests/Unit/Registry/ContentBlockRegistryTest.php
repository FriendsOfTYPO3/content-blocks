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

namespace TYPO3\CMS\ContentBlocks\Tests\Unit\Registry;

use TYPO3\CMS\ContentBlocks\Loader\LoadedContentBlock;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class ContentBlockRegistryTest extends UnitTestCase
{
    /**
     * @test
     */
    public function duplicateContentBlockThrowsException(): void
    {
        $loadedContentBlockA = LoadedContentBlock::fromArray([
            'name' => 'example/a',
            'yaml' => [
                'table' => 'tt_content',
            ],
        ]);
        $loadedContentBlockB = LoadedContentBlock::fromArray([
            'name' => 'example/a',
            'yaml' => [
                'table' => 'tt_content',
            ],
        ]);

        $contentBlockRegistry = new ContentBlockRegistry();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1678474766);

        $contentBlockRegistry->register($loadedContentBlockA);
        $contentBlockRegistry->register($loadedContentBlockB);
    }

    /**
     * @test
     */
    public function duplicateTypeNameForContentElementThrowsException(): void
    {
        $loadedContentBlockA = LoadedContentBlock::fromArray([
            'name' => 'example/a',
            'yaml' => [
                'typeName' => 'example',
                'table' => 'tt_content',
            ],
        ]);
        $loadedContentBlockB = LoadedContentBlock::fromArray([
            'name' => 'example/b',
            'yaml' => [
                'typeName' => 'example',
                'table' => 'tt_content',
            ],
        ]);

        $contentBlockRegistry = new ContentBlockRegistry();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1701351270);

        $contentBlockRegistry->register($loadedContentBlockA);
        $contentBlockRegistry->register($loadedContentBlockB);
    }

    /**
     * @test
     */
    public function duplicateTypeNameForPageTypeThrowsException(): void
    {
        $loadedContentBlockA = LoadedContentBlock::fromArray([
            'name' => 'example/a',
            'yaml' => [
                'typeName' => '123',
                'table' => 'pages',
            ],
        ]);
        $loadedContentBlockB = LoadedContentBlock::fromArray([
            'name' => 'example/b',
            'yaml' => [
                'typeName' => '123',
                'table' => 'pages',
            ],
        ]);

        $contentBlockRegistry = new ContentBlockRegistry();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1701351270);

        $contentBlockRegistry->register($loadedContentBlockA);
        $contentBlockRegistry->register($loadedContentBlockB);
    }

    /**
     * @test
     */
    public function duplicateTypeNameForRecordTypeWithSameTableThrowsException(): void
    {
        $loadedContentBlockA = LoadedContentBlock::fromArray([
            'name' => 'example/a',
            'yaml' => [
                'typeName' => '123',
                'table' => 'my_record',
            ],
        ]);
        $loadedContentBlockB = LoadedContentBlock::fromArray([
            'name' => 'example/b',
            'yaml' => [
                'typeName' => '123',
                'table' => 'my_record',
            ],
        ]);

        $contentBlockRegistry = new ContentBlockRegistry();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1701351270);

        $contentBlockRegistry->register($loadedContentBlockA);
        $contentBlockRegistry->register($loadedContentBlockB);
    }

    /**
     * @test
     */
    public function duplicateTypeNameForRecordTypeDifferentTableIsAllowed(): void
    {
        $loadedContentBlockA = LoadedContentBlock::fromArray([
            'name' => 'example/a',
            'yaml' => [
                'typeName' => '123',
                'table' => 'my_record',
            ],
        ]);
        $loadedContentBlockB = LoadedContentBlock::fromArray([
            'name' => 'example/b',
            'yaml' => [
                'typeName' => '123',
                'table' => 'my_other_record',
            ],
        ]);

        $contentBlockRegistry = new ContentBlockRegistry();

        $contentBlockRegistry->register($loadedContentBlockA);
        $contentBlockRegistry->register($loadedContentBlockB);
    }
}
