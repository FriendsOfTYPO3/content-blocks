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

use TYPO3\CMS\ContentBlocks\FieldConfiguration\FolderFieldConfiguration;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class FolderFieldConfigurationTest extends UnitTestCase
{
    public static function getTcaReturnsExpectedTcaDataProvider(): iterable
    {
        yield 'truthy values' => [
            'config' => [
                'properties' => [
                    'non_available_field' => 'foo',
                    'default' => 1,
                    'readOnly' => 1,
                    'size' => 1,
                    'maxitems' => 1,
                    'minitems' => 1,
                    'autoSizeMax' => 1,
                    'multiple' => 1,
                    'hideMoveIcons' => 1,
                    'elementBrowserEntryPoints' => [
                        'foo' => 'bar',
                    ],
                ],
            ],
            'expectedTca' => [
                'label' => 'LLL:test-path.xlf:foo.label',
                'description' => 'LLL:test-path.xlf:foo.description',
                'config' => [
                    'type' => 'folder',
                    'default' => '1',
                    'readOnly' => true,
                    'size' => 1,
                    'maxitems' => 1,
                    'minitems' => 1,
                    'autoSizeMax' => 1,
                    'multiple' => true,
                    'hideMoveIcons' => true,
                    'elementBrowserEntryPoints' => [
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
                    'readOnly' => 0,
                    'size' => 0,
                    'maxitems' => 0,
                    'minitems' => 0,
                    'autoSizeMax' => 0,
                    'multiple' => 0,
                    'hideMoveIcons' => 0,
                    'elementBrowserEntryPoints' => [],
                    'foo' => '',
                ],
            ],
            'expectedTca' => [
                'label' => 'LLL:test-path.xlf:foo.label',
                'description' => 'LLL:test-path.xlf:foo.description',
                'config' => [
                    'type' => 'folder',
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
        $fieldConfiguration = FolderFieldConfiguration::createFromArray($config);

        self::assertSame($expectedTca, $fieldConfiguration->getTca('LLL:test-path.xlf:foo'));
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
        $inputFieldConfiguration = FolderFieldConfiguration::createFromArray([]);

        self::assertSame($expectedSql, $inputFieldConfiguration->getSql($uniqueColumnName));
    }
}
