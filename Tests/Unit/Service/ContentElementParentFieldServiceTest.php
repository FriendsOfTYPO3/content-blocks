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

namespace TYPO3\CMS\ContentBlocks\Tests\Unit\Service;

use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\DependencyInjection\Container;
use TYPO3\CMS\ContentBlocks\Definition\Factory\ContentBlockCompiler;
use TYPO3\CMS\ContentBlocks\Definition\Factory\TableDefinitionCollectionFactory;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\ContentBlocks\Loader\LoadedContentBlock;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\ContentBlocks\Schema\FieldTypeResolver;
use TYPO3\CMS\ContentBlocks\Schema\SimpleTcaSchemaFactory;
use TYPO3\CMS\ContentBlocks\ServiceProvider;
use TYPO3\CMS\ContentBlocks\Tests\Unit\Fixtures\FieldTypeRegistryTestFactory;
use TYPO3\CMS\Core\Cache\Frontend\NullFrontend;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class ContentElementParentFieldServiceTest extends UnitTestCase
{
    #[Test]
    public function uniqueForeignFieldAreCollectedForTtContent(): void
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
                            'identifier' => 'nested_content',
                            'type' => 'Collection',
                            'foreign_table' => 'tt_content',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'foo/baz',
                'icon' => [
                    'iconPath' => '',
                    'iconProvider' => '',
                ],
                'extPath' => 'EXT:example/ContentBlocks/baz',
                'yaml' => [
                    'table' => 'foobar',
                    'typeField' => 'CType',
                    'typeName' => 'foo_baz',
                    'fields' => [
                        [
                            'identifier' => 'nested_content',
                            'type' => 'Collection',
                            'foreign_table' => 'tt_content',
                        ],
                    ],
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
                    'fields' => [
                        [
                            'identifier' => 'nested_content2',
                            'type' => 'Collection',
                            'foreign_table' => 'tt_content',
                            'foreign_field' => 'alternative_foreign_field',
                        ],
                    ],
                ],
            ],
        ];

        $expected = [
            'foreign_table_parent_uid',
            'alternative_foreign_field',
        ];

        $fieldTypeRegistry = FieldTypeRegistryTestFactory::create();
        $fieldTypeResolver = new FieldTypeResolver($fieldTypeRegistry);
        $simpleTcaSchemaFactory = new SimpleTcaSchemaFactory($fieldTypeResolver);
        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $contentBlockCompiler = new ContentBlockCompiler();
        $tableDefinitionCollection = (new TableDefinitionCollectionFactory(new NullFrontend('test'), $contentBlockCompiler))
            ->createUncached($contentBlockRegistry, $fieldTypeRegistry, $simpleTcaSchemaFactory);
        $container = new Container();
        $container->set(TableDefinitionCollection::class, $tableDefinitionCollection);
        $container->set('cache.content_blocks_code', new NullFrontend('test'));
        $result = ServiceProvider::getContentBlockParentFieldNames($container);

        self::assertSame($expected, $result->getArrayCopy());
    }

    #[Test]
    public function emptyResultIfNoContentElementDefinition(): void
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
                    'table' => 'foobar',
                    'typeField' => 'CType',
                    'typeName' => 'foo_bar',
                    'fields' => [
                        [
                            'identifier' => 'nested_content',
                            'type' => 'Collection',
                            'foreign_table' => 'tt_content',
                        ],
                    ],
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
                    'table' => 'foobar',
                    'typeField' => 'CType',
                    'typeName' => 't3ce_example',
                    'fields' => [
                        [
                            'identifier' => 'nested_content2',
                            'type' => 'Collection',
                            'foreign_table' => 'tt_content',
                            'foreign_field' => 'alternative_foreign_field',
                        ],
                    ],
                ],
            ],
        ];

        $expected = [];

        $fieldTypeRegistry = FieldTypeRegistryTestFactory::create();
        $fieldTypeResolver = new FieldTypeResolver($fieldTypeRegistry);
        $simpleTcaSchemaFactory = new SimpleTcaSchemaFactory($fieldTypeResolver);
        $contentBlockRegistry = new ContentBlockRegistry();
        foreach ($contentBlocks as $contentBlock) {
            $contentBlockRegistry->register(LoadedContentBlock::fromArray($contentBlock));
        }
        $contentBlockCompiler = new ContentBlockCompiler();
        $tableDefinitionCollection = (new TableDefinitionCollectionFactory(new NullFrontend('test'), $contentBlockCompiler))
            ->createUncached($contentBlockRegistry, $fieldTypeRegistry, $simpleTcaSchemaFactory);
        $container = new Container();
        $container->set(TableDefinitionCollection::class, $tableDefinitionCollection);
        $container->set('cache.content_blocks_code', new NullFrontend('test'));
        $result = ServiceProvider::getContentBlockParentFieldNames($container);

        self::assertSame($expected, $result->getArrayCopy());
    }
}
