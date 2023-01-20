<?php

declare(strict_types=1);

namespace TYPO3\CMS\ContentBlocks\Tests\Unit\Generator;

use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\ContentBlocks\Generator\SqlGenerator;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class SqlGeneratorTest extends UnitTestCase
{
    public function generateReturnsExpectedSqlStatementsDataProvider(): iterable
    {
        yield 'simple fields in tt_content table' => [
            'array' => [
                [
                    'composerJson' => [
                        'name' => 'foo/bar',
                    ],
                    'yaml' => [
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

        yield 'inline field on root level' => [
            'array' => [
                [
                    'composerJson' => [
                        'name' => 'foo/bar',
                    ],
                    'yaml' => [
                        'fields' => [
                            [
                                'identifier' => 'text',
                                'type' => 'Text',
                            ],
                            [
                                'identifier' => 'collection',
                                'type' => 'Collection',
                                'properties' => [
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
            'expected' => [
                "CREATE TABLE `foo_bar_collection`(`text` VARCHAR(255) DEFAULT '' NOT NULL);",
                "CREATE TABLE `foo_bar_collection`(`foreign_table_parent_uid` int(11) DEFAULT '0' NOT NULL);",
                "CREATE TABLE `tt_content`(`foo_bar_text` VARCHAR(255) DEFAULT '' NOT NULL);",
                "CREATE TABLE `tt_content`(`foo_bar_collection` int(11) DEFAULT '0' NOT NULL);",
            ],
        ];

        yield 'inline field on second level' => [
            'array' => [
                [
                    'composerJson' => [
                        'name' => 'foo/bar',
                    ],
                    'yaml' => [
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
                                'properties' => [
                                    'fields' => [
                                        [
                                            'identifier' => 'text',
                                            'type' => 'Text',
                                        ],
                                        [
                                            'identifier' => 'collection2',
                                            'type' => 'Collection',
                                            'properties' => [
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
                ],
            ],
            'expected' => [
                "CREATE TABLE `foo_bar_collection2`(`text` VARCHAR(255) DEFAULT '' NOT NULL);",
                "CREATE TABLE `foo_bar_collection2`(`foreign_table_parent_uid` int(11) DEFAULT '0' NOT NULL);",
                "CREATE TABLE `foo_bar_collection`(`text` VARCHAR(255) DEFAULT '' NOT NULL);",
                "CREATE TABLE `foo_bar_collection`(`collection2` int(11) DEFAULT '0' NOT NULL);",
                "CREATE TABLE `foo_bar_collection`(`foreign_table_parent_uid` int(11) DEFAULT '0' NOT NULL);",
                "CREATE TABLE `tt_content`(`foo_bar_text` VARCHAR(255) DEFAULT '' NOT NULL);",
                "CREATE TABLE `tt_content`(`foo_bar_collection` int(11) DEFAULT '0' NOT NULL);",
            ],
        ];
    }

    /**
     * @dataProvider generateReturnsExpectedSqlStatementsDataProvider
     * @test
     */
    public function generateReturnsExpectedSqlStatements(array $array, array $expected): void
    {
        $tableDefinitionCollection = TableDefinitionCollection::createFromArray($array);
        $sqlGenerator = new SqlGenerator($tableDefinitionCollection);

        $result = $sqlGenerator->generate();

        self::assertSame($expected, $result);
    }
}
