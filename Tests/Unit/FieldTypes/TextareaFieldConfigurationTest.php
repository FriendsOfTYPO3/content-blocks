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

use TYPO3\CMS\ContentBlocks\FieldConfiguration\TextareaFieldConfiguration;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class TextareaFieldConfigurationTest extends UnitTestCase
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
                'default' => 'Default value',
                'placeholder' => 'Placeholder text',
                'max' => 15,
                'min' => 3,
                'size' => 20,
                'rows' => 10,
                'cols' => 20,
                'autocomplete' => 1,
                'required' => 1,
                'readOnly' => 1,
                'nullable' => 1,
                'enableTabulator' => 1,
                'fixedFont' => 1,
                'mode' => 'useOrOverridePlaceholder',
                'is_in' => 'abc',
                'wrap' => 'off',
                'eval' => ['trim', 'lower'],
                'enableRichtext' => 1,
                'richtextConfiguration' => 'default',
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
                    'type' => 'text',
                    'default' => 'Default value',
                    'readOnly' => true,
                    'required' => true,
                    'max' => 15,
                    'min' => 3,
                    'nullable' => true,
                    'mode' => 'useOrOverridePlaceholder',
                    'placeholder' => 'Placeholder text',
                    'is_in' => 'abc',
                    'eval' => 'trim,lower',
                    'rows' => 10,
                    'cols' => 20,
                    'enableTabulator' => true,
                    'fixedFont' => true,
                    'wrap' => 'off',
                    'enableRichtext' => true,
                    'richtextConfiguration' => 'default',
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
                'placeholder' => '',
                'max' => 0,
                'min' => 0,
                'rows' => 0,
                'cols' => 0,
                'size' => 0,
                'autocomplete' => 0,
                'required' => 0,
                'readOnly' => 0,
                'nullable' => 0,
                'mode' => '',
                'is_in' => '',
                'wrap' => '',
                'valuePicker' => [
                    'items' => [],
                ],
                'eval' => [],
                'enableTabulator' => 0,
                'fixedFont' => 0,
                'enableRichtext' => 0,
                'richtextConfiguration' => '',
            ],
            'expectedTca' => [
                'config' => [
                    'type' => 'text',
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
        $fieldConfiguration = TextareaFieldConfiguration::createFromArray($config);

        self::assertSame($expectedTca, $fieldConfiguration->getTca());
    }

    public static function getSqlReturnsExpectedSqlDefinitionDataProvider(): iterable
    {
        yield 'default text column' => [
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
        $inputFieldConfiguration = TextareaFieldConfiguration::createFromArray([]);

        self::assertSame($expectedSql, $inputFieldConfiguration->getSql($uniqueColumnName));
    }
}
