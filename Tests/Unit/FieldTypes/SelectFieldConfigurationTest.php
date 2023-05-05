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

namespace TYPO3\CMS\ContentBlocks\Tests\Unit\FieldTypes;

use TYPO3\CMS\ContentBlocks\FieldConfiguration\SelectFieldConfiguration;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class SelectFieldConfigurationTest extends UnitTestCase
{
    public static function getTcaReturnsExpectedTcaDataProvider(): iterable
    {
        yield 'truthy values' => [
            'config' => [
                'properties' => [
                    'non_available_field' => 'foo',
                    'default' => 1,
                    'renderType' => 'selectSingle',
                    'readOnly' => 1,
                    'size' => 1,
                    'MM' => 'foo',
                    'MM_hasUidField' => 1,
                    'MM_opposite_field' => 'foo',
                    'MM_match_fields' => [
                        'foo' => 'bar',
                    ],
                    'MM_oppositeUsage' => 'foo',
                    'MM_table_where' => 'foo',
                    'dontRemapTablesOnCopy' => 'foo',
                    'localizeReferencesAtParentLocalization' => 1,
                    'maxitems' => 1,
                    'minitems' => 1,
                    'foreign_table' => 'foo',
                    'itemsProcFunc' => 'foo',
                    'allowNonIdValues' => 1,
                    'authMode' => 'foo',
                    'disableNoMatchingValueElement' => 1,
                    'exclusiveKeys' => 'key',
                    'fileFolderConfig' => [
                        'foo' => 'bar',
                    ],
                    'foreign_table_prefix' => 'key',
                    'foreign_table_where' => 'key',
                    'itemGroups' => [
                        'foo' => 'bar',
                    ],
                    'items' => [
                        'foo' => 'bar',
                    ],
                    'sortItems' => [
                        'foo' => 'bar',
                    ],
                    'appearance' => [
                        'foo' => 'bar',
                    ],
                    'treeConfig' => [
                        'foo' => 'bar',
                    ],
                ],
            ],
            'expectedTca' => [
                'label' => 'LLL:test-path.xlf:foo.label',
                'description' => 'LLL:test-path.xlf:foo.description',
                'config' => [
                    'type' => 'select',
                    'renderType' => 'selectSingle',
                    'default' => 1,
                    'readOnly' => true,
                    'size' => 1,
                    'MM' => 'foo',
                    'MM_hasUidField' => true,
                    'MM_opposite_field' => 'foo',
                    'MM_match_fields' => [
                        'foo' => 'bar',
                    ],
                    'MM_oppositeUsage' => 'foo',
                    'MM_table_where' => 'foo',
                    'dontRemapTablesOnCopy' => 'foo',
                    'localizeReferencesAtParentLocalization' => true,
                    'maxitems' => 1,
                    'minitems' => 1,
                    'foreign_table' => 'foo',
                    'itemsProcFunc' => 'foo',
                    'allowNonIdValues' => true,
                    'authMode' => 'foo',
                    'disableNoMatchingValueElement' => true,
                    'exclusiveKeys' => 'key',
                    'fileFolderConfig' => [
                        'foo' => 'bar',
                    ],
                    'foreign_table_prefix' => 'key',
                    'foreign_table_where' => 'key',
                    'itemGroups' => [
                        'foo' => 'bar',
                    ],
                    'items' => [
                        'foo' => 'bar',
                    ],
                    'sortItems' => [
                        'foo' => 'bar',
                    ],
                    'appearance' => [
                        'foo' => 'bar',
                    ],
                    'treeConfig' => [
                        'foo' => 'bar',
                    ],
                ],
            ],
        ];

        yield 'falsy values' => [
            'config' => [
                'properties' => [
                    'non_available_field' => 'foo',
                    'default' => '',
                    'renderType' => '',
                    'readOnly' => 0,
                    'size' => 0,
                    'MM' => '',
                    'MM_hasUidField' => 0,
                    'MM_opposite_field' => '',
                    'MM_match_fields' => [],
                    'MM_oppositeUsage' => '',
                    'MM_table_where' => '',
                    'dontRemapTablesOnCopy' => '',
                    'localizeReferencesAtParentLocalization' => 0,
                    'maxitems' => 0,
                    'minitems' => 0,
                    'foreign_table' => '',
                    'itemsProcFunc' => '',
                    'allowNonIdValues' => 0,
                    'authMode' => '',
                    'disableNoMatchingValueElement' => 0,
                    'exclusiveKeys' => '',
                    'fileFolderConfig' => [],
                    'foreign_table_prefix' => '',
                    'foreign_table_where' => '',
                    'itemGroups' => [],
                    'items' => [],
                    'sortItems' => [],
                    'appearance' => [],
                    'treeConfig' => [],
                ],
            ],
            'expectedTca' => [
                'label' => 'LLL:test-path.xlf:foo.label',
                'description' => 'LLL:test-path.xlf:foo.description',
                'config' => [
                    'type' => 'select',
                ],
            ],
        ];
    }

    /**
     * @test
     * @dataProvider getTcaReturnsExpectedTcaDataProvider
     */
    public function getTcaReturnsExpectedTca(array $config, array $expectedTca): void
    {
        $fieldConfiguration = SelectFieldConfiguration::createFromArray($config);

        self::assertSame($expectedTca, $fieldConfiguration->getTca('LLL:test-path.xlf:foo'));
    }

    public static function getSqlReturnsExpectedSqlDefinitionDataProvider(): iterable
    {
        yield 'default varchar column' => [
            'uniqueColumnName' => 'cb_example_myText',
            'expectedSql' => '`cb_example_myText` VARCHAR(255) DEFAULT \'\' NOT NULL',
        ];
    }

    /**
     * @test
     * @dataProvider getSqlReturnsExpectedSqlDefinitionDataProvider
     */
    public function getSqlReturnsExpectedSqlDefinition(string $uniqueColumnName, string $expectedSql): void
    {
        $inputFieldConfiguration = SelectFieldConfiguration::createFromArray([]);

        self::assertSame($expectedSql, $inputFieldConfiguration->getSql($uniqueColumnName));
    }
}
