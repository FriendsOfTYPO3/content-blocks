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

use TYPO3\CMS\ContentBlocks\Definition\Factory\TableDefinitionCollectionFactory;
use TYPO3\CMS\ContentBlocks\Loader\LoadedContentBlock;
use TYPO3\CMS\ContentBlocks\Registry\AutomaticLanguageKeysRegistry;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class TableDefinitionCollectionFactoryTest extends UnitTestCase
{
    public static function notUniqueIdentifiersThrowAnExceptionDataProvider(): iterable
    {
        yield 'two collections with the same identifier' => [
            'contentBlocks' => [
                [
                    'name' => 't3ce/example',
                    'icon' => '',
                    'iconProvider' => '',
                    'extPath' => 'EXT:example/ContentBlocks/example',
                    'yaml' => [
                        'table' => 'tt_content',
                        'typeField' => 'CType',
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

    /**
     * @dataProvider notUniqueIdentifiersThrowAnExceptionDataProvider
     * @test
     */
    public function notUniqueIdentifiersThrowAnException(array $contentBlocks): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1677407942);
        $this->expectExceptionMessage('The identifier "foo" in content block "t3ce/example" does exist more than once. Please choose unique identifiers.');

        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $automaticLanguageKeyRegistry = new AutomaticLanguageKeysRegistry();
        (new TableDefinitionCollectionFactory($automaticLanguageKeyRegistry))->createFromLoadedContentBlocks($contentBlockRegistry);
    }

    public static function notUniqueIdentifiersWithinCollectionThrowAnExceptionDataProvider(): iterable
    {
        yield 'two collections with the same identifier' => [
            'contentBlocks' => [
                [
                    'name' => 't3ce/example',
                    'icon' => '',
                    'iconProvider' => '',
                    'extPath' => 'EXT:example/ContentBlocks/example',
                    'yaml' => [
                        'table' => 'tt_content',
                        'typeField' => 'CType',
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

    /**
     * @dataProvider notUniqueIdentifiersWithinCollectionThrowAnExceptionDataProvider
     * @test
     */
    public function notUniqueIdentifiersWithinCollectionThrowAnException(array $contentBlocks): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1677407942);
        $this->expectExceptionMessage('The identifier "foo" in content block "t3ce/example" does exist more than once. Please choose unique identifiers.');

        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $automaticLanguageKeyRegistry = new AutomaticLanguageKeysRegistry();
        (new TableDefinitionCollectionFactory($automaticLanguageKeyRegistry))->createFromLoadedContentBlocks($contentBlockRegistry);
    }

    /**
     * @test
     */
    public function paletteInsidePaletteIsNotAllowed(): void
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
        $this->expectExceptionMessage('Palette "palette_inside_palette" is not allowed inside palette "palette_1" in content block "foo/bar".');

        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $automaticLanguageKeyRegistry = new AutomaticLanguageKeysRegistry();
        (new TableDefinitionCollectionFactory($automaticLanguageKeyRegistry))->createFromLoadedContentBlocks($contentBlockRegistry);
    }

    /**
     * @test
     */
    public function paletteInsidePaletteInsideCollectionIsNotAllowed(): void
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
        $this->expectExceptionMessage('Palette "palette_inside_palette" is not allowed inside palette "palette_1" in content block "foo/bar".');

        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $automaticLanguageKeyRegistry = new AutomaticLanguageKeysRegistry();
        (new TableDefinitionCollectionFactory($automaticLanguageKeyRegistry))->createFromLoadedContentBlocks($contentBlockRegistry);
    }

    /**
     * @test
     */
    public function paletteWithSameIdentifierIsNotAllowed(): void
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
        $this->expectExceptionMessage('The palette identifier "palette_1" in content block "foo/bar" does exist more than once. Please choose unique identifiers.');

        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $automaticLanguageKeyRegistry = new AutomaticLanguageKeysRegistry();
        (new TableDefinitionCollectionFactory($automaticLanguageKeyRegistry))->createFromLoadedContentBlocks($contentBlockRegistry);
    }

    /**
     * @test
     */
    public function paletteWithSameIdentifierInsideCollectionIsNotAllowed(): void
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
        $this->expectExceptionMessage('The palette identifier "palette_1" in content block "foo/bar" does exist more than once. Please choose unique identifiers.');

        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $automaticLanguageKeyRegistry = new AutomaticLanguageKeysRegistry();
        (new TableDefinitionCollectionFactory($automaticLanguageKeyRegistry))->createFromLoadedContentBlocks($contentBlockRegistry);
    }

    /**
     * @test
     */
    public function tabWithSameIdentifierIsNotAllowed(): void
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
        $this->expectExceptionMessage('The tab identifier "tab_1" in content block "foo/bar" does exist more than once. Please choose unique identifiers.');

        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $automaticLanguageKeyRegistry = new AutomaticLanguageKeysRegistry();
        (new TableDefinitionCollectionFactory($automaticLanguageKeyRegistry))->createFromLoadedContentBlocks($contentBlockRegistry);
    }

    /**
     * @test
     */
    public function tabWithSameIdentifierInsideCollectionIsNotAllowed(): void
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
        $this->expectExceptionMessage('The tab identifier "tab_1" in content block "foo/bar" does exist more than once. Please choose unique identifiers.');

        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $automaticLanguageKeyRegistry = new AutomaticLanguageKeysRegistry();
        (new TableDefinitionCollectionFactory($automaticLanguageKeyRegistry))->createFromLoadedContentBlocks($contentBlockRegistry);
    }

    /**
     * @test
     */
    public function tabInsidePaletteIsNotAllowed(): void
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
        $this->expectExceptionMessage('Tab "tab_1" is not allowed inside palette "palette_1" in content block "foo/bar".');

        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $automaticLanguageKeyRegistry = new AutomaticLanguageKeysRegistry();
        (new TableDefinitionCollectionFactory($automaticLanguageKeyRegistry))->createFromLoadedContentBlocks($contentBlockRegistry);
    }

    /**
     * @test
     */
    public function tabInsidePaletteInsideCollectionIsNotAllowed(): void
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
        $this->expectExceptionMessage('Tab "tab_1" is not allowed inside palette "palette_1" in content block "foo/bar".');

        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $automaticLanguageKeyRegistry = new AutomaticLanguageKeysRegistry();
        (new TableDefinitionCollectionFactory($automaticLanguageKeyRegistry))->createFromLoadedContentBlocks($contentBlockRegistry);
    }

    /**
     * @test
     */
    public function linebreaksAreOnlyAllowedWithinPalettes(): void
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
        $this->expectExceptionMessage('Linebreaks are only allowed within Palettes in content block "foo/bar".');

        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $automaticLanguageKeyRegistry = new AutomaticLanguageKeysRegistry();
        (new TableDefinitionCollectionFactory($automaticLanguageKeyRegistry))->createFromLoadedContentBlocks($contentBlockRegistry);
    }

    /**
     * @test
     */
    public function linebreaksAreOnlyAllowedWithinPalettesInsideCollections(): void
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
        $this->expectExceptionMessage('Linebreaks are only allowed within Palettes in content block "foo/bar".');

        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $automaticLanguageKeyRegistry = new AutomaticLanguageKeysRegistry();
        (new TableDefinitionCollectionFactory($automaticLanguageKeyRegistry))->createFromLoadedContentBlocks($contentBlockRegistry);
    }

    /**
     * @test
     */
    public function identifierIsRequired(): void
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
        $this->expectExceptionMessage('A field is missing the required "identifier" in content block "foo/bar".');

        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $automaticLanguageKeyRegistry = new AutomaticLanguageKeysRegistry();
        (new TableDefinitionCollectionFactory($automaticLanguageKeyRegistry))->createFromLoadedContentBlocks($contentBlockRegistry);
    }

    /**
     * @test
     */
    public function typeIsRequired(): void
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
        $this->expectExceptionMessage('The field "text1" is missing the required "type" in content block "foo/bar".');

        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $automaticLanguageKeyRegistry = new AutomaticLanguageKeysRegistry();
        (new TableDefinitionCollectionFactory($automaticLanguageKeyRegistry))->createFromLoadedContentBlocks($contentBlockRegistry);
    }

    /**
     * @test
     */
    public function identifierIsRequiredInsideCollections(): void
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
        $this->expectExceptionMessage('A field is missing the required "identifier" in content block "foo/bar".');

        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $automaticLanguageKeyRegistry = new AutomaticLanguageKeysRegistry();
        (new TableDefinitionCollectionFactory($automaticLanguageKeyRegistry))->createFromLoadedContentBlocks($contentBlockRegistry);
    }

    /**
     * @test
     */
    public function flexFieldIsNotAllowedToMixNonSheetAndSheet(): void
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
        $this->expectExceptionMessage('You must not mix Sheets with normal fields inside the FlexForm definition "flexField" in content block "foo/bar".');
        $this->expectExceptionCode(1685217163);

        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $automaticLanguageKeyRegistry = new AutomaticLanguageKeysRegistry();
        (new TableDefinitionCollectionFactory($automaticLanguageKeyRegistry))->createFromLoadedContentBlocks($contentBlockRegistry);
    }

    public static function structuralFieldTypesAreNotAllowedInFlexFormDataProvider(): iterable
    {
        yield 'Invalid field inside default Sheet' => [
            'contentBlocks' => [
                [
                    'name' => 'foo/bar',
                    'icon' => '',
                    'iconProvider' => '',
                    'extPath' => 'EXT:example/ContentBlocks/foo',
                    'yaml' => [
                        'table' => 'tt_content',
                        'typeField' => 'CType',
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
                    'icon' => '',
                    'iconProvider' => '',
                    'extPath' => 'EXT:example/ContentBlocks/foo',
                    'yaml' => [
                        'table' => 'tt_content',
                        'typeField' => 'CType',
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

    /**
     * @test
     * @dataProvider structuralFieldTypesAreNotAllowedInFlexFormDataProvider
     */
    public function structuralFieldTypesAreNotAllowedInFlexForm(array $contentBlocks, string $message): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($message);
        $this->expectExceptionCode(1685220309);

        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $automaticLanguageKeyRegistry = new AutomaticLanguageKeysRegistry();
        (new TableDefinitionCollectionFactory($automaticLanguageKeyRegistry))->createFromLoadedContentBlocks($contentBlockRegistry);
    }

    public static function sectionsHaveAtLeastOneContainerExceptionIsThrownDataProvider(): iterable
    {
        yield 'Missing Container in Section' => [
            'contentBlocks' => [
                [
                    'name' => 'foo/bar',
                    'icon' => '',
                    'iconProvider' => '',
                    'extPath' => 'EXT:example/ContentBlocks/foo',
                    'yaml' => [
                        'table' => 'tt_content',
                        'typeField' => 'CType',
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

    /**
     * @test
     * @dataProvider sectionsHaveAtLeastOneContainerExceptionIsThrownDataProvider
     */
    public function sectionsHaveAtLeastOneContainerExceptionIsThrown(array $contentBlocks, string $message): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($message);
        $this->expectExceptionCode(1686330220);

        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $automaticLanguageKeyRegistry = new AutomaticLanguageKeysRegistry();
        (new TableDefinitionCollectionFactory($automaticLanguageKeyRegistry))->createFromLoadedContentBlocks($contentBlockRegistry);
    }

    public static function containerHaveAtLeastOneFieldExceptionIsThrownDataProvider(): iterable
    {
        yield 'Missing Container in Section' => [
            'contentBlocks' => [
                [
                    'name' => 'foo/bar',
                    'icon' => '',
                    'iconProvider' => '',
                    'extPath' => 'EXT:example/ContentBlocks/foo',
                    'yaml' => [
                        'table' => 'tt_content',
                        'typeField' => 'CType',
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

    /**
     * @test
     * @dataProvider containerHaveAtLeastOneFieldExceptionIsThrownDataProvider
     */
    public function containerHaveAtLeastOneFieldExceptionIsThrown(array $contentBlocks, string $message): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($message);
        $this->expectExceptionCode(1686331469);

        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $automaticLanguageKeyRegistry = new AutomaticLanguageKeysRegistry();
        (new TableDefinitionCollectionFactory($automaticLanguageKeyRegistry))->createFromLoadedContentBlocks($contentBlockRegistry);
    }

    public static function containerContainsValidFieldTypeExceptionIsThrownDataProvider(): iterable
    {
        yield 'Missing Container in Section' => [
            'contentBlocks' => [
                [
                    'name' => 'foo/bar',
                    'icon' => '',
                    'iconProvider' => '',
                    'extPath' => 'EXT:example/ContentBlocks/foo',
                    'yaml' => [
                        'table' => 'tt_content',
                        'typeField' => 'CType',
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

    /**
     * @test
     * @dataProvider containerContainsValidFieldTypeExceptionIsThrownDataProvider
     */
    public function containerContainsValidFieldTypeExceptionIsThrown(array $contentBlocks, string $message): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($message);
        $this->expectExceptionCode(1686330594);

        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $automaticLanguageKeyRegistry = new AutomaticLanguageKeysRegistry();
        (new TableDefinitionCollectionFactory($automaticLanguageKeyRegistry))->createFromLoadedContentBlocks($contentBlockRegistry);
    }

    public static function localCollectionsCanHaveTableOverriddenDataProvider(): iterable
    {
        yield 'two collections with the same identifier' => [
            'contentBlocks' => [
                [
                    'name' => 't3ce/example',
                    'icon' => '',
                    'iconProvider' => '',
                    'extPath' => 'EXT:example/ContentBlocks/example',
                    'yaml' => [
                        'table' => 'tt_content',
                        'typeField' => 'CType',
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

    /**
     * @test
     * @dataProvider localCollectionsCanHaveTableOverriddenDataProvider
     */
    public function localCollectionsCanHaveTableOverridden(array $contentBlocks, string $expectedTable): void
    {
        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $automaticLanguageKeyRegistry = new AutomaticLanguageKeysRegistry();
        $tableDefinitionCollection = (new TableDefinitionCollectionFactory($automaticLanguageKeyRegistry))
            ->createFromLoadedContentBlocks($contentBlockRegistry);

        self::assertTrue($tableDefinitionCollection->hasTable($expectedTable));
    }
}
