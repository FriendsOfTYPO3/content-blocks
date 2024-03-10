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

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\ContentBlocks\FieldType\SelectFieldType;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class SelectFieldConfigurationTest extends UnitTestCase
{
    public static function getTcaReturnsExpectedTcaDataProvider(): iterable
    {
        yield 'truthy values' => [
            'config' => [
                'label' => 'foo',
                'description' => 'foo',
                'displayCond' => [
                    'foo' => 'bar',
                ],
                'l10n_display' => 'foo',
                'l10n_mode' => 'foo',
                'onChange' => 'foo',
                'exclude' => true,
                'non_available_field' => 'foo',
                'default' => 1,
                'renderType' => 'selectSingle',
                'readOnly' => 1,
                'size' => 1,
                'MM' => 'foo',
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
                'itemsProcConfig' => [
                    'foo' => 'bar',
                ],
            ],
            'expectedTca' => [
                'label' => 'foo',
                'description' => 'foo',
                'displayCond' => [
                    'foo' => 'bar',
                ],
                'l10n_display' => 'foo',
                'l10n_mode' => 'foo',
                'onChange' => 'foo',
                'exclude' => true,
                'config' => [
                    'renderType' => 'selectSingle',
                    'type' => 'select',
                    'default' => 1,
                    'readOnly' => true,
                    'size' => 1,
                    'MM' => 'foo',
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
                    'itemsProcConfig' => [
                        'foo' => 'bar',
                    ],
                ],
            ],
        ];

        yield 'falsy values' => [
            'config' => [
                'label' => '',
                'description' => null,
                'displayCond' => [],
                'l10n_display' => '',
                'l10n_mode' => '',
                'onChange' => '',
                'exclude' => false,
                'non_available_field' => 'foo',
                'default' => '',
                'renderType' => '',
                'readOnly' => 0,
                'size' => 0,
                'MM' => '',
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
            'expectedTca' => [
                'config' => [
                    'type' => 'select',
                    'items' => [],
                ],
            ],
        ];
    }

    #[DataProvider('getTcaReturnsExpectedTcaDataProvider')]
    #[Test]
    public function getTcaReturnsExpectedTca(array $config, array $expectedTca): void
    {
        $fieldConfiguration = SelectFieldType::createFromArray($config);

        self::assertSame($expectedTca, $fieldConfiguration->getTca());
    }

    public static function getSqlReturnsExpectedSqlDefinitionDataProvider(): iterable
    {
        yield 'default varchar column' => [
            'uniqueColumnName' => 'cb_example_myText',
            'expectedSql' => '`cb_example_myText` VARCHAR(255) DEFAULT \'\' NOT NULL',
        ];
    }

    #[DataProvider('getSqlReturnsExpectedSqlDefinitionDataProvider')]
    #[Test]
    public function getSqlReturnsExpectedSqlDefinition(string $uniqueColumnName, string $expectedSql): void
    {
        $inputFieldConfiguration = SelectFieldType::createFromArray([]);

        self::assertSame($expectedSql, SelectFieldType::getSql($uniqueColumnName));
    }
}
