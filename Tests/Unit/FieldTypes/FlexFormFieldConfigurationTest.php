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

use TYPO3\CMS\ContentBlocks\FieldConfiguration\FlexFormConfiguration;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class FlexFormFieldConfigurationTest extends UnitTestCase
{
    public static function getTcaReturnsExpectedTcaDataProvider(): iterable
    {
        yield 'truthy values' => [
            'config' => [
                'properties' => [
                    'non_available_field' => 'foo',
                    'ds_pointerField' => 'foobar',
                    'ds' => ['foo' => 'bar'],
                ],
            ],
            'expectedTca' => [
                'label' => 'LLL:test-path.xlf:foo.label',
                'description' => 'LLL:test-path.xlf:foo.description',
                'config' => [
                    'type' => 'flex',
                    'ds_pointerField' => 'foobar',
                    'ds' => ['foo' => 'bar'],
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
        $fieldConfiguration = FlexFormConfiguration::createFromArray($config);

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
        $inputFieldConfiguration = FlexFormConfiguration::createFromArray([]);

        self::assertSame($expectedSql, $inputFieldConfiguration->getSql($uniqueColumnName));
    }
}
