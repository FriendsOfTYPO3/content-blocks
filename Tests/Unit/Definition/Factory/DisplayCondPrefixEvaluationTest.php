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

use TYPO3\CMS\ContentBlocks\Definition\Factory\PrefixType;
use TYPO3\CMS\ContentBlocks\Definition\Factory\TableDefinitionCollectionFactory;
use TYPO3\CMS\ContentBlocks\Loader\LoadedContentBlock;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class DisplayCondPrefixEvaluationTest extends UnitTestCase
{
    /**
     * @test
     */
    public function displayCondIsPrefixedForArraySyntax(): void
    {
        $baseTca['tt_content'] = [];
        $GLOBALS['TCA'] = $baseTca;

        $contentBlock = LoadedContentBlock::fromArray([
            'name' => 'bar/foo',
            'yaml' => [
                'table' => 'tt_content',
                'prefixFields' => true,
                'prefixType' => PrefixType::FULL->value,
                'fields' => [
                    [
                        'identifier' => 'aField',
                        'displayCond' => [
                            'AND' => [
                                'FIELD:bField:=:aValue'
                            ]
                        ],
                        'type' => 'Text',
                    ],
                    [
                        'identifier' => 'bField',
                        'type' => 'Text',
                    ],
                ],
            ],
        ]);

        $expected = [
            'AND' => [
                'FIELD:bar_foo_bField:=:aValue'
            ]
        ];

        $contentBlockRegistry = new ContentBlockRegistry();
        $contentBlockRegistry->register($contentBlock);
        $tableDefinitionCollection = (new TableDefinitionCollectionFactory($contentBlockRegistry))->create();
        $tcaFieldDefinition = $tableDefinitionCollection
            ->getTable('tt_content')
            ->getTcaFieldDefinitionCollection()
            ->getField('bar_foo_aField');

        $tca = $tcaFieldDefinition->getTca();

        self::assertEquals($expected, $tca['displayCond']);
    }

    /**
     * @test
     */
    public function displayCondIsPrefixedForStringSyntax(): void
    {
        $baseTca['tt_content'] = [];
        $GLOBALS['TCA'] = $baseTca;

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

        $expected = 'FIELD:bar_foo_aField:=:aValue';

        $contentBlockRegistry = new ContentBlockRegistry();
        $contentBlockRegistry->register($contentBlock);
        $tableDefinitionCollection = (new TableDefinitionCollectionFactory($contentBlockRegistry))->create();
        $tcaFieldDefinition = $tableDefinitionCollection
            ->getTable('tt_content')
            ->getTcaFieldDefinitionCollection()
            ->getField('bar_foo_aField');

        $tca = $tcaFieldDefinition->getTca();

        self::assertEquals($expected, $tca['displayCond']);
    }
}
