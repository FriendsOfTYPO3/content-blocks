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

use TYPO3\CMS\ContentBlocks\FieldConfiguration\ReferenceFieldConfiguration;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class ReferenceFieldConfigurationTest extends UnitTestCase
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
                'allowed' => 'foo',
                'foreign_table' => 'foo',
                'readOnly' => 1,
                'size' => 1,
                'maxitems' => 1,
                'minitems' => 1,
                'autoSizeMax' => 1,
                'multiple' => 1,
                'MM' => 'foo',
                'MM_opposite_field' => 'foo',
                'MM_match_fields' => [
                    'foo' => 'bar',
                ],
                'MM_oppositeUsage' => 'foo',
                'MM_table_where' => 'foo',
                'dontRemapTablesOnCopy' => 'foo',
                'localizeReferencesAtParentLocalization' => 1,
                'hideMoveIcons' => 1,
                'hideSuggest' => 1,
                'prepend_tname' => 1,
                'elementBrowserEntryPoints' => [
                    'foo' => 'bar',
                ],
                'filter' => [
                    'foo' => 'bar',
                ],
                'suggestOptions' => [
                    'foo' => 'bar',
                ],
                'appearance' => [
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
                    'type' => 'group',
                    'default' => 1,
                    'allowed' => 'foo',
                    'foreign_table' => 'foo',
                    'readOnly' => true,
                    'size' => 1,
                    'maxitems' => 1,
                    'minitems' => 1,
                    'autoSizeMax' => 1,
                    'multiple' => true,
                    'MM' => 'foo',
                    'MM_opposite_field' => 'foo',
                    'MM_match_fields' => [
                        'foo' => 'bar',
                    ],
                    'MM_oppositeUsage' => 'foo',
                    'MM_table_where' => 'foo',
                    'dontRemapTablesOnCopy' => 'foo',
                    'localizeReferencesAtParentLocalization' => true,
                    'hideMoveIcons' => true,
                    'hideSuggest' => true,
                    'prepend_tname' => true,
                    'elementBrowserEntryPoints' => [
                        'foo' => 'bar',
                    ],
                    'filter' => [
                        'foo' => 'bar',
                    ],
                    'suggestOptions' => [
                        'foo' => 'bar',
                    ],
                    'appearance' => [
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
                'non_available_field' => '',
                'default' => '',
                'allowed' => '',
                'foreign_table' => '',
                'readOnly' => 0,
                'size' => 0,
                'maxitems' => 0,
                'minitems' => 0,
                'autoSizeMax' => 0,
                'multiple' => 0,
                'MM' => '',
                'MM_opposite_field' => '',
                'MM_match_fields' => [],
                'MM_oppositeUsage' => '',
                'MM_table_where' => '',
                'dontRemapTablesOnCopy' => '',
                'localizeReferencesAtParentLocalization' => 0,
                'hideMoveIcons' => 0,
                'hideSuggest' => 0,
                'prepend_tname' => 0,
                'elementBrowserEntryPoints' => [],
                'foo' => '',
                'filter' => [],
                'suggestOptions' => [],
                'appearance' => [],
            ],
            'expectedTca' => [
                'config' => [
                    'type' => 'group',
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
        $fieldConfiguration = ReferenceFieldConfiguration::createFromArray($config);

        self::assertSame($expectedTca, $fieldConfiguration->getTca());
    }

    public static function getSqlReturnsExpectedSqlDefinitionDataProvider(): iterable
    {
        yield 'default varchar column' => [
            'uniqueColumnName' => 'cb_example_myText',
            'expectedSql' => '`cb_example_myText` text',
        ];
    }

    /**
     * @test
     * @dataProvider getSqlReturnsExpectedSqlDefinitionDataProvider
     */
    public function getSqlReturnsExpectedSqlDefinition(string $uniqueColumnName, string $expectedSql): void
    {
        $inputFieldConfiguration = ReferenceFieldConfiguration::createFromArray([]);

        self::assertSame($expectedSql, $inputFieldConfiguration->getSql($uniqueColumnName));
    }
}
