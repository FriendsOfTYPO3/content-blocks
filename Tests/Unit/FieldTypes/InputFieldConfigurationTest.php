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

use TYPO3\CMS\ContentBlocks\FieldConfiguration\InputFieldConfiguration;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class InputFieldConfigurationTest extends UnitTestCase
{
    public function getTcaReturnsExpectedTcaDataProvider(): iterable
    {
        yield 'Input field with truthy values' => [
            'config' => [
                'identifier' => 'myText',
                'languagePath' => 'test-path-for-input.xlf:text',
                'properties' => [
                    'default' => 'Default value',
                    'placeholder' => 'Placeholder text',
                    'max' => 15,
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
                        ]
                    ],
                    'eval' => ['trim', 'lower'],
                ],
            ],
            'expectedTca' => [
                'exclude' => true,
                'label' => 'LLL:test-path-for-input.xlf:text.label',
                'description' => 'LLL:test-path-for-input.xlf:text.description',
                'config' => [
                    'type' => 'input',
                    'default' => 'Default value',
                    'placeholder' => 'Placeholder text',
                    'max' => 15,
                    'size' => 20,
                    'autocomplete' => true,
                    'required' => true,
                    'readOnly' => true,
                    'nullable' => true,
                    'mode' => 'useOrOverridePlaceholder',
                    'is_in' => 'abc',
                    'valuePicker' => [
                        'items' => [
                            ['One', '1'],
                            ['Two', '2'],
                        ]
                    ],
                    'eval' => 'trim,lower',
                ],
            ],
        ];

        yield 'Input field with falsy values' => [
            'config' => [
                'identifier' => 'myText',
                'languagePath' => 'test-path-for-input.xlf:text',
                'properties' => [
                    'default' => '',
                    'placeholder' => '',
                    'max' => 0,
                    'size' => 0,
                    'autocomplete' => 0,
                    'required' => 0,
                    'readOnly' => 0,
                    'nullable' => 0,
                    'mode' => '',
                    'is_in' => '',
                    'valuePicker' => [
                        'items' => []
                    ],
                    'eval' => [],
                ],
            ],
            'expectedTca' => [
                'exclude' => true,
                'label' => 'LLL:test-path-for-input.xlf:text.label',
                'description' => 'LLL:test-path-for-input.xlf:text.description',
                'config' => [
                    'type' => 'input',
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
        $inputFieldConfiguration = InputFieldConfiguration::createFromArray($config);

        self::assertEquals($expectedTca, $inputFieldConfiguration->getTca());
    }

    public function getSqlReturnsExpectedSqlDataProvider(): iterable
    {
        yield 'Simple input field' => [
            'config' => [
                'identifier' => 'myText',
                'languagePath' => 'test-path-for-input.xlf:text',
            ],
            'uniqueColumnName' => 'cb_example_myText',
            'expectedSql' => '`cb_example_myText` VARCHAR(255) DEFAULT \'\' NOT NULL',
        ];
    }

    /**
     * @test
     * @dataProvider getSqlReturnsExpectedSqlDataProvider
     */
    public function getTcaReturnsExpectedSql(array $config, string $uniqueColumnName, string $expectedSql): void
    {
        $inputFieldConfiguration = InputFieldConfiguration::createFromArray($config);

        self::assertSame($expectedSql, $inputFieldConfiguration->getSql($uniqueColumnName));
    }
}
