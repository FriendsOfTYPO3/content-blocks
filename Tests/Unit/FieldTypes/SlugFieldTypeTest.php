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
use TYPO3\CMS\ContentBlocks\FieldType\SlugFieldType;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class SlugFieldTypeTest extends UnitTestCase
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
                'readOnly' => true,
                'size' => 1,
                'appearance' => [
                    'foo' => 'bar',
                ],
                'eval' => 'foo',
                'fallbackCharacter' => 'foo',
                'generatorOptions' => [
                    'foo' => 'bar',
                ],
                'prependSlash' => true,
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
                    'type' => 'slug',
                    'readOnly' => true,
                    'size' => 1,
                    'appearance' => [
                        'foo' => 'bar',
                    ],
                    'eval' => 'foo',
                    'fallbackCharacter' => 'foo',
                    'generatorOptions' => [
                        'foo' => 'bar',
                    ],
                    'prependSlash' => true,
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
                'readOnly' => false,
                'size' => 0,
                'appearance' => [],
                'eval' => '',
                'fallbackCharacter' => '',
                'generatorOptions' => [],
                'prependSlash' => false,
            ],
            'expectedTca' => [
                'config' => [
                    'type' => 'slug',
                ],
            ],
        ];
    }

    #[DataProvider('getTcaReturnsExpectedTcaDataProvider')]
    #[Test]
    public function getTcaReturnsExpectedTca(array $config, array $expectedTca): void
    {
        $fieldConfiguration = SlugFieldType::createFromArray($config);

        self::assertSame($expectedTca, $fieldConfiguration->getTca());
    }

    public static function getSqlReturnsExpectedSqlDefinitionDataProvider(): iterable
    {
        yield 'default varchar column' => [
            'uniqueColumnName' => 'cb_example_myText',
            'expectedSql' => '',
        ];
    }

    #[DataProvider('getSqlReturnsExpectedSqlDefinitionDataProvider')]
    #[Test]
    public function getSqlReturnsExpectedSqlDefinition(string $uniqueColumnName, string $expectedSql): void
    {
        $fieldType = SlugFieldType::createFromArray([]);

        self::assertSame($expectedSql, $fieldType->getSql($uniqueColumnName));
    }
}
