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
use TYPO3\CMS\ContentBlocks\FieldType\FolderFieldType;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class FolderFieldConfigurationTest extends UnitTestCase
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
                'label' => '',
                'description' => null,
                'displayCond' => [],
                'l10n_display' => '',
                'l10n_mode' => '',
                'onChange' => '',
                'exclude' => false,
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
            'expectedTca' => [
                'config' => [
                    'type' => 'folder',
                ],
            ],
        ];
    }

    #[DataProvider('getTcaReturnsExpectedTcaDataProvider')]
    #[Test]
    public function getTcaReturnsExpectedTca(array $config, array $expectedTca): void
    {
        $fieldConfiguration = FolderFieldType::createFromArray($config);

        self::assertSame($expectedTca, $fieldConfiguration->getTca());
    }

    public static function getSqlReturnsExpectedSqlDefinitionDataProvider(): iterable
    {
        yield 'default varchar column' => [
            'uniqueColumnName' => 'cb_example_myText',
            'expectedSql' => '`cb_example_myText` text',
        ];
    }

    #[DataProvider('getSqlReturnsExpectedSqlDefinitionDataProvider')]
    #[Test]
    public function getSqlReturnsExpectedSqlDefinition(string $uniqueColumnName, string $expectedSql): void
    {
        $inputFieldConfiguration = FolderFieldType::createFromArray([]);

        self::assertSame($expectedSql, FolderFieldType::getSql($uniqueColumnName));
    }
}
