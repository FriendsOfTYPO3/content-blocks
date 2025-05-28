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
use TYPO3\CMS\ContentBlocks\Loader\ContentBlockLoader;
use TYPO3\CMS\ContentBlocks\Loader\LoadedContentBlock;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\ContentBlocks\Schema\FieldTypeResolver;
use TYPO3\CMS\ContentBlocks\Schema\SimpleTcaSchemaFactory;
use TYPO3\CMS\ContentBlocks\Tests\Unit\Fixtures\FieldTypeRegistryTestFactory;
use TYPO3\CMS\Core\Cache\Frontend\NullFrontend;
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
        $fieldTypeResolver = new FieldTypeResolver($fieldTypeRegistry);
        $simpleTcaSchemaFactory = new SimpleTcaSchemaFactory($fieldTypeResolver);
        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $contentBlockCompiler = new ContentBlockCompiler();
        $loader = $this->createMock(ContentBlockLoader::class);
        $tableDefinitionCollectionFactory = new TableDefinitionCollectionFactory(new NullFrontend('test'), $contentBlockCompiler, $loader);
        $tableDefinitionCollection = $tableDefinitionCollectionFactory->createUncached(
            $contentBlockRegistry,
            $fieldTypeRegistry,
            $simpleTcaSchemaFactory
        );
        $contentElementDefinition = $tableDefinitionCollection->getContentElementDefinition('t3ce_example');

        self::assertSame('t3ce_example', $contentElementDefinition->getTypeName());
    }

    #[Test]
    public function nonExistingTableThrowsException(): void
    {
        $fieldTypeRegistry = FieldTypeRegistryTestFactory::create();
        $fieldTypeResolver = new FieldTypeResolver($fieldTypeRegistry);
        $simpleTcaSchemaFactory = new SimpleTcaSchemaFactory($fieldTypeResolver);
        $contentBlockRegistry = new ContentBlockRegistry();
        $contentBlockCompiler = new ContentBlockCompiler();
        $loader = $this->createMock(ContentBlockLoader::class);
        $tableDefinitionCollectionFactory = new TableDefinitionCollectionFactory(new NullFrontend('test'), $contentBlockCompiler, $loader);
        $tableDefinitionCollection = $tableDefinitionCollectionFactory->createUncached(
            $contentBlockRegistry,
            $fieldTypeRegistry,
            $simpleTcaSchemaFactory
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
        $fieldTypeResolver = new FieldTypeResolver($fieldTypeRegistry);
        $simpleTcaSchemaFactory = new SimpleTcaSchemaFactory($fieldTypeResolver);
        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $contentBlockCompiler = new ContentBlockCompiler();
        $loader = $this->createMock(ContentBlockLoader::class);
        $tableDefinitionCollectionFactory = new TableDefinitionCollectionFactory(new NullFrontend('test'), $contentBlockCompiler, $loader);
        $tableDefinitionCollection = $tableDefinitionCollectionFactory->createUncached(
            $contentBlockRegistry,
            $fieldTypeRegistry,
            $simpleTcaSchemaFactory
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
        $fieldTypeResolver = new FieldTypeResolver($fieldTypeRegistry);
        $simpleTcaSchemaFactory = new SimpleTcaSchemaFactory($fieldTypeResolver);
        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $contentBlockCompiler = new ContentBlockCompiler();
        $loader = $this->createMock(ContentBlockLoader::class);
        $tableDefinitionCollectionFactory = new TableDefinitionCollectionFactory(new NullFrontend('test'), $contentBlockCompiler, $loader);
        $tableDefinitionCollection = $tableDefinitionCollectionFactory->createUncached(
            $contentBlockRegistry,
            $fieldTypeRegistry,
            $simpleTcaSchemaFactory
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
        $fieldTypeResolver = new FieldTypeResolver($fieldTypeRegistry);
        $simpleTcaSchemaFactory = new SimpleTcaSchemaFactory($fieldTypeResolver);
        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $contentBlockCompiler = new ContentBlockCompiler();
        $loader = $this->createMock(ContentBlockLoader::class);
        $tableDefinitionCollectionFactory = new TableDefinitionCollectionFactory(new NullFrontend('test'), $contentBlockCompiler, $loader);
        $tableDefinitionCollection = $tableDefinitionCollectionFactory->createUncached(
            $contentBlockRegistry,
            $fieldTypeRegistry,
            $simpleTcaSchemaFactory
        );
        $typeDefinitionCollection = $tableDefinitionCollection->getTable('tt_content')->contentTypeDefinitionCollection;
        $result = [];
        foreach ($typeDefinitionCollection as $typeDefinition) {
            $result[] = $typeDefinition->getName();
        }

        self::assertSame(['fizz/bar', 't3ce/example', 'foo/bar'], $result);
    }
}
