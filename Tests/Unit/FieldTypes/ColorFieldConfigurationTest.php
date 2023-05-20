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

use TYPO3\CMS\ContentBlocks\FieldConfiguration\ColorFieldConfiguration;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class ColorFieldConfigurationTest extends UnitTestCase
{
    public static function getTcaReturnsExpectedTcaDataProvider(): iterable
    {
        yield 'truthy values' => [
            'config' => [
                'properties' => [
                    'non_available_field' => 'foo',
                    'default' => '#000000',
                    'placeholder' => 'Placeholder text',
                    'size' => 20,
                    'autocomplete' => 1,
                    'required' => 1,
                    'readOnly' => 1,
                    'nullable' => 1,
                    'mode' => 'useOrOverridePlaceholder',
                    'valuePicker' => [
                        'items' => [
                            ['One', '1'],
                            ['Two', '2'],
                        ],
                    ],
                ],
            ],
            'expectedTca' => [
                'config' => [
                    'type' => 'color',
                    'size' => 20,
                    'default' => '#000000',
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
                ],
            ],
        ];

        yield 'falsy values' => [
            'config' => [
                'properties' => [
                    'non_available_field' => 'foo',
                    'default' => '',
                    'placeholder' => '',
                    'size' => 0,
                    'autocomplete' => 0,
                    'required' => 0,
                    'readOnly' => 0,
                    'nullable' => 0,
                    'mode' => '',
                    'valuePicker' => [
                        'items' => [],
                    ],
                ],
            ],
            'expectedTca' => [
                'config' => [
                    'type' => 'color',
                    'autocomplete' => false,
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
        $fieldConfiguration = ColorFieldConfiguration::createFromArray($config);

        self::assertSame($expectedTca, $fieldConfiguration->getTca());
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
        $inputFieldConfiguration = ColorFieldConfiguration::createFromArray([]);

        self::assertSame($expectedSql, $inputFieldConfiguration->getSql($uniqueColumnName));
    }
}
