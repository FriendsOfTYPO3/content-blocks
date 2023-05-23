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
use TYPO3\CMS\ContentBlocks\Loader\ParsedContentBlock;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class TableDefinitionFactoryTest extends UnitTestCase
{
    public static function notUniqueIdentifiersThrowAnExceptionDataProvider(): iterable
    {
        yield 'two collections with the same identifier' => [
            'contentBlocks' => [
                [
                    'name' => 't3ce/example',
                    'icon' => '',
                    'iconProvider' => '',
                    'path' => 'EXT:example/ContentBlocks/example',
                    'yaml' => [
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

        $contentBlocks = array_map(fn (array $contentBlock) => ParsedContentBlock::fromArray($contentBlock), $contentBlocks);
        (new TableDefinitionCollectionFactory())->createFromParsedContentBlocks($contentBlocks);
    }

    public static function notUniqueIdentifiersWithinCollectionThrowAnExceptionDataProvider(): iterable
    {
        yield 'two collections with the same identifier' => [
            'contentBlocks' => [
                [
                    'name' => 't3ce/example',
                    'icon' => '',
                    'iconProvider' => '',
                    'path' => 'EXT:example/ContentBlocks/example',
                    'yaml' => [
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

        $contentBlocks = array_map(fn (array $contentBlock) => ParsedContentBlock::fromArray($contentBlock), $contentBlocks);
        (new TableDefinitionCollectionFactory())->createFromParsedContentBlocks($contentBlocks);
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
                'path' => 'EXT:example/ContentBlocks/foo',
                'yaml' => [
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

        $contentBlocks = array_map(fn (array $contentBlock) => ParsedContentBlock::fromArray($contentBlock), $contentBlocks);
        (new TableDefinitionCollectionFactory())->createFromParsedContentBlocks($contentBlocks);
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
                'path' => 'EXT:example/ContentBlocks/foo',
                'yaml' => [
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

        $contentBlocks = array_map(fn (array $contentBlock) => ParsedContentBlock::fromArray($contentBlock), $contentBlocks);
        (new TableDefinitionCollectionFactory())->createFromParsedContentBlocks($contentBlocks);
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
                'path' => 'EXT:example/ContentBlocks/foo',
                'yaml' => [
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

        $contentBlocks = array_map(fn (array $contentBlock) => ParsedContentBlock::fromArray($contentBlock), $contentBlocks);
        (new TableDefinitionCollectionFactory())->createFromParsedContentBlocks($contentBlocks);
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
                'path' => 'EXT:example/ContentBlocks/foo',
                'yaml' => [
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

        $contentBlocks = array_map(fn (array $contentBlock) => ParsedContentBlock::fromArray($contentBlock), $contentBlocks);
        (new TableDefinitionCollectionFactory())->createFromParsedContentBlocks($contentBlocks);
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
                'path' => 'EXT:example/ContentBlocks/foo',
                'yaml' => [
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

        $contentBlocks = array_map(fn (array $contentBlock) => ParsedContentBlock::fromArray($contentBlock), $contentBlocks);
        (new TableDefinitionCollectionFactory())->createFromParsedContentBlocks($contentBlocks);
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
                'path' => 'EXT:example/ContentBlocks/foo',
                'yaml' => [
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

        $contentBlocks = array_map(fn (array $contentBlock) => ParsedContentBlock::fromArray($contentBlock), $contentBlocks);
        (new TableDefinitionCollectionFactory())->createFromParsedContentBlocks($contentBlocks);
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
                'path' => 'EXT:example/ContentBlocks/foo',
                'yaml' => [
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

        $contentBlocks = array_map(fn (array $contentBlock) => ParsedContentBlock::fromArray($contentBlock), $contentBlocks);
        (new TableDefinitionCollectionFactory())->createFromParsedContentBlocks($contentBlocks);
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
                'path' => 'EXT:example/ContentBlocks/foo',
                'yaml' => [
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

        $contentBlocks = array_map(fn (array $contentBlock) => ParsedContentBlock::fromArray($contentBlock), $contentBlocks);
        (new TableDefinitionCollectionFactory())->createFromParsedContentBlocks($contentBlocks);
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
                'path' => 'EXT:example/ContentBlocks/foo',
                'yaml' => [
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

        $contentBlocks = array_map(fn (array $contentBlock) => ParsedContentBlock::fromArray($contentBlock), $contentBlocks);
        (new TableDefinitionCollectionFactory())->createFromParsedContentBlocks($contentBlocks);
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
                'path' => 'EXT:example/ContentBlocks/foo',
                'yaml' => [
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

        $contentBlocks = array_map(fn (array $contentBlock) => ParsedContentBlock::fromArray($contentBlock), $contentBlocks);
        (new TableDefinitionCollectionFactory())->createFromParsedContentBlocks($contentBlocks);
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
                'path' => 'EXT:example/ContentBlocks/foo',
                'yaml' => [
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

        $contentBlocks = array_map(fn (array $contentBlock) => ParsedContentBlock::fromArray($contentBlock), $contentBlocks);
        (new TableDefinitionCollectionFactory())->createFromParsedContentBlocks($contentBlocks);
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
                'path' => 'EXT:example/ContentBlocks/foo',
                'yaml' => [
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

        $contentBlocks = array_map(fn (array $contentBlock) => ParsedContentBlock::fromArray($contentBlock), $contentBlocks);
        (new TableDefinitionCollectionFactory())->createFromParsedContentBlocks($contentBlocks);
    }
}
