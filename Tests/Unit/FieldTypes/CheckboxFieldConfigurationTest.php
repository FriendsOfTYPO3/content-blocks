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
use TYPO3\CMS\ContentBlocks\FieldType\CheckboxFieldConfiguration;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class CheckboxFieldConfigurationTest extends UnitTestCase
{
    public static function getTcaReturnsExpectedTcaDataProvider(): iterable
    {
        yield 'truthy values' => [
            'config' => [
                'label' => 'foo',
                'description' => 'foo',
                'exclude' => true,
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
                'displayCond' => [
                    'foo' => 'bar',
                ],
                'l10n_display' => 'foo',
                'l10n_mode' => 'foo',
                'onChange' => 'foo',
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
                    'renderType' => 'checkboxToggle',
                    'type' => 'check',
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
                'exclude' => false,
                'non_available_field' => 'foo',
                'renderType' => '',
                'default' => 0,
                'readOnly' => 0,
                'eval' => '',
                'itemsProcFunc' => '',
                'cols' => '',
                'validation' => [],
                'items' => [],
                'displayCond' => [],
                'l10n_display' => '',
                'l10n_mode' => '',
                'onChange' => '',
            ],
            'expectedTca' => [
                'config' => [
                    'type' => 'check',
                ],
            ],
        ];

        yield 'invertStateDisplay on, no items defined' => [
            'config' => [
                'invertStateDisplay' => 1,
            ],
            'expectedTca' => [
                'exclude' => true,
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
                'invertStateDisplay' => 1,
                'items' => [
                    ['Item1'],
                ],
            ],
            'expectedTca' => [
                'exclude' => true,
                'config' => [
                    'type' => 'check',
                    'items' => [
                        [0 => 'Item1', 'invertStateDisplay' => true],
                    ],
                ],
            ],
        ];
    }

    #[DataProvider('getTcaReturnsExpectedTcaDataProvider')]
    #[Test]
    public function getTcaReturnsExpectedTca(array $config, array $expectedTca): void
    {
        $fieldConfiguration = CheckboxFieldConfiguration::createFromArray($config);

        self::assertSame($expectedTca, $fieldConfiguration->getTca());
    }

    public static function getSqlReturnsExpectedSqlDefinitionDataProvider(): iterable
    {
        yield 'default integer column' => [
            'uniqueColumnName' => 'cb_example_myText',
            'expectedSql' => '`cb_example_myText` int(11) UNSIGNED DEFAULT \'0\' NOT NULL',
        ];
    }

    #[DataProvider('getSqlReturnsExpectedSqlDefinitionDataProvider')]
    #[Test]
    public function getSqlReturnsExpectedSqlDefinition(string $uniqueColumnName, string $expectedSql): void
    {
        $inputFieldConfiguration = CheckboxFieldConfiguration::createFromArray([]);

        self::assertSame($expectedSql, $inputFieldConfiguration->getSql($uniqueColumnName));
    }
}
