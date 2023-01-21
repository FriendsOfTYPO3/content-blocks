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

use TYPO3\CMS\ContentBlocks\FieldConfiguration\NumberFieldConfiguration;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class NumberFieldConfigurationTest extends UnitTestCase
{
    public function getTcaReturnsExpectedTcaDataProvider(): iterable
    {
        yield 'truthy values' => [
            'config' => [
                'properties' => [
                    'non_available_field' => 'foo',
                    'default' => 10,
                    'placeholder' => 'Placeholder text',
                    'size' => 20,
                    'autocomplete' => 1,
                    'required' => 1,
                    'readOnly' => 1,
                    'nullable' => 1,
                    'mode' => 'useOrOverridePlaceholder',
                    'is_in' => 'abc',
                    'valuePicker' => [
                        'items' => [
                            ['One', '1'],
                            ['Two', '2'],
                        ],
                    ],
                    'range' => [
                        'lower' => 10,
                    ],
                    'slider' => [
                        'step' => 1,
                    ],
                ],
            ],
            'expectedTca' => [
                'exclude' => true,
                'label' => 'LLL:test-path.xlf:foo.label',
                'description' => 'LLL:test-path.xlf:foo.description',
                'config' => [
                    'type' => 'number',
                    'size' => 20,
                    'default' => 10,
                    'readOnly' => true,
                    'nullable' => true,
                    'mode' => 'useOrOverridePlaceholder',
                    'placeholder' => 'Placeholder text',
                    'required' => true,
                    'autocomplete' => true,
                    'valuePicker' => [
                        'items' => [
                            ['One', '1'],
                            ['Two', '2'],
                        ],
                    ],
                    'range' => [
                        'lower' => 10,
                    ],
                    'slider' => [
                        'step' => 1,
                    ],
                ],
            ],
        ];

        yield 'falsy values' => [
            'config' => [
                'properties' => [
                    'non_available_field' => 'foo',
                    'default' => '',
                    'format' => '',
                    'placeholder' => '',
                    'size' => 0,
                    'autocomplete' => 0,
                    'required' => 0,
                    'readOnly' => 0,
                    'nullable' => 0,
                    'mode' => '',
                    'is_in' => '',
                    'valuePicker' => [
                        'items' => [],
                    ],
                    'range' => [],
                    'slider' => [],
                ],
            ],
            'expectedTca' => [
                'exclude' => true,
                'label' => 'LLL:test-path.xlf:foo.label',
                'description' => 'LLL:test-path.xlf:foo.description',
                'config' => [
                    'type' => 'number',
                    'autocomplete' => false,
                ],
            ],
        ];

        yield 'format decimal default value float' => [
            'config' => [
                'properties' => [
                    'non_available_field' => 'foo',
                    'default' => 10,
                    'format' => 'decimal',
                ],
            ],
            'expectedTca' => [
                'exclude' => true,
                'label' => 'LLL:test-path.xlf:foo.label',
                'description' => 'LLL:test-path.xlf:foo.description',
                'config' => [
                    'type' => 'number',
                    'default' => 10.0,
                    'format' => 'decimal',
                ],
            ],
        ];

        yield 'format decimal default value zero not set as default' => [
            'config' => [
                'properties' => [
                    'non_available_field' => 'foo',
                    'default' => 0,
                    'format' => 'decimal',
                ],
            ],
            'expectedTca' => [
                'exclude' => true,
                'label' => 'LLL:test-path.xlf:foo.label',
                'description' => 'LLL:test-path.xlf:foo.description',
                'config' => [
                    'type' => 'number',
                    'format' => 'decimal',
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
        $fieldConfiguration = NumberFieldConfiguration::createFromArray($config);

        self::assertSame($expectedTca, $fieldConfiguration->getTca('LLL:test-path.xlf:foo', false));
    }

    public function getSqlReturnsExpectedSqlDefinitionDataProvider(): iterable
    {
        yield 'integer column' => [
            'config' => [],
            'uniqueColumnName' => 'cb_example_myText',
            'expectedSql' => '`cb_example_myText` int(11) DEFAULT \'0\' NOT NULL',
        ];
        yield 'float column' => [
            'config' => [
                'properties' => [
                    'format' => 'decimal',
                ],
            ],
            'uniqueColumnName' => 'cb_example_myText',
            'expectedSql' => '`cb_example_myText` float DEFAULT \'0\' NOT NULL',
        ];
    }

    /**
     * @test
     * @dataProvider getSqlReturnsExpectedSqlDefinitionDataProvider
     */
    public function getSqlReturnsExpectedSqlDefinition(array $config, string $uniqueColumnName, string $expectedSql): void
    {
        $inputFieldConfiguration = NumberFieldConfiguration::createFromArray($config);

        self::assertSame($expectedSql, $inputFieldConfiguration->getSql($uniqueColumnName));
    }
}
