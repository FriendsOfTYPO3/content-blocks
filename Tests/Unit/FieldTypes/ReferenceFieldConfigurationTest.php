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

class ReferenceFieldConfigurationTest extends UnitTestCase
{
    public function getTcaReturnsExpectedTcaDataProvider(): iterable
    {
        yield 'truthy values' => [
            'config' => [
                'properties' => [
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
                    'MM_hasUidField' => 1,
                    'MM_opposite_field' => 'foo',
                    'MM_insert_fields' => [
                        'foo' => 'bar',
                    ],
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
            ],
            'expectedTca' => [
                'exclude' => true,
                'label' => 'LLL:test-path.xlf:foo.label',
                'description' => 'LLL:test-path.xlf:foo.description',
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
                    'MM_hasUidField' => true,
                    'MM_opposite_field' => 'foo',
                    'MM_insert_fields' => [
                        'foo' => 'bar',
                    ],
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
                'properties' => [
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
                    'MM_hasUidField' => 0,
                    'MM_opposite_field' => '',
                    'MM_insert_fields' => [],
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
            ],
            'expectedTca' => [
                'exclude' => true,
                'label' => 'LLL:test-path.xlf:foo.label',
                'description' => 'LLL:test-path.xlf:foo.description',
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

        self::assertSame($expectedTca, $fieldConfiguration->getTca('LLL:test-path.xlf:foo', false));
    }

    public function getSqlReturnsExpectedSqlDefinitionDataProvider(): iterable
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
        $inputFieldConfiguration = ReferenceFieldConfiguration::createFromArray([]);

        self::assertSame($expectedSql, $inputFieldConfiguration->getSql($uniqueColumnName));
    }
}
