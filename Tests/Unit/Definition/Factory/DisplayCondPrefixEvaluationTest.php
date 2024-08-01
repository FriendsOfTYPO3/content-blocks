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
use TYPO3\CMS\ContentBlocks\Definition\Factory\PrefixType;
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

final class DisplayCondPrefixEvaluationTest extends UnitTestCase
{
    #[Test]
    public function displayCondIsPrefixedForStringSyntax(): void
    {
        $contentBlock = LoadedContentBlock::fromArray([
            'name' => 'bar/foo',
            'yaml' => [
                'table' => 'tt_content',
                'prefixFields' => true,
                'prefixType' => PrefixType::FULL->value,
                'fields' => [
                    [
                        'identifier' => 'aField',
                        'displayCond' => 'FIELD:bField:=:aValue',
                        'type' => 'Text',
                    ],
                    [
                        'identifier' => 'bField',
                        'type' => 'Text',
                    ],
                ],
            ],
        ]);

        $expected = 'FIELD:bar_foo_bField:=:aValue';

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
        $contentBlockRegistry->register($contentBlock);
        $contentBlockCompiler = new ContentBlockCompiler();
        $tableDefinitionCollectionFactory = new TableDefinitionCollectionFactory(new NullFrontend('test'), $contentBlockCompiler);
        $tableDefinitionCollection = $tableDefinitionCollectionFactory->createUncached(
            $contentBlockRegistry,
            $fieldTypeRegistry,
            $tcaSchemaFactory
        );
        $tcaFieldDefinition = $tableDefinitionCollection
            ->getTable('tt_content')
            ->getTcaFieldDefinitionCollection()
            ->getField('bar_foo_aField');

        $tca = $tcaFieldDefinition->getTca();

        self::assertEquals($expected, $tca['displayCond']);
    }

    public static function displayCondIsPrefixedForArraySyntaxDataProvider(): iterable
    {
        yield 'simple AND condition' => [
            [
                'AND' => [
                    'FIELD:bField:=:aValue',
                ],
            ],
            [
                'AND' => [
                    'FIELD:bar_foo_bField:=:aValue',
                ],
            ],
        ];

        yield 'AND condition with 2 values' => [
            [
                'AND' => [
                    'FIELD:bField:=:aValue',
                    'FIELD:cField:=:aValue',
                ],
            ],
            [
                'AND' => [
                    'FIELD:bar_foo_bField:=:aValue',
                    'FIELD:bar_foo_cField:=:aValue',
                ],
            ],
        ];

        yield 'complex displayCond' => [
            [
                'AND' => [
                    'FIELD:bField:=:aValue',
                    'OR' => [
                        'FIELD:cField:=:aValue',
                        'FIELD:dField:=:aValue',
                        'BLA:bField',
                    ],
                ],
            ],
            [
                'AND' => [
                    'FIELD:bar_foo_bField:=:aValue',
                    'OR' => [
                        'FIELD:bar_foo_cField:=:aValue',
                        'FIELD:bar_foo_dField:=:aValue',
                        'BLA:bField',
                    ],
                ],
            ],
        ];
    }

    #[DataProvider('displayCondIsPrefixedForArraySyntaxDataProvider')]
    #[Test]
    public function displayCondIsPrefixedForArraySyntax(array $displayCond, array $expected): void
    {
        $contentBlock = LoadedContentBlock::fromArray([
            'name' => 'bar/foo',
            'yaml' => [
                'table' => 'tt_content',
                'prefixFields' => true,
                'prefixType' => PrefixType::FULL->value,
                'fields' => [
                    [
                        'identifier' => 'aField',
                        'displayCond' => $displayCond,
                        'type' => 'Text',
                    ],
                    [
                        'identifier' => 'bField',
                        'type' => 'Text',
                    ],
                    [
                        'identifier' => 'palette',
                        'type' => 'Palette',
                        'fields' => [
                            [
                                'identifier' => 'cField',
                                'type' => 'Text',
                            ],
                            [
                                'identifier' => 'dField',
                                'type' => 'Text',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

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
        $contentBlockRegistry->register($contentBlock);
        $contentBlockCompiler = new ContentBlockCompiler();
        $tableDefinitionCollectionFactory = new TableDefinitionCollectionFactory(new NullFrontend('test'), $contentBlockCompiler);
        $tableDefinitionCollection = $tableDefinitionCollectionFactory->createUncached(
            $contentBlockRegistry,
            $fieldTypeRegistry,
            $tcaSchemaFactory
        );
        $tcaFieldDefinition = $tableDefinitionCollection
            ->getTable('tt_content')
            ->getTcaFieldDefinitionCollection()
            ->getField('bar_foo_aField');

        $tca = $tcaFieldDefinition->getTca();

        self::assertEquals($expected, $tca['displayCond']);
    }
}
