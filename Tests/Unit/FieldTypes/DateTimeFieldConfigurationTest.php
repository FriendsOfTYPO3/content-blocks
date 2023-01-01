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

use TYPO3\CMS\ContentBlocks\FieldConfiguration\DateTimeFieldConfiguration;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class DateTimeFieldConfigurationTest extends UnitTestCase
{
    public function getTcaReturnsExpectedTcaDataProvider(): iterable
    {
        yield 'truthy values' => [
            'config' => [
                'properties' => [
                    'non_available_field' => 'foo',
                    'default' => 1,
                    'readOnly' => 1,
                    'size' => 1,
                    'required' => 1,
                    'nullable' => 1,
                    'mode' => 'foo',
                    'placeholder' => 'foo',
                    'range' => [
                        'foo' => 'bar',
                    ],
                    'dbType' => 'foo',
                    'disableAgeDisplay' => 1,
                    'format' => 'foo',
                ],
            ],
            'expectedTca' => [
                'exclude' => true,
                'label' => 'LLL:test-path.xlf:foo.label',
                'description' => 'LLL:test-path.xlf:foo.description',
                'config' => [
                    'type' => 'datetime',
                    'default' => 1,
                    'readOnly' => true,
                    'size' => 1,
                    'required' => true,
                    'nullable' => true,
                    'mode' => 'foo',
                    'placeholder' => 'foo',
                    'range' => [
                        'foo' => 'bar',
                    ],
                    'dbType' => 'foo',
                    'disableAgeDisplay' => true,
                    'format' => 'foo',
                ],
            ],
        ];

        yield 'falsy values' => [
            'config' => [
                'properties' => [
                    'non_available_field' => 'foo',
                    'default' => '',
                    'readOnly' => 0,
                    'size' => 0,
                    'required' => 0,
                    'nullable' => 0,
                    'mode' => '',
                    'placeholder' => '',
                    'range' => [],
                    'dbType' => '',
                    'disableAgeDisplay' => 0,
                    'format' => '',
                ],
            ],
            'expectedTca' => [
                'exclude' => true,
                'label' => 'LLL:test-path.xlf:foo.label',
                'description' => 'LLL:test-path.xlf:foo.description',
                'config' => [
                    'type' => 'datetime',
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
        $fieldConfiguration = DateTimeFieldConfiguration::createFromArray($config);

        self::assertSame($expectedTca, $fieldConfiguration->getTca('test-path.xlf:foo', false));
    }

    public function getSqlReturnsExpectedSqlDefinitionDataProvider(): iterable
    {
        yield 'default varchar column' => [
            'uniqueColumnName' => 'cb_example_myText',
            'expectedSql' => '',
        ];
    }

    /**
     * @test
     * @dataProvider getSqlReturnsExpectedSqlDefinitionDataProvider
     */
    public function getSqlReturnsExpectedSqlDefinition(string $uniqueColumnName, string $expectedSql): void
    {
        $inputFieldConfiguration = DateTimeFieldConfiguration::createFromArray([]);

        self::assertSame($expectedSql, $inputFieldConfiguration->getSql($uniqueColumnName));
    }
}
