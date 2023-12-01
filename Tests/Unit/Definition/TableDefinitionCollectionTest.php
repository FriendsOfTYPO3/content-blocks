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

namespace TYPO3\CMS\ContentBlocks\Tests\Unit\Definition;

use TYPO3\CMS\ContentBlocks\Definition\Factory\TableDefinitionCollectionFactory;
use TYPO3\CMS\ContentBlocks\Loader\LoadedContentBlock;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class TableDefinitionCollectionTest extends UnitTestCase
{
    /**
     * @test
     */
    public function contentElementDefinitionIsFoundByCType(): void
    {
        $contentBlocks = [
            [
                'name' => 'foo/bar',
                'icon' => '',
                'iconProvider' => '',
                'extPath' => 'EXT:example/ContentBlocks/foo',
                'yaml' => [
                    'table' => 'tt_content',
                    'typeField' => 'CType',
                    'fields' => [],
                ],
            ],
            [
                'name' => 't3ce/example',
                'icon' => '',
                'iconProvider' => '',
                'extPath' => 'EXT:example/ContentBlocks/example',
                'yaml' => [
                    'table' => 'tt_content',
                    'typeField' => 'CType',
                    'fields' => [],
                ],
            ],
        ];

        $GLOBALS['TCA']['tt_content']['ctrl']['type'] = 'CType';

        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $tableDefinitionCollection = (new TableDefinitionCollectionFactory())->createFromLoadedContentBlocks($contentBlockRegistry);
        $contentElementDefinition = $tableDefinitionCollection->getContentElementDefinition('t3ce_example');

        self::assertNotNull($contentElementDefinition);
        self::assertSame('t3ce', $contentElementDefinition->getVendor());
        self::assertSame('example', $contentElementDefinition->getPackage());
    }

    /**
     * @test
     */
    public function nonExistingContentElementReturnsNull(): void
    {
        $contentBlocks = [
            [
                'name' => 't3ce/example',
                'icon' => '',
                'iconProvider' => '',
                'extPath' => 'EXT:example/ContentBlocks/example',
                'yaml' => [
                    'table' => 'tt_content',
                    'typeField' => 'CType',
                    'fields' => [],
                ],
            ],
        ];

        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $tableDefinitionCollection = (new TableDefinitionCollectionFactory())->createFromLoadedContentBlocks($contentBlockRegistry);
        $contentElementDefinition = $tableDefinitionCollection->getContentElementDefinition('idonotexist');

        self::assertNull($contentElementDefinition);
    }

    /**
     * @test
     */
    public function contentBlocksCanBeSortedByPriority(): void
    {
        $contentBlocks = [
            [
                'name' => 'foo/bar',
                'icon' => '',
                'iconProvider' => '',
                'extPath' => 'EXT:example/ContentBlocks/foo',
                'yaml' => [
                    'table' => 'tt_content',
                    'typeField' => 'CType',
                    'fields' => [],
                ],
            ],
            [
                'name' => 't3ce/example',
                'icon' => '',
                'iconProvider' => '',
                'extPath' => 'EXT:example/ContentBlocks/example',
                'yaml' => [
                    'table' => 'tt_content',
                    'typeField' => 'CType',
                    'priority' => 20,
                    'fields' => [],
                ],
            ],
            [
                'name' => 'fizz/bar',
                'icon' => '',
                'iconProvider' => '',
                'extPath' => 'EXT:example/ContentBlocks/fizz',
                'yaml' => [
                    'table' => 'tt_content',
                    'typeField' => 'CType',
                    'priority' => 30,
                    'fields' => [],
                ],
            ],
        ];

        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $tableDefinitionCollection = (new TableDefinitionCollectionFactory())->createFromLoadedContentBlocks($contentBlockRegistry);
        $typeDefinitionCollection = $tableDefinitionCollection->getTable('tt_content')->getContentTypeDefinitionCollection();
        $result = [];
        foreach ($typeDefinitionCollection as $typeDefinition) {
            $result[] = $typeDefinition->getName();
        }

        self::assertSame(['fizz/bar', 't3ce/example', 'foo/bar'], $result);
    }
}
