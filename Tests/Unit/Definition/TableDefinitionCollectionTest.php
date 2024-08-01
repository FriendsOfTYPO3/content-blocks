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

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\ContentBlocks\Definition\Factory\ContentBlockCompiler;
use TYPO3\CMS\ContentBlocks\Definition\Factory\TableDefinitionCollectionFactory;
use TYPO3\CMS\ContentBlocks\Loader\LoadedContentBlock;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\ContentBlocks\Tests\Unit\Fixtures\FieldTypeRegistryTestFactory;
use TYPO3\CMS\Core\Cache\Frontend\NullFrontend;
use TYPO3\CMS\Core\Cache\Frontend\PhpFrontend;
use TYPO3\CMS\Core\Schema\FieldTypeFactory;
use TYPO3\CMS\Core\Schema\RelationMapBuilder;
use TYPO3\CMS\Core\Schema\TcaSchemaFactory;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class TableDefinitionCollectionTest extends UnitTestCase
{
    #[Test]
    public function contentElementDefinitionIsFoundByCType(): void
    {
        $contentBlocks = [
            [
                'name' => 'foo/bar',
                'icon' => [
                    'iconPath' => '',
                    'iconProvider' => '',
                ],
                'extPath' => 'EXT:example/ContentBlocks/foo',
                'yaml' => [
                    'table' => 'tt_content',
                    'typeField' => 'CType',
                    'typeName' => 'foo_bar',
                    'fields' => [],
                ],
            ],
            [
                'name' => 't3ce/example',
                'icon' => [
                    'iconPath' => '',
                    'iconProvider' => '',
                ],
                'extPath' => 'EXT:example/ContentBlocks/example',
                'yaml' => [
                    'table' => 'tt_content',
                    'typeField' => 'CType',
                    'typeName' => 't3ce_example',
                    'fields' => [],
                ],
            ],
        ];

        $fieldTypeRegistry = FieldTypeRegistryTestFactory::create();
        $cacheMock = $this->createMock(PhpFrontend::class);
        $cacheMock->method('has')->with(self::isType('string'))->willReturn(false);
        $tcaSchemaFactory = new TcaSchemaFactory(
            new RelationMapBuilder(),
            new FieldTypeFactory(),
            '',
            $cacheMock
        );
        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $contentBlockCompiler = new ContentBlockCompiler();
        $tableDefinitionCollectionFactory = new TableDefinitionCollectionFactory(new NullFrontend('test'), $contentBlockCompiler);
        $tableDefinitionCollection = $tableDefinitionCollectionFactory->createUncached(
            $contentBlockRegistry,
            $fieldTypeRegistry,
            $tcaSchemaFactory
        );
        $contentElementDefinition = $tableDefinitionCollection->getContentElementDefinition('t3ce_example');

        self::assertSame('t3ce_example', $contentElementDefinition->getTypeName());
    }

    #[Test]
    public function nonExistingTableThrowsException(): void
    {
        $fieldTypeRegistry = FieldTypeRegistryTestFactory::create();
        $cacheMock = $this->createMock(PhpFrontend::class);
        $cacheMock->method('has')->with(self::isType('string'))->willReturn(false);
        $tcaSchemaFactory = new TcaSchemaFactory(
            new RelationMapBuilder(),
            new FieldTypeFactory(),
            '',
            $cacheMock
        );
        $contentBlockRegistry = new ContentBlockRegistry();
        $contentBlockCompiler = new ContentBlockCompiler();
        $tableDefinitionCollectionFactory = new TableDefinitionCollectionFactory(new NullFrontend('test'), $contentBlockCompiler);
        $tableDefinitionCollection = $tableDefinitionCollectionFactory->createUncached(
            $contentBlockRegistry,
            $fieldTypeRegistry,
            $tcaSchemaFactory
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1702413869);

        $tableDefinitionCollection->getContentElementDefinition('idonotexist');
    }

    #[Test]
    public function nonExistingContentElementThrowsException(): void
    {
        $contentBlocks = [
            [
                'name' => 't3ce/example',
                'icon' => [
                    'iconPath' => '',
                    'iconProvider' => '',
                ],
                'extPath' => 'EXT:example/ContentBlocks/example',
                'yaml' => [
                    'table' => 'tt_content',
                    'typeField' => 'CType',
                    'typeName' => 't3ce_example',
                    'fields' => [],
                ],
            ],
        ];

        $fieldTypeRegistry = FieldTypeRegistryTestFactory::create();
        $cacheMock = $this->createMock(PhpFrontend::class);
        $cacheMock->method('has')->with(self::isType('string'))->willReturn(false);
        $tcaSchemaFactory = new TcaSchemaFactory(
            new RelationMapBuilder(),
            new FieldTypeFactory(),
            '',
            $cacheMock
        );
        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $contentBlockCompiler = new ContentBlockCompiler();
        $tableDefinitionCollectionFactory = new TableDefinitionCollectionFactory(new NullFrontend('test'), $contentBlockCompiler);
        $tableDefinitionCollection = $tableDefinitionCollectionFactory->createUncached(
            $contentBlockRegistry,
            $fieldTypeRegistry,
            $tcaSchemaFactory
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1702413909);

        $tableDefinitionCollection->getContentElementDefinition('idonotexist');
    }

    public static function saveAndCloseIsAddedDataProvider(): iterable
    {
        yield 'saveAndClose is set to 1' => [
            'contentBlocks' => [
                [
                    'name' => 't3ce/example',
                    'icon' => [
                        'iconPath' => '',
                        'iconProvider' => '',
                    ],
                    'extPath' => 'EXT:example/ContentBlocks/example',
                    'yaml' => [
                        'table' => 'tt_content',
                        'typeField' => 'CType',
                        'typeName' => 'saveAndCloseTest',
                        'saveAndClose' => '1',
                    ],
                ],
            ],
            'typeName' => 'saveAndCloseTest',
            'expected' => true,
        ];

        yield 'saveAndClose is set to 0' => [
            'contentBlocks' => [
                [
                    'name' => 't3ce/example',
                    'icon' => [
                        'iconPath' => '',
                        'iconProvider' => '',
                    ],
                    'extPath' => 'EXT:example/ContentBlocks/example',
                    'yaml' => [
                        'table' => 'tt_content',
                        'typeField' => 'CType',
                        'typeName' => 'saveAndCloseTest',
                        'saveAndClose' => '0',
                    ],
                ],
            ],
            'typeName' => 'saveAndCloseTest',
            'expected' => false,
        ];
    }

    #[DataProvider('saveAndCloseIsAddedDataProvider')]
    #[Test]
    public function saveAndCloseIsAdded(array $contentBlocks, string $typeName, bool $expected): void
    {
        $fieldTypeRegistry = FieldTypeRegistryTestFactory::create();
        $cacheMock = $this->createMock(PhpFrontend::class);
        $cacheMock->method('has')->with(self::isType('string'))->willReturn(false);
        $tcaSchemaFactory = new TcaSchemaFactory(
            new RelationMapBuilder(),
            new FieldTypeFactory(),
            '',
            $cacheMock
        );
        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $contentBlockCompiler = new ContentBlockCompiler();
        $tableDefinitionCollectionFactory = new TableDefinitionCollectionFactory(new NullFrontend('test'), $contentBlockCompiler);
        $tableDefinitionCollection = $tableDefinitionCollectionFactory->createUncached(
            $contentBlockRegistry,
            $fieldTypeRegistry,
            $tcaSchemaFactory
        );

        $contentElement = $tableDefinitionCollection->getContentElementDefinition($typeName);

        self::assertSame($expected, $contentElement->hasSaveAndClose());
    }

    #[Test]
    public function contentBlocksCanBeSortedByPriority(): void
    {
        $contentBlocks = [
            [
                'name' => 'foo/bar',
                'icon' => [
                    'iconPath' => '',
                    'iconProvider' => '',
                ],
                'extPath' => 'EXT:example/ContentBlocks/foo',
                'yaml' => [
                    'table' => 'tt_content',
                    'typeField' => 'CType',
                    'typeName' => 'foo_bar',
                    'fields' => [],
                ],
            ],
            [
                'name' => 't3ce/example',
                'icon' => [
                    'iconPath' => '',
                    'iconProvider' => '',
                ],
                'extPath' => 'EXT:example/ContentBlocks/example',
                'yaml' => [
                    'table' => 'tt_content',
                    'typeField' => 'CType',
                    'typeName' => 't3ce_example',
                    'priority' => 20,
                    'fields' => [],
                ],
            ],
            [
                'name' => 'fizz/bar',
                'icon' => [
                    'iconPath' => '',
                    'iconProvider' => '',
                ],
                'extPath' => 'EXT:example/ContentBlocks/fizz',
                'yaml' => [
                    'table' => 'tt_content',
                    'typeField' => 'CType',
                    'typeName' => 'fizz_bar',
                    'priority' => 30,
                    'fields' => [],
                ],
            ],
        ];

        $fieldTypeRegistry = FieldTypeRegistryTestFactory::create();
        $cacheMock = $this->createMock(PhpFrontend::class);
        $cacheMock->method('has')->with(self::isType('string'))->willReturn(false);
        $tcaSchemaFactory = new TcaSchemaFactory(
            new RelationMapBuilder(),
            new FieldTypeFactory(),
            '',
            $cacheMock
        );
        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $contentBlockCompiler = new ContentBlockCompiler();
        $tableDefinitionCollectionFactory = new TableDefinitionCollectionFactory(new NullFrontend('test'), $contentBlockCompiler);
        $tableDefinitionCollection = $tableDefinitionCollectionFactory->createUncached(
            $contentBlockRegistry,
            $fieldTypeRegistry,
            $tcaSchemaFactory
        );
        $typeDefinitionCollection = $tableDefinitionCollection->getTable('tt_content')->getContentTypeDefinitionCollection();
        $result = [];
        foreach ($typeDefinitionCollection as $typeDefinition) {
            $result[] = $typeDefinition->getName();
        }

        self::assertSame(['fizz/bar', 't3ce/example', 'foo/bar'], $result);
    }
}
