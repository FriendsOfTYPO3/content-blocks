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
use TYPO3\CMS\ContentBlocks\FieldType\BaseFieldTypeRegistryFactory;
use TYPO3\CMS\ContentBlocks\Generator\SqlGenerator;
use TYPO3\CMS\ContentBlocks\Loader\ContentBlockLoader;
use TYPO3\CMS\ContentBlocks\Loader\LoadedContentBlock;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\ContentBlocks\Schema\FieldTypeResolver;
use TYPO3\CMS\ContentBlocks\Schema\SimpleTcaSchemaFactory;
use TYPO3\CMS\ContentBlocks\Tests\Unit\Fixtures\FieldTypeRegistryTestFactory;
use TYPO3\CMS\Core\Cache\Frontend\NullFrontend;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class SqlGeneratorTest extends UnitTestCase
{
    public static function generateReturnsExpectedSqlStatementsDataProvider(): iterable
    {
        yield 'simple fields in custom foobar table with parent reference' => [
            'contentBlocks' => [
                [
                    'name' => 'foo/parent',
                    'yaml' => [
                        'table' => 'tt_content',
                        'typeName' => 'foo_parent',
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
                        'typeName' => 'foo_bar',
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
                "CREATE TABLE `foobar` (`fieldname` varchar(255) DEFAULT '' NOT NULL);",
                'CREATE TABLE `foobar` (KEY parent_uid (tablenames, fieldname, foreign_table_parent_uid));',
            ],
        ];

        yield 'two different parents with different parent reference field names' => [
            'contentBlocks' => [
                [
                    'name' => 'foo/parent',
                    'yaml' => [
                        'table' => 'table1',
                        'prefixFields' => false,
                        'fields' => [
                            [
                                'identifier' => 'collection',
                                'type' => 'Collection',
                                'foreign_table' => 'foobar',
                                'foreign_field' => 'foreign_field_1',
                            ],
                        ],
                    ],
                ],
                [
                    'name' => 'foo/parent2',
                    'yaml' => [
                        'table' => 'table2',
                        'prefixFields' => false,
                        'fields' => [
                            [
                                'identifier' => 'collection',
                                'type' => 'Collection',
                                'foreign_table' => 'foobar',
                                'foreign_field' => 'foreign_field_2',
                            ],
                        ],
                    ],
                ],
                [
                    'name' => 'foo/bar',
                    'yaml' => [
                        'table' => 'foobar',
                        'typeName' => 'foo_bar',
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
                'CREATE TABLE `foobar` (KEY parent_uid (foreign_field_1));',
                'CREATE TABLE `foobar` (KEY parent_uid_2 (foreign_field_2));',
            ],
        ];

        yield 'three fields in custom foobar table with parent reference' => [
            'contentBlocks' => [
                [
                    'name' => 'foo/parent',
                    'yaml' => [
                        'table' => 'tt_content',
                        'typeName' => 'foo_parent',
                        'fields' => [
                            [
                                'identifier' => 'collection',
                                'type' => 'Collection',
                                'foreign_table' => 'foobar',
                                'shareAcrossTables' => true,
                                'shareAcrossFields' => true,
                            ],
                            [
                                'identifier' => 'collection3',
                                'type' => 'Collection',
                                'foreign_table' => 'foobar',
                                'shareAcrossTables' => true,
                                'shareAcrossFields' => true,
                            ],
                            [
                                'identifier' => 'collection2',
                                'type' => 'Collection',
                                'foreign_table' => 'foobar',
                                'foreign_field' => 'alternative_foreign_field',
                                'foreign_table_field' => 'alternative_foreign_table_field',
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
                        'typeName' => 'foo_bar',
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
                "CREATE TABLE `foobar` (`fieldname` varchar(255) DEFAULT '' NOT NULL);",
                'CREATE TABLE `foobar` (KEY parent_uid (tablenames, fieldname, foreign_table_parent_uid));',
                'CREATE TABLE `foobar` (KEY parent_uid_2 (alternative_foreign_table_field, fieldname, alternative_foreign_field));',
            ],
        ];

        yield 'inline field on root level' => [
            'contentBlocks' => [
                [
                    'name' => 'foo/bar',
                    'yaml' => [
                        'table' => 'tt_content',
                        'typeField' => 'CType',
                        'typeName' => 'foo_bar',
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
                'CREATE TABLE `foo_bar_collection` (KEY parent_uid (foreign_table_parent_uid));',
            ],
        ];

        yield 'inline field on second level' => [
            'contentBlocks' => [
                [
                    'name' => 'foo/bar',
                    'yaml' => [
                        'table' => 'tt_content',
                        'typeField' => 'CType',
                        'typeName' => 'foo_bar',
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
                'CREATE TABLE `collection2` (KEY parent_uid (foreign_table_parent_uid));',
                'CREATE TABLE `foo_bar_collection` (KEY parent_uid (foreign_table_parent_uid));',
            ],
        ];
    }

    #[DataProvider('generateReturnsExpectedSqlStatementsDataProvider')]
    #[Test]
    public function generateReturnsExpectedSqlStatements(array $contentBlocks, array $expected): void
    {
        $fieldTypeRegistry = FieldTypeRegistryTestFactory::create();
        $baseFieldTypeRegistry = new BaseFieldTypeRegistryFactory($fieldTypeRegistry);
        $fieldTypeResolver = new FieldTypeResolver($baseFieldTypeRegistry->create());
        $packageManager = $this->createMock(PackageManager::class);
        $packageManager->method('getActivePackages')->willReturn([]);
        $simpleTcaSchemaFactory = new SimpleTcaSchemaFactory(new NullFrontend('test'), $fieldTypeResolver, $packageManager);
        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $loader = $this->createMock(ContentBlockLoader::class);
        $loader->method('loadUncached')->willReturn($contentBlockRegistry);
        $contentBlockCompiler = new ContentBlockCompiler();
        $tableDefinitionCollectionFactory = (new TableDefinitionCollectionFactory(new NullFrontend('test'), $contentBlockCompiler, $loader));
        $sqlGenerator = new SqlGenerator($loader, $tableDefinitionCollectionFactory, $simpleTcaSchemaFactory, $fieldTypeRegistry);

        $result = $sqlGenerator->generate();

        self::assertSame($expected, $result);
    }
}
