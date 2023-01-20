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

use TYPO3\CMS\ContentBlocks\FieldConfiguration\CategoryFieldConfiguration;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class CategoryFieldConfigurationTest extends UnitTestCase
{
    public function getTcaReturnsExpectedTcaDataProvider(): iterable
    {
        yield 'truthy values' => [
            'config' => [
                'properties' => [
                    'non_available_field' => 'foo',
                    'default' => 1,
                    'readOnly' => 1,
                    'maxitems' => 1,
                    'minitems' => 1,
                    'exclusiveKeys' => 'key',
                    'treeConfig' => [
                        'foo' => 'bar',
                    ],
                    'relationship' => 'foo',
                ],
            ],
            'expectedTca' => [
                'exclude' => true,
                'label' => 'LLL:test-path.xlf:foo.label',
                'description' => 'LLL:test-path.xlf:foo.description',
                'config' => [
                    'type' => 'category',
                    'default' => 1,
                    'readOnly' => true,
                    'maxitems' => 1,
                    'minitems' => 1,
                    'exclusiveKeys' => 'key',
                    'treeConfig' => [
                        'foo' => 'bar',
                    ],
                    'relationship' => 'foo',
                ],
            ],
        ];

        yield 'falsy values' => [
            'config' => [
                'properties' => [
                    'non_available_field' => 'foo',
                    'default' => '',
                    'readOnly' => 0,
                    'maxitems' => 0,
                    'minitems' => 0,
                    'exclusiveKeys' => '',
                    'treeConfig' => [],
                    'relationship' => '',
                ],
            ],
            'expectedTca' => [
                'exclude' => true,
                'label' => 'LLL:test-path.xlf:foo.label',
                'description' => 'LLL:test-path.xlf:foo.description',
                'config' => [
                    'type' => 'category',
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
        $fieldConfiguration = CategoryFieldConfiguration::createFromArray($config);

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
        $inputFieldConfiguration = CategoryFieldConfiguration::createFromArray([]);

        self::assertSame($expectedSql, $inputFieldConfiguration->getSql($uniqueColumnName));
    }
}
