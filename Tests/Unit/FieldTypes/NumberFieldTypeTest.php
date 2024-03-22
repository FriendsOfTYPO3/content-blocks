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
use TYPO3\CMS\ContentBlocks\FieldType\NumberFieldType;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class NumberFieldTypeTest extends UnitTestCase
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
                'label' => '',
                'description' => null,
                'displayCond' => [],
                'l10n_display' => '',
                'l10n_mode' => '',
                'onChange' => '',
                'exclude' => false,
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
            'expectedTca' => [
                'config' => [
                    'type' => 'number',
                    'autocomplete' => false,
                ],
            ],
        ];

        yield 'format decimal default value float' => [
            'config' => [
                'non_available_field' => 'foo',
                'default' => 10,
                'format' => 'decimal',
            ],
            'expectedTca' => [
                'exclude' => true,
                'config' => [
                    'type' => 'number',
                    'default' => 10.0,
                    'format' => 'decimal',
                ],
            ],
        ];

        yield 'format decimal default value zero not set as default' => [
            'config' => [
                'non_available_field' => 'foo',
                'default' => 0,
                'format' => 'decimal',
            ],
            'expectedTca' => [
                'exclude' => true,
                'config' => [
                    'type' => 'number',
                    'format' => 'decimal',
                ],
            ],
        ];
    }

    #[DataProvider('getTcaReturnsExpectedTcaDataProvider')]
    #[Test]
    public function getTcaReturnsExpectedTca(array $config, array $expectedTca): void
    {
        $fieldConfiguration = NumberFieldType::createFromArray($config);

        self::assertSame($expectedTca, $fieldConfiguration->getTca());
    }

    public static function getSqlReturnsExpectedSqlDefinitionDataProvider(): iterable
    {
        yield 'integer column' => [
            'config' => [],
            'uniqueColumnName' => 'cb_example_myText',
            'expectedSql' => '`cb_example_myText` int(11) DEFAULT \'0\' NOT NULL',
        ];
        yield 'decimal column' => [
            'config' => [
                'format' => 'decimal',
            ],
            'uniqueColumnName' => 'cb_example_myText',
            'expectedSql' => '`cb_example_myText` decimal(10,2) DEFAULT \'0.00\' NOT NULL',
        ];
    }

    #[DataProvider('getSqlReturnsExpectedSqlDefinitionDataProvider')]
    #[Test]
    public function getSqlReturnsExpectedSqlDefinition(array $config, string $uniqueColumnName, string $expectedSql): void
    {
        $fieldType = NumberFieldType::createFromArray($config);

        self::assertSame($expectedSql, $fieldType->getSql($uniqueColumnName));
    }
}
