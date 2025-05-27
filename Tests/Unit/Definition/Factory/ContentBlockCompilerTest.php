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

final class ContentBlockCompilerTest extends UnitTestCase
{
    public static function notUniqueIdentifiersThrowAnExceptionDataProvider(): iterable
    {
        yield 'two collections with the same identifier' => [
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
                        'typeName' => 'foo_bar',
                        'fields' => [
                            [
                                'identifier' => 'foo',
                                'type' => 'Text',
                            ],
                            [
                                'identifier' => 'bar',
                                'type' => 'Text',
                            ],
                            [
                                'identifier' => 'foo',
                                'type' => 'Text',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    #[DataProvider('notUniqueIdentifiersThrowAnExceptionDataProvider')]
    #[Test]
    public function notUniqueIdentifiersThrowAnException(array $contentBlocks): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1677407942);
        $this->expectExceptionMessage('The identifier "foo" in Content Block "t3ce/example" does exist more than once. Please choose unique identifiers.');

        $fieldTypeRegistry = FieldTypeRegistryTestFactory::create();
        $fieldTypeResolver = new FieldTypeResolver($fieldTypeRegistry);
        $simpleTcaSchemaFactory = new SimpleTcaSchemaFactory(new NullFrontend('test'), $fieldTypeResolver);
        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $contentBlockCompiler = new ContentBlockCompiler();
        $loader = $this->createMock(ContentBlockLoader::class);
        $tableDefinitionCollectionFactory = new TableDefinitionCollectionFactory(new NullFrontend('test'), $contentBlockCompiler, $loader);
        $tableDefinitionCollectionFactory->createUncached(
            $contentBlockRegistry,
            $fieldTypeRegistry,
            $simpleTcaSchemaFactory
        );
    }

    public static function notUniqueIdentifiersWithinCollectionThrowAnExceptionDataProvider(): iterable
    {
        yield 'two collections with the same identifier' => [
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
                        'typeName' => 'foo_bar',
                        'fields' => [
                            [
                                'identifier' => 'collection',
                                'type' => 'Collection',
                                'fields' => [
                                    [
                                        'identifier' => 'foo',
                                        'type' => 'Text',
                                    ],
                                    [
                                        'identifier' => 'bar',
                                        'type' => 'Text',
                                    ],
                                    [
                                        'identifier' => 'foo',
                                        'type' => 'Text',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    #[DataProvider('notUniqueIdentifiersWithinCollectionThrowAnExceptionDataProvider')]
    #[Test]
    public function notUniqueIdentifiersWithinCollectionThrowAnException(array $contentBlocks): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1677407942);
        $this->expectExceptionMessage('The identifier "foo" in Content Block "t3ce/example" does exist more than once. Please choose unique identifiers.');

        $fieldTypeRegistry = FieldTypeRegistryTestFactory::create();
        $fieldTypeResolver = new FieldTypeResolver($fieldTypeRegistry);
        $simpleTcaSchemaFactory = new SimpleTcaSchemaFactory(new NullFrontend('test'), $fieldTypeResolver);
        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $contentBlockCompiler = new ContentBlockCompiler();
        $loader = $this->createMock(ContentBlockLoader::class);
        $tableDefinitionCollectionFactory = new TableDefinitionCollectionFactory(new NullFrontend('test'), $contentBlockCompiler, $loader);
        $tableDefinitionCollectionFactory->createUncached(
            $contentBlockRegistry,
            $fieldTypeRegistry,
            $simpleTcaSchemaFactory
        );
    }

    public static function differentTypesPerIdentifierThrowExceptionDataProvider(): iterable
    {
        yield 'Two Content Blocks with same identifier and different types' => [
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
                        'typeName' => 'foo_bar',
                        'prefixFields' => false,
                        'fields' => [
                            [
                                'identifier' => 'foo',
                                'type' => 'Text',
                            ],
                        ],
                    ],
                ],
                [
                    'name' => 't3ce/example2',
                    'icon' => [
                        'iconPath' => '',
                        'iconProvider' => '',
                    ],
                    'extPath' => 'EXT:example/ContentBlocks/example2',
                    'yaml' => [
                        'table' => 'tt_content',
                        'typeField' => 'CType',
                        'typeName' => 'foo_bar2',
                        'prefixFields' => false,
                        'fields' => [
                            [
                                'identifier' => 'foo',
                                'type' => 'Link',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    #[DataProvider('differentTypesPerIdentifierThrowExceptionDataProvider')]
    #[Test]
    public function differentTypesPerIdentifierThrowException(array $contentBlocks): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1741707494);

        $fieldTypeRegistry = FieldTypeRegistryTestFactory::create();
        $fieldTypeResolver = new FieldTypeResolver($fieldTypeRegistry);
        $simpleTcaSchemaFactory = new SimpleTcaSchemaFactory(new NullFrontend('test'), $fieldTypeResolver);
        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $contentBlockCompiler = new ContentBlockCompiler();
        $loader = $this->createMock(ContentBlockLoader::class);
        $tableDefinitionCollectionFactory = new TableDefinitionCollectionFactory(new NullFrontend('test'), $contentBlockCompiler, $loader);
        $tableDefinitionCollectionFactory->createUncached(
            $contentBlockRegistry,
            $fieldTypeRegistry,
            $simpleTcaSchemaFactory
        );
    }

    #[Test]
    public function paletteInsidePaletteIsNotAllowed(): void
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
                    'fields' => [
                        [
                            'identifier' => 'palette_1',
                            'type' => 'Palette',
                            'fields' => [
                                [
                                    'identifier' => 'palette_inside_palette',
                                    'type' => 'Palette',
                                    'fields' => [],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1679168602);
        $this->expectExceptionMessage('Palette "palette_inside_palette" is not allowed inside palette "palette_1" in Content Block "foo/bar".');

        $fieldTypeRegistry = FieldTypeRegistryTestFactory::create();
        $fieldTypeResolver = new FieldTypeResolver($fieldTypeRegistry);
        $simpleTcaSchemaFactory = new SimpleTcaSchemaFactory(new NullFrontend('test'), $fieldTypeResolver);
        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $contentBlockCompiler = new ContentBlockCompiler();
        $loader = $this->createMock(ContentBlockLoader::class);
        $tableDefinitionCollectionFactory = new TableDefinitionCollectionFactory(new NullFrontend('test'), $contentBlockCompiler, $loader);
        $tableDefinitionCollectionFactory->createUncached(
            $contentBlockRegistry,
            $fieldTypeRegistry,
            $simpleTcaSchemaFactory
        );
    }

    #[Test]
    public function paletteInsidePaletteInsideCollectionIsNotAllowed(): void
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
                    'fields' => [
                        [
                            'identifier' => 'inline',
                            'type' => 'Collection',
                            'fields' => [
                                [
                                    'identifier' => 'palette_1',
                                    'type' => 'Palette',
                                    'fields' => [
                                        [
                                            'identifier' => 'palette_inside_palette',
                                            'type' => 'Palette',
                                            'fields' => [],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1679168602);
        $this->expectExceptionMessage('Palette "palette_inside_palette" is not allowed inside palette "palette_1" in Content Block "foo/bar".');

        $fieldTypeRegistry = FieldTypeRegistryTestFactory::create();
        $fieldTypeResolver = new FieldTypeResolver($fieldTypeRegistry);
        $simpleTcaSchemaFactory = new SimpleTcaSchemaFactory(new NullFrontend('test'), $fieldTypeResolver);
        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $contentBlockCompiler = new ContentBlockCompiler();
        $loader = $this->createMock(ContentBlockLoader::class);
        $tableDefinitionCollectionFactory = new TableDefinitionCollectionFactory(new NullFrontend('test'), $contentBlockCompiler, $loader);
        $tableDefinitionCollectionFactory->createUncached(
            $contentBlockRegistry,
            $fieldTypeRegistry,
            $simpleTcaSchemaFactory
        );
    }

    #[Test]
    public function paletteWithSameIdentifierIsNotAllowed(): void
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
                    'fields' => [
                        [
                            'identifier' => 'palette_1',
                            'type' => 'Palette',
                            'fields' => [
                                [
                                    'identifier' => 'field1',
                                    'type' => 'Text',
                                ],
                                [
                                    'identifier' => 'field2',
                                    'type' => 'Text',
                                ],
                            ],
                        ],
                        [
                            'identifier' => 'palette_1',
                            'type' => 'Palette',
                            'fields' => [
                                [
                                    'identifier' => 'field3',
                                    'type' => 'Text',
                                ],
                                [
                                    'identifier' => 'field4',
                                    'type' => 'Text',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1679168022);
        $this->expectExceptionMessage('The palette identifier "palette_1" in Content Block "foo/bar" does exist more than once. Please choose unique identifiers.');

        $fieldTypeRegistry = FieldTypeRegistryTestFactory::create();
        $fieldTypeResolver = new FieldTypeResolver($fieldTypeRegistry);
        $simpleTcaSchemaFactory = new SimpleTcaSchemaFactory(new NullFrontend('test'), $fieldTypeResolver);
        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $contentBlockCompiler = new ContentBlockCompiler();
        $loader = $this->createMock(ContentBlockLoader::class);
        $tableDefinitionCollectionFactory = new TableDefinitionCollectionFactory(new NullFrontend('test'), $contentBlockCompiler, $loader);
        $tableDefinitionCollectionFactory->createUncached(
            $contentBlockRegistry,
            $fieldTypeRegistry,
            $simpleTcaSchemaFactory
        );
    }

    #[Test]
    public function paletteWithSameIdentifierInsideCollectionIsNotAllowed(): void
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
                    'fields' => [
                        [
                            'identifier' => 'inline',
                            'type' => 'Collection',
                            'fields' => [
                                [
                                    'identifier' => 'palette_1',
                                    'type' => 'Palette',
                                    'fields' => [
                                        [
                                            'identifier' => 'field1',
                                            'type' => 'Text',
                                        ],
                                        [
                                            'identifier' => 'field2',
                                            'type' => 'Text',
                                        ],
                                    ],
                                ],
                                [
                                    'identifier' => 'palette_1',
                                    'type' => 'Palette',
                                    'fields' => [
                                        [
                                            'identifier' => 'field3',
                                            'type' => 'Text',
                                        ],
                                        [
                                            'identifier' => 'field4',
                                            'type' => 'Text',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1679168022);
        $this->expectExceptionMessage('The palette identifier "palette_1" in Content Block "foo/bar" does exist more than once. Please choose unique identifiers.');

        $fieldTypeRegistry = FieldTypeRegistryTestFactory::create();
        $fieldTypeResolver = new FieldTypeResolver($fieldTypeRegistry);
        $simpleTcaSchemaFactory = new SimpleTcaSchemaFactory(new NullFrontend('test'), $fieldTypeResolver);
        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $contentBlockCompiler = new ContentBlockCompiler();
        $loader = $this->createMock(ContentBlockLoader::class);
        $tableDefinitionCollectionFactory = new TableDefinitionCollectionFactory(new NullFrontend('test'), $contentBlockCompiler, $loader);
        $tableDefinitionCollectionFactory->createUncached(
            $contentBlockRegistry,
            $fieldTypeRegistry,
            $simpleTcaSchemaFactory
        );
    }

    #[Test]
    public function tabWithSameIdentifierIsNotAllowed(): void
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
                    'fields' => [
                        [
                            'identifier' => 'tab_1',
                            'type' => 'Tab',
                        ],
                        [
                            'identifier' => 'tab_2',
                            'type' => 'Tab',
                        ],
                        [
                            'identifier' => 'tab_1',
                            'type' => 'Tab',
                        ],
                    ],
                ],
            ],
        ];

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1679243686);
        $this->expectExceptionMessage('The tab identifier "tab_1" in Content Block "foo/bar" does exist more than once. Please choose unique identifiers.');

        $fieldTypeRegistry = FieldTypeRegistryTestFactory::create();
        $fieldTypeResolver = new FieldTypeResolver($fieldTypeRegistry);
        $simpleTcaSchemaFactory = new SimpleTcaSchemaFactory(new NullFrontend('test'), $fieldTypeResolver);
        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $contentBlockCompiler = new ContentBlockCompiler();
        $loader = $this->createMock(ContentBlockLoader::class);
        $tableDefinitionCollectionFactory = new TableDefinitionCollectionFactory(new NullFrontend('test'), $contentBlockCompiler, $loader);
        $tableDefinitionCollectionFactory->createUncached(
            $contentBlockRegistry,
            $fieldTypeRegistry,
            $simpleTcaSchemaFactory
        );
    }

    #[Test]
    public function tabWithSameIdentifierInsideCollectionIsNotAllowed(): void
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
                    'fields' => [
                        [
                            'identifier' => 'inline',
                            'type' => 'Collection',
                            'fields' => [
                                [
                                    'identifier' => 'tab_1',
                                    'type' => 'Tab',
                                ],
                                [
                                    'identifier' => 'tab_2',
                                    'type' => 'Tab',
                                ],
                                [
                                    'identifier' => 'tab_1',
                                    'type' => 'Tab',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1679243686);
        $this->expectExceptionMessage('The tab identifier "tab_1" in Content Block "foo/bar" does exist more than once. Please choose unique identifiers.');

        $fieldTypeRegistry = FieldTypeRegistryTestFactory::create();
        $fieldTypeResolver = new FieldTypeResolver($fieldTypeRegistry);
        $simpleTcaSchemaFactory = new SimpleTcaSchemaFactory(new NullFrontend('test'), $fieldTypeResolver);
        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $contentBlockCompiler = new ContentBlockCompiler();
        $loader = $this->createMock(ContentBlockLoader::class);
        $tableDefinitionCollectionFactory = new TableDefinitionCollectionFactory(new NullFrontend('test'), $contentBlockCompiler, $loader);
        $tableDefinitionCollectionFactory->createUncached(
            $contentBlockRegistry,
            $fieldTypeRegistry,
            $simpleTcaSchemaFactory
        );
    }

    #[Test]
    public function tabInsidePaletteIsNotAllowed(): void
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
                    'fields' => [
                        [
                            'identifier' => 'palette_1',
                            'type' => 'Palette',
                            'fields' => [
                                [
                                    'identifier' => 'tab_1',
                                    'type' => 'Tab',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1679245193);
        $this->expectExceptionMessage('Tab "tab_1" is not allowed inside palette "palette_1" in Content Block "foo/bar".');

        $fieldTypeRegistry = FieldTypeRegistryTestFactory::create();
        $fieldTypeResolver = new FieldTypeResolver($fieldTypeRegistry);
        $simpleTcaSchemaFactory = new SimpleTcaSchemaFactory(new NullFrontend('test'), $fieldTypeResolver);
        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $contentBlockCompiler = new ContentBlockCompiler();
        $loader = $this->createMock(ContentBlockLoader::class);
        $tableDefinitionCollectionFactory = new TableDefinitionCollectionFactory(new NullFrontend('test'), $contentBlockCompiler, $loader);
        $tableDefinitionCollectionFactory->createUncached(
            $contentBlockRegistry,
            $fieldTypeRegistry,
            $simpleTcaSchemaFactory
        );
    }

    #[Test]
    public function tabInsidePaletteInsideCollectionIsNotAllowed(): void
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
                    'fields' => [
                        [
                            'identifier' => 'inline',
                            'type' => 'Collection',
                            'fields' => [
                                [
                                    'identifier' => 'palette_1',
                                    'type' => 'Palette',
                                    'fields' => [
                                        [
                                            'identifier' => 'tab_1',
                                            'type' => 'Tab',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1679245193);
        $this->expectExceptionMessage('Tab "tab_1" is not allowed inside palette "palette_1" in Content Block "foo/bar".');

        $fieldTypeRegistry = FieldTypeRegistryTestFactory::create();
        $fieldTypeResolver = new FieldTypeResolver($fieldTypeRegistry);
        $simpleTcaSchemaFactory = new SimpleTcaSchemaFactory(new NullFrontend('test'), $fieldTypeResolver);
        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $contentBlockCompiler = new ContentBlockCompiler();
        $loader = $this->createMock(ContentBlockLoader::class);
        $tableDefinitionCollectionFactory = new TableDefinitionCollectionFactory(new NullFrontend('test'), $contentBlockCompiler, $loader);
        $tableDefinitionCollectionFactory->createUncached(
            $contentBlockRegistry,
            $fieldTypeRegistry,
            $simpleTcaSchemaFactory
        );
    }

    #[Test]
    public function linebreaksAreOnlyAllowedWithinPalettes(): void
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
                    'fields' => [
                        [
                            'identifier' => 'palette_1',
                            'type' => 'Palette',
                            'fields' => [
                                [
                                    'identifier' => 'field1',
                                    'type' => 'Text',
                                ],
                                [
                                    'type' => 'Linebreak',
                                ],
                                [
                                    'identifier' => 'field2',
                                    'type' => 'Text',
                                ],
                            ],
                        ],
                        [
                            'type' => 'Linebreak',
                        ],
                    ],
                ],
            ],
        ];

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1679224392);
        $this->expectExceptionMessage('Linebreaks are only allowed within Palettes in Content Block "foo/bar".');

        $fieldTypeRegistry = FieldTypeRegistryTestFactory::create();
        $fieldTypeResolver = new FieldTypeResolver($fieldTypeRegistry);
        $simpleTcaSchemaFactory = new SimpleTcaSchemaFactory(new NullFrontend('test'), $fieldTypeResolver);
        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $contentBlockCompiler = new ContentBlockCompiler();
        $loader = $this->createMock(ContentBlockLoader::class);
        $tableDefinitionCollectionFactory = new TableDefinitionCollectionFactory(new NullFrontend('test'), $contentBlockCompiler, $loader);
        $tableDefinitionCollectionFactory->createUncached(
            $contentBlockRegistry,
            $fieldTypeRegistry,
            $simpleTcaSchemaFactory
        );
    }

    #[Test]
    public function linebreaksCanBeIgnoredIfConfiguredExplicitly(): void
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
                    'fields' => [
                        [
                            'identifier' => 'palette_1',
                            'type' => 'Palette',
                            'fields' => [
                                [
                                    'identifier' => 'field1',
                                    'type' => 'Text',
                                ],
                                [
                                    'type' => 'Linebreak',
                                ],
                                [
                                    'identifier' => 'field2',
                                    'type' => 'Text',
                                ],
                            ],
                        ],
                        [
                            'type' => 'Linebreak',
                            'ignoreIfNotInPalette' => true,
                        ],
                    ],
                ],
            ],
        ];

        $fieldTypeRegistry = FieldTypeRegistryTestFactory::create();
        $fieldTypeResolver = new FieldTypeResolver($fieldTypeRegistry);
        $simpleTcaSchemaFactory = new SimpleTcaSchemaFactory(new NullFrontend('test'), $fieldTypeResolver);
        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $contentBlockCompiler = new ContentBlockCompiler();
        $loader = $this->createMock(ContentBlockLoader::class);
        $tableDefinitionCollectionFactory = new TableDefinitionCollectionFactory(new NullFrontend('test'), $contentBlockCompiler, $loader);
        $tableDefinitionCollectionFactory->createUncached(
            $contentBlockRegistry,
            $fieldTypeRegistry,
            $simpleTcaSchemaFactory
        );
    }

    #[Test]
    public function linebreaksAreOnlyAllowedWithinPalettesInsideCollections(): void
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
                    'fields' => [
                        [
                            'identifier' => 'inline',
                            'type' => 'Collection',
                            'fields' => [
                                [
                                    'identifier' => 'palette_1',
                                    'type' => 'Palette',
                                    'fields' => [
                                        [
                                            'identifier' => 'field1',
                                            'type' => 'Text',
                                        ],
                                        [
                                            'type' => 'Linebreak',
                                        ],
                                        [
                                            'identifier' => 'field2',
                                            'type' => 'Text',
                                        ],
                                    ],
                                ],
                                [
                                    'type' => 'Linebreak',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1679224392);
        $this->expectExceptionMessage('Linebreaks are only allowed within Palettes in Content Block "foo/bar".');

        $fieldTypeRegistry = FieldTypeRegistryTestFactory::create();
        $fieldTypeResolver = new FieldTypeResolver($fieldTypeRegistry);
        $simpleTcaSchemaFactory = new SimpleTcaSchemaFactory(new NullFrontend('test'), $fieldTypeResolver);
        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $contentBlockCompiler = new ContentBlockCompiler();
        $loader = $this->createMock(ContentBlockLoader::class);
        $tableDefinitionCollectionFactory = new TableDefinitionCollectionFactory(new NullFrontend('test'), $contentBlockCompiler, $loader);
        $tableDefinitionCollectionFactory->createUncached(
            $contentBlockRegistry,
            $fieldTypeRegistry,
            $simpleTcaSchemaFactory
        );
    }

    #[Test]
    public function identifierIsRequired(): void
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
                    'fields' => [
                        [
                            'type' => 'Text',
                            'identifier' => 'text1',
                        ],
                        [
                            'type' => 'Text',
                        ],
                    ],
                ],
            ],
        ];

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1679226075);
        $this->expectExceptionMessage('A field is missing the required "identifier" in Content Block "foo/bar".');

        $fieldTypeRegistry = FieldTypeRegistryTestFactory::create();
        $fieldTypeResolver = new FieldTypeResolver($fieldTypeRegistry);
        $simpleTcaSchemaFactory = new SimpleTcaSchemaFactory(new NullFrontend('test'), $fieldTypeResolver);
        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $contentBlockCompiler = new ContentBlockCompiler();
        $loader = $this->createMock(ContentBlockLoader::class);
        $tableDefinitionCollectionFactory = new TableDefinitionCollectionFactory(new NullFrontend('test'), $contentBlockCompiler, $loader);
        $tableDefinitionCollectionFactory->createUncached(
            $contentBlockRegistry,
            $fieldTypeRegistry,
            $simpleTcaSchemaFactory
        );
    }

    #[Test]
    public function typeIsRequired(): void
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
                    'fields' => [
                        [
                            'identifier' => 'text1',
                        ],
                    ],
                ],
            ],
        ];

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1694768937);
        $this->expectExceptionMessage('The field "text1" is missing the required "type" in Content Block "foo/bar".');

        $fieldTypeRegistry = FieldTypeRegistryTestFactory::create();
        $fieldTypeResolver = new FieldTypeResolver($fieldTypeRegistry);
        $simpleTcaSchemaFactory = new SimpleTcaSchemaFactory(new NullFrontend('test'), $fieldTypeResolver);
        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $contentBlockCompiler = new ContentBlockCompiler();
        $loader = $this->createMock(ContentBlockLoader::class);
        $tableDefinitionCollectionFactory = new TableDefinitionCollectionFactory(new NullFrontend('test'), $contentBlockCompiler, $loader);
        $tableDefinitionCollectionFactory->createUncached(
            $contentBlockRegistry,
            $fieldTypeRegistry,
            $simpleTcaSchemaFactory
        );
    }

    #[Test]
    public function identifierIsRequiredInsideCollections(): void
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
                    'fields' => [
                        [
                            'identifier' => 'inline',
                            'type' => 'Collection',
                            'fields' => [
                                [
                                    'identifier' => 'text1',
                                    'type' => 'Text',
                                ],
                                [
                                    'type' => 'Text',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1679226075);
        $this->expectExceptionMessage('A field is missing the required "identifier" in Content Block "foo/bar".');

        $fieldTypeRegistry = FieldTypeRegistryTestFactory::create();
        $fieldTypeResolver = new FieldTypeResolver($fieldTypeRegistry);
        $simpleTcaSchemaFactory = new SimpleTcaSchemaFactory(new NullFrontend('test'), $fieldTypeResolver);
        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $contentBlockCompiler = new ContentBlockCompiler();
        $loader = $this->createMock(ContentBlockLoader::class);
        $tableDefinitionCollectionFactory = new TableDefinitionCollectionFactory(new NullFrontend('test'), $contentBlockCompiler, $loader);
        $tableDefinitionCollectionFactory->createUncached(
            $contentBlockRegistry,
            $fieldTypeRegistry,
            $simpleTcaSchemaFactory
        );
    }

    #[Test]
    public function flexFieldIsNotAllowedToMixNonSheetAndSheet(): void
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
                    'fields' => [
                        [
                            'identifier' => 'inline',
                            'type' => 'Collection',
                            'fields' => [
                                [
                                    'identifier' => 'flexField',
                                    'type' => 'FlexForm',
                                    'fields' => [
                                        [
                                            'identifier' => 'foo',
                                            'type' => 'Text',
                                        ],
                                        [
                                            'identifier' => 'aSheet',
                                            'type' => 'Sheet',
                                            'fields' => [
                                                [
                                                    'identifier' => 'bar',
                                                    'type' => 'Text',
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('You must not mix Sheets with normal fields inside the FlexForm definition "flexField" in Content Block "foo/bar".');
        $this->expectExceptionCode(1685217163);

        $fieldTypeRegistry = FieldTypeRegistryTestFactory::create();
        $fieldTypeResolver = new FieldTypeResolver($fieldTypeRegistry);
        $simpleTcaSchemaFactory = new SimpleTcaSchemaFactory(new NullFrontend('test'), $fieldTypeResolver);
        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $contentBlockCompiler = new ContentBlockCompiler();
        $loader = $this->createMock(ContentBlockLoader::class);
        $tableDefinitionCollectionFactory = new TableDefinitionCollectionFactory(new NullFrontend('test'), $contentBlockCompiler, $loader);
        $tableDefinitionCollectionFactory->createUncached(
            $contentBlockRegistry,
            $fieldTypeRegistry,
            $simpleTcaSchemaFactory
        );
    }

    public static function structuralFieldTypesAreNotAllowedInFlexFormDataProvider(): iterable
    {
        yield 'Invalid field inside default Sheet' => [
            'contentBlocks' => [
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
                        'fields' => [
                            [
                                'identifier' => 'inline',
                                'type' => 'Collection',
                                'fields' => [
                                    [
                                        'identifier' => 'flexField',
                                        'type' => 'FlexForm',
                                        'fields' => [
                                            [
                                                'identifier' => 'foo',
                                                'type' => 'FlexForm',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'message' => 'Field type "FlexForm" with identifier "foo" is not allowed inside FlexForm in Content Block "foo/bar".',
        ];

        yield 'Invalid field inside Sheet' => [
            'contentBlocks' => [
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
                        'fields' => [
                            [
                                'identifier' => 'inline',
                                'type' => 'Collection',
                                'fields' => [
                                    [
                                        'identifier' => 'flexField',
                                        'type' => 'FlexForm',
                                        'fields' => [
                                            [
                                                'identifier' => 'aSheet',
                                                'type' => 'Sheet',
                                                'fields' => [
                                                    [
                                                        'identifier' => 'foo',
                                                        'type' => 'Text',
                                                    ],
                                                ],
                                            ],
                                            [
                                                'identifier' => 'aSheet2',
                                                'type' => 'Sheet',
                                                'fields' => [
                                                    [
                                                        'identifier' => 'paletteInFlex',
                                                        'type' => 'Palette',
                                                        'fields' => [
                                                            [
                                                                [
                                                                    'identifier' => 'bar',
                                                                    'type' => 'Text',
                                                                ],
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'message' => 'Field type "Palette" with identifier "paletteInFlex" is not allowed inside FlexForm in Content Block "foo/bar".',
        ];
    }

    #[DataProvider('structuralFieldTypesAreNotAllowedInFlexFormDataProvider')]
    #[Test]
    public function structuralFieldTypesAreNotAllowedInFlexForm(array $contentBlocks, string $message): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($message);
        $this->expectExceptionCode(1685220309);

        $fieldTypeRegistry = FieldTypeRegistryTestFactory::create();
        $fieldTypeResolver = new FieldTypeResolver($fieldTypeRegistry);
        $simpleTcaSchemaFactory = new SimpleTcaSchemaFactory(new NullFrontend('test'), $fieldTypeResolver);
        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $contentBlockCompiler = new ContentBlockCompiler();
        $loader = $this->createMock(ContentBlockLoader::class);
        $tableDefinitionCollectionFactory = new TableDefinitionCollectionFactory(new NullFrontend('test'), $contentBlockCompiler, $loader);
        $tableDefinitionCollectionFactory->createUncached(
            $contentBlockRegistry,
            $fieldTypeRegistry,
            $simpleTcaSchemaFactory
        );
    }

    public static function sectionsHaveAtLeastOneContainerExceptionIsThrownDataProvider(): iterable
    {
        yield 'Missing Container in Section' => [
            'contentBlocks' => [
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
                        'fields' => [
                            [
                                'identifier' => 'inline',
                                'type' => 'Collection',
                                'fields' => [
                                    [
                                        'identifier' => 'flexField',
                                        'type' => 'FlexForm',
                                        'fields' => [
                                            [
                                                'identifier' => 'section1',
                                                'type' => 'Section',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'message' => 'FlexForm field "flexField" has a Section "section1" without "container" defined. This is invalid, please add at least one item to "container" in Content Block "foo/bar".',
        ];
    }

    #[DataProvider('sectionsHaveAtLeastOneContainerExceptionIsThrownDataProvider')]
    #[Test]
    public function sectionsHaveAtLeastOneContainerExceptionIsThrown(array $contentBlocks, string $message): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($message);
        $this->expectExceptionCode(1686330220);

        $fieldTypeRegistry = FieldTypeRegistryTestFactory::create();
        $fieldTypeResolver = new FieldTypeResolver($fieldTypeRegistry);
        $simpleTcaSchemaFactory = new SimpleTcaSchemaFactory(new NullFrontend('test'), $fieldTypeResolver);
        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $contentBlockCompiler = new ContentBlockCompiler();
        $loader = $this->createMock(ContentBlockLoader::class);
        $tableDefinitionCollectionFactory = new TableDefinitionCollectionFactory(new NullFrontend('test'), $contentBlockCompiler, $loader);
        $tableDefinitionCollectionFactory->createUncached(
            $contentBlockRegistry,
            $fieldTypeRegistry,
            $simpleTcaSchemaFactory
        );
    }

    public static function containerHaveAtLeastOneFieldExceptionIsThrownDataProvider(): iterable
    {
        yield 'Missing Container in Section' => [
            'contentBlocks' => [
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
                        'fields' => [
                            [
                                'identifier' => 'inline',
                                'type' => 'Collection',
                                'fields' => [
                                    [
                                        'identifier' => 'flexField',
                                        'type' => 'FlexForm',
                                        'fields' => [
                                            [
                                                'identifier' => 'section1',
                                                'type' => 'Section',
                                                'container' => [
                                                    [
                                                        'identifier' => 'container1',
                                                        'fields' => [],
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'message' => 'FlexForm field "flexField" has a Container in Section "section1" without "fields" defined. This is invalid, please add at least one field to "fields" in Content Block "foo/bar".',
        ];
    }

    #[DataProvider('containerHaveAtLeastOneFieldExceptionIsThrownDataProvider')]
    #[Test]
    public function containerHaveAtLeastOneFieldExceptionIsThrown(array $contentBlocks, string $message): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($message);
        $this->expectExceptionCode(1686331469);

        $fieldTypeRegistry = FieldTypeRegistryTestFactory::create();
        $fieldTypeResolver = new FieldTypeResolver($fieldTypeRegistry);
        $simpleTcaSchemaFactory = new SimpleTcaSchemaFactory(new NullFrontend('test'), $fieldTypeResolver);
        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $contentBlockCompiler = new ContentBlockCompiler();
        $loader = $this->createMock(ContentBlockLoader::class);
        $tableDefinitionCollectionFactory = new TableDefinitionCollectionFactory(new NullFrontend('test'), $contentBlockCompiler, $loader);
        $tableDefinitionCollectionFactory->createUncached(
            $contentBlockRegistry,
            $fieldTypeRegistry,
            $simpleTcaSchemaFactory
        );
    }

    public static function containerContainsValidFieldTypeExceptionIsThrownDataProvider(): iterable
    {
        yield 'Missing Container in Section' => [
            'contentBlocks' => [
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
                        'fields' => [
                            [
                                'identifier' => 'inline',
                                'type' => 'Collection',
                                'fields' => [
                                    [
                                        'identifier' => 'flexField',
                                        'type' => 'FlexForm',
                                        'fields' => [
                                            [
                                                'identifier' => 'section1',
                                                'type' => 'Section',
                                                'container' => [
                                                    [
                                                        'identifier' => 'container1',
                                                        'fields' => [
                                                            [
                                                                'type' => 'FlexForm',
                                                                'identifier' => 'nestedFlex',
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'message' => 'FlexForm field "flexField" has an invalid field of type "FlexForm" inside of a "container" item. Please use valid field types in Content Block "foo/bar".',
        ];
    }

    #[DataProvider('containerContainsValidFieldTypeExceptionIsThrownDataProvider')]
    #[Test]
    public function containerContainsValidFieldTypeExceptionIsThrown(array $contentBlocks, string $message): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($message);
        $this->expectExceptionCode(1686330594);

        $fieldTypeRegistry = FieldTypeRegistryTestFactory::create();
        $fieldTypeResolver = new FieldTypeResolver($fieldTypeRegistry);
        $simpleTcaSchemaFactory = new SimpleTcaSchemaFactory(new NullFrontend('test'), $fieldTypeResolver);
        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $contentBlockCompiler = new ContentBlockCompiler();
        $loader = $this->createMock(ContentBlockLoader::class);
        $tableDefinitionCollectionFactory = new TableDefinitionCollectionFactory(new NullFrontend('test'), $contentBlockCompiler, $loader);
        $tableDefinitionCollectionFactory->createUncached(
            $contentBlockRegistry,
            $fieldTypeRegistry,
            $simpleTcaSchemaFactory
        );
    }

    public static function localCollectionsCanHaveTableOverriddenDataProvider(): iterable
    {
        yield 'two collections with the same identifier' => [
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
                        'typeName' => 'foo_bar',
                        'fields' => [
                            [
                                'identifier' => 'my_collection',
                                'type' => 'Collection',
                                'table' => 'my_other_table_name',
                                'fields' => [
                                    [
                                        'identifier' => 'text',
                                        'type' => 'Text',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'expectedTable' => 'my_other_table_name',
        ];
    }

    #[DataProvider('localCollectionsCanHaveTableOverriddenDataProvider')]
    #[Test]
    public function localCollectionsCanHaveTableOverridden(array $contentBlocks, string $expectedTable): void
    {
        $fieldTypeRegistry = FieldTypeRegistryTestFactory::create();
        $fieldTypeResolver = new FieldTypeResolver($fieldTypeRegistry);
        $simpleTcaSchemaFactory = new SimpleTcaSchemaFactory(new NullFrontend('test'), $fieldTypeResolver);
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

        self::assertTrue($tableDefinitionCollection->hasTable($expectedTable));
    }
}
