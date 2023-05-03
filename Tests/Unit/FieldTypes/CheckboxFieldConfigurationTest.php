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

use TYPO3\CMS\ContentBlocks\FieldConfiguration\CheckboxFieldConfiguration;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class CheckboxFieldConfigurationTest extends UnitTestCase
{
    public static function getTcaReturnsExpectedTcaDataProvider(): iterable
    {
        yield 'truthy values' => [
            'config' => [
                'properties' => [
                    'non_available_field' => 'foo',
                    'renderType' => 'checkboxToggle',
                    'default' => 1,
                    'readOnly' => 1,
                    'eval' => 'foo',
                    'itemsProcFunc' => 'foo->bar',
                    'cols' => 5,
                    'validation' => [
                        'foo' => 'bar',
                    ],
                    'items' => [
                        ['Item1'],
                        ['Item2'],
                    ],
                ],
            ],
            'expectedTca' => [
                'exclude' => true,
                'label' => 'LLL:test-path.xlf:foo.label',
                'description' => 'LLL:test-path.xlf:foo.description',
                'config' => [
                    'type' => 'check',
                    'renderType' => 'checkboxToggle',
                    'default' => 1,
                    'readOnly' => true,
                    'itemsProcFunc' => 'foo->bar',
                    'cols' => 5,
                    'eval' => 'foo',
                    'validation' => [
                        'foo' => 'bar',
                    ],
                    'items' => [
                        ['Item1'],
                        ['Item2'],
                    ],
                ],
            ],
        ];

        yield 'falsy values' => [
            'config' => [
                'properties' => [
                    'non_available_field' => 'foo',
                    'renderType' => '',
                    'default' => 0,
                    'readOnly' => 0,
                    'eval' => '',
                    'itemsProcFunc' => '',
                    'cols' => '',
                    'validation' => [],
                    'items' => [],
                ],
            ],
            'expectedTca' => [
                'exclude' => true,
                'label' => 'LLL:test-path.xlf:foo.label',
                'description' => 'LLL:test-path.xlf:foo.description',
                'config' => [
                    'type' => 'check',
                ],
            ],
        ];

        yield 'invertStateDisplay on, no items defined' => [
            'config' => [
                'properties' => [
                    'invertStateDisplay' => 1,
                ],
            ],
            'expectedTca' => [
                'exclude' => true,
                'label' => 'LLL:test-path.xlf:foo.label',
                'description' => 'LLL:test-path.xlf:foo.description',
                'config' => [
                    'type' => 'check',
                    'items' => [
                        ['invertStateDisplay' => true],
                    ],
                ],
            ],
        ];

        yield 'invertStateDisplay on, items defined' => [
            'config' => [
                'properties' => [
                    'invertStateDisplay' => 1,
                    'items' => [
                        ['Item1'],
                    ],
                ],
            ],
            'expectedTca' => [
                'exclude' => true,
                'label' => 'LLL:test-path.xlf:foo.label',
                'description' => 'LLL:test-path.xlf:foo.description',
                'config' => [
                    'type' => 'check',
                    'items' => [
                        [0 => 'Item1', 'invertStateDisplay' => true],
                    ],
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
        $fieldConfiguration = CheckboxFieldConfiguration::createFromArray($config);

        self::assertSame($expectedTca, $fieldConfiguration->getTca('LLL:test-path.xlf:foo', false));
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
        $inputFieldConfiguration = CheckboxFieldConfiguration::createFromArray([]);

        self::assertSame($expectedSql, $inputFieldConfiguration->getSql($uniqueColumnName));
    }
}
