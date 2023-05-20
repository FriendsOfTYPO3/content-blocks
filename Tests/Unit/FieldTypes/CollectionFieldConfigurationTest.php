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

use TYPO3\CMS\ContentBlocks\FieldConfiguration\CollectionFieldConfiguration;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class CollectionFieldConfigurationTest extends UnitTestCase
{
    public static function getTcaReturnsExpectedTcaDataProvider(): iterable
    {
        yield 'truthy values' => [
            'config' => [
                'properties' => [
                    'non_available_field' => 'foo',
                    'readOnly' => 1,
                    'size' => 1,
                    'localizeReferencesAtParentLocalization' => 1,
                    'minitems' => 1,
                    'maxitems' => 1,
                    'MM' => 'foo',
                    'MM_hasUidField' => 1,
                    'MM_opposite_field' => 'foo',
                    'foreign_table' => 'foo',
                    'autoSizeMax' => 1,
                    'filter' => [
                        'foo' => 'bar',
                    ],
                    'appearance' => [
                        'foo' => 'bar',
                    ],
                    'behaviour' => [
                        'foo' => 'bar',
                    ],
                    'customControls' => [
                        'foo' => 'bar',
                    ],
                    'foreign_default_sortby' => 'foo',
                    'foreign_field' => 'foo',
                    'foreign_label' => 'foo',
                    'foreign_match_fields' => [
                        'foo' => 'bar',
                    ],
                    'foreign_selector' => 'foo',
                    'foreign_sortby' => 'foo',
                    'foreign_table_field' => 'foo',
                    'foreign_unique' => 'foo',
                    'overrideChildTca' => [
                        'foo' => 'bar',
                    ],
                    'symmetric_field' => 'foo',
                    'symmetric_label' => 'foo',
                    'symmetric_sortby' => 'foo',
                ],
            ],
            'expectedTca' => [
                'config' => [
                    'type' => 'inline',
                    'readOnly' => true,
                    'size' => 1,
                    'localizeReferencesAtParentLocalization' => true,
                    'maxitems' => 1,
                    'minitems' => 1,
                    'MM' => 'foo',
                    'MM_hasUidField' => true,
                    'MM_opposite_field' => 'foo',
                    'foreign_table' => 'foo',
                    'autoSizeMax' => 1,
                    'filter' => [
                        'foo' => 'bar',
                    ],
                    'appearance' => [
                        'foo' => 'bar',
                    ],
                    'behaviour' => [
                        'foo' => 'bar',
                    ],
                    'customControls' => [
                        'foo' => 'bar',
                    ],
                    'foreign_default_sortby' => 'foo',
                    'foreign_field' => 'foo',
                    'foreign_label' => 'foo',
                    'foreign_match_fields' => [
                        'foo' => 'bar',
                    ],
                    'foreign_selector' => 'foo',
                    'foreign_sortby' => 'foo',
                    'foreign_table_field' => 'foo',
                    'foreign_unique' => 'foo',
                    'overrideChildTca' => [
                        'foo' => 'bar',
                    ],
                    'symmetric_field' => 'foo',
                    'symmetric_label' => 'foo',
                    'symmetric_sortby' => 'foo',
                ],
            ],
        ];

        yield 'falsy values' => [
            'config' => [
                'properties' => [
                    'non_available_field' => 'foo',
                    'readOnly' => 0,
                    'size' => 0,
                    'localizeReferencesAtParentLocalization' => 0,
                    'minitems' => 0,
                    'maxitems' => 0,
                    'MM' => '',
                    'MM_hasUidField' => 0,
                    'MM_opposite_field' => '',
                    'autoSizeMax' => 0,
                    'filter' => [],
                    'appearance' => [],
                    'behaviour' => [],
                    'customControls' => [],
                    'foreign_default_sortby' => '',
                    'foreign_field' => '',
                    'foreign_label' => '',
                    'foreign_match_fields' => [],
                    'foreign_selector' => '',
                    'foreign_sortby' => '',
                    'foreign_table_field' => '',
                    'foreign_unique' => '',
                    'overrideChildTca' => [],
                    'symmetric_field' => '',
                    'symmetric_label' => '',
                    'symmetric_sortby' => '',
                ],
            ],
            'expectedTca' => [
                'config' => [
                    'type' => 'inline',
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
        $fieldConfiguration = CollectionFieldConfiguration::createFromArray($config);

        self::assertSame($expectedTca, $fieldConfiguration->getTca());
    }

    public static function getSqlReturnsExpectedSqlDefinitionDataProvider(): iterable
    {
        yield 'default integer column' => [
            'uniqueColumnName' => 'cb_example_myText',
            'expectedSql' => '`cb_example_myText` int(11) DEFAULT \'0\' NOT NULL',
        ];
    }

    /**
     * @test
     * @dataProvider getSqlReturnsExpectedSqlDefinitionDataProvider
     */
    public function getSqlReturnsExpectedSqlDefinition(string $uniqueColumnName, string $expectedSql): void
    {
        $inputFieldConfiguration = CollectionFieldConfiguration::createFromArray([]);

        self::assertSame($expectedSql, $inputFieldConfiguration->getSql($uniqueColumnName));
    }
}
