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

namespace TYPO3\CMS\ContentBlocks\Tests\Unit\Generator;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\ContentBlocks\Definition\Factory\ContentBlockCompiler;
use TYPO3\CMS\ContentBlocks\Definition\Factory\TableDefinitionCollectionFactory;
use TYPO3\CMS\ContentBlocks\Generator\SqlGenerator;
use TYPO3\CMS\ContentBlocks\Loader\ContentBlockLoader;
use TYPO3\CMS\ContentBlocks\Loader\LoadedContentBlock;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\Core\Cache\Frontend\NullFrontend;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class SqlGeneratorTest extends UnitTestCase
{
    public static function generateReturnsExpectedSqlStatementsDataProvider(): iterable
    {
        yield 'simple fields in tt_content table' => [
            'array' => [
                [
                    'name' => 'foo/bar',
                    'yaml' => [
                        'table' => 'tt_content',
                        'typeField' => 'CType',
                        'fields' => [
                            [
                                'identifier' => 'text',
                                'type' => 'Text',
                            ],
                            [
                                'identifier' => 'number',
                                'type' => 'Number',
                            ],
                        ],
                    ],
                ],
            ],
            'expected' => [
                "CREATE TABLE `tt_content`(`foo_bar_text` VARCHAR(255) DEFAULT '' NOT NULL);",
                "CREATE TABLE `tt_content`(`foo_bar_number` int(11) DEFAULT '0' NOT NULL);",
            ],
        ];

        yield 'simple fields in custom foobar table' => [
            'array' => [
                [
                    'name' => 'foo/bar',
                    'yaml' => [
                        'table' => 'foobar',
                        'fields' => [
                            [
                                'identifier' => 'text',
                                'type' => 'Text',
                            ],
                            [
                                'identifier' => 'number',
                                'type' => 'Number',
                            ],
                        ],
                    ],
                ],
            ],
            'expected' => [
                "CREATE TABLE `foobar`(`foo_bar_text` VARCHAR(255) DEFAULT '' NOT NULL);",
                "CREATE TABLE `foobar`(`foo_bar_number` int(11) DEFAULT '0' NOT NULL);",
            ],
        ];

        yield 'simple fields in custom foobar table with parent reference' => [
            'array' => [
                [
                    'name' => 'foo/parent',
                    'yaml' => [
                        'table' => 'tt_content',
                        'fields' => [
                            [
                                'identifier' => 'collection',
                                'type' => 'Collection',
                                'foreign_table' => 'foobar',
                                'shareAcrossTables' => true,
                                'shareAcrossFields' => true,
                            ],
                        ],
                    ],
                ],
                [
                    'name' => 'foo/bar',
                    'yaml' => [
                        'table' => 'foobar',
                        'fields' => [
                            [
                                'identifier' => 'text',
                                'type' => 'Text',
                            ],
                            [
                                'identifier' => 'number',
                                'type' => 'Number',
                            ],
                        ],
                    ],
                ],
            ],
            'expected' => [
                "CREATE TABLE `tt_content`(`foo_parent_collection` int(11) UNSIGNED DEFAULT '0' NOT NULL);",
                "CREATE TABLE `foobar`(`foo_bar_text` VARCHAR(255) DEFAULT '' NOT NULL);",
                "CREATE TABLE `foobar`(`foo_bar_number` int(11) DEFAULT '0' NOT NULL);",
                "CREATE TABLE `foobar`(`foreign_table_parent_uid` int(11) DEFAULT '0' NOT NULL, KEY parent_uid (foreign_table_parent_uid));",
                "CREATE TABLE `foobar`(`tablenames` varchar(255) DEFAULT '' NOT NULL);",
                "CREATE TABLE `foobar`(`fieldname` varchar(255) DEFAULT '' NOT NULL);",
            ],
        ];

        yield 'simple fields in custom foobar table with typeField defined' => [
            'array' => [
                [
                    'name' => 'foo/bar',
                    'yaml' => [
                        'table' => 'foobar',
                        'typeField' => 'my_type',
                        'typeName' => 'foo',
                        'fields' => [
                            [
                                'identifier' => 'text',
                                'type' => 'Text',
                            ],
                            [
                                'identifier' => 'number',
                                'type' => 'Number',
                            ],
                        ],
                    ],
                ],
            ],
            'expected' => [
                "CREATE TABLE `foobar`(`my_type` VARCHAR(255) DEFAULT '' NOT NULL);",
                "CREATE TABLE `foobar`(`foo_bar_text` VARCHAR(255) DEFAULT '' NOT NULL);",
                "CREATE TABLE `foobar`(`foo_bar_number` int(11) DEFAULT '0' NOT NULL);",
            ],
        ];

        yield 'nullable option removes NOT NULL statement' => [
            'array' => [
                [
                    'name' => 'foo/bar',
                    'yaml' => [
                        'table' => 'tt_content',
                        'typeField' => 'CType',
                        'fields' => [
                            [
                                'identifier' => 'text',
                                'type' => 'Text',
                            ],
                            [
                                'identifier' => 'number',
                                'type' => 'Number',
                                'nullable' => true,
                            ],
                        ],
                    ],
                ],
            ],
            'expected' => [
                "CREATE TABLE `tt_content`(`foo_bar_text` VARCHAR(255) DEFAULT '' NOT NULL);",
                "CREATE TABLE `tt_content`(`foo_bar_number` int(11) DEFAULT '0');",
            ],
        ];

        yield 'inline field on root level' => [
            'array' => [
                [
                    'name' => 'foo/bar',
                    'yaml' => [
                        'table' => 'tt_content',
                        'typeField' => 'CType',
                        'fields' => [
                            [
                                'identifier' => 'text',
                                'type' => 'Text',
                            ],
                            [
                                'identifier' => 'collection',
                                'type' => 'Collection',
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
            'expected' => [
                "CREATE TABLE `foo_bar_collection`(`text` VARCHAR(255) DEFAULT '' NOT NULL);",
                "CREATE TABLE `foo_bar_collection`(`foreign_table_parent_uid` int(11) DEFAULT '0' NOT NULL, KEY parent_uid (foreign_table_parent_uid));",
                "CREATE TABLE `tt_content`(`foo_bar_text` VARCHAR(255) DEFAULT '' NOT NULL);",
                "CREATE TABLE `tt_content`(`foo_bar_collection` int(11) UNSIGNED DEFAULT '0' NOT NULL);",
            ],
        ];

        yield 'inline field on second level' => [
            'array' => [
                [
                    'name' => 'foo/bar',
                    'yaml' => [
                        'table' => 'tt_content',
                        'typeField' => 'CType',
                        'prefixFields' => true,
                        'fields' => [
                            [
                                'identifier' => 'text',
                                'type' => 'Text',
                            ],
                            [
                                'identifier' => 'catgeory',
                                'type' => 'Category',
                            ],
                            [
                                'identifier' => 'collection',
                                'type' => 'Collection',
                                'fields' => [
                                    [
                                        'identifier' => 'text',
                                        'type' => 'Text',
                                    ],
                                    [
                                        'identifier' => 'collection2',
                                        'type' => 'Collection',
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
                ],
            ],
            'expected' => [
                "CREATE TABLE `collection2`(`text` VARCHAR(255) DEFAULT '' NOT NULL);",
                "CREATE TABLE `collection2`(`foreign_table_parent_uid` int(11) DEFAULT '0' NOT NULL, KEY parent_uid (foreign_table_parent_uid));",
                "CREATE TABLE `foo_bar_collection`(`text` VARCHAR(255) DEFAULT '' NOT NULL);",
                "CREATE TABLE `foo_bar_collection`(`collection2` int(11) UNSIGNED DEFAULT '0' NOT NULL);",
                "CREATE TABLE `foo_bar_collection`(`foreign_table_parent_uid` int(11) DEFAULT '0' NOT NULL, KEY parent_uid (foreign_table_parent_uid));",
                "CREATE TABLE `tt_content`(`foo_bar_text` VARCHAR(255) DEFAULT '' NOT NULL);",
                "CREATE TABLE `tt_content`(`foo_bar_collection` int(11) UNSIGNED DEFAULT '0' NOT NULL);",
            ],
        ];
    }

    #[DataProvider('generateReturnsExpectedSqlStatementsDataProvider')]
    #[Test]
    public function generateReturnsExpectedSqlStatements(array $contentBlocks, array $expected): void
    {
        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $loader = $this->createMock(ContentBlockLoader::class);
        $loader->method('loadUncached')->willReturn($contentBlockRegistry);
        $contentBlockCompiler = new ContentBlockCompiler();
        $tableDefinitionCollectionFactory = (new TableDefinitionCollectionFactory(new NullFrontend('test'), $contentBlockCompiler));
        $sqlGenerator = new SqlGenerator($loader, $tableDefinitionCollectionFactory);

        $result = $sqlGenerator->generate();

        self::assertSame($expected, $result);
    }
}
