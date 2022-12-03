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
    /**
     * dataprovider for checking InputFieldConfiguration
     */
    public function checkInputFieldConfigurationDataProvider(): iterable
    {
        yield 'Check input field configurations.' => [
            'config' => [
                'text' => [
                    'identifier' => 'myText',
                    'languagePath' => 'test-path-for-input.xlf:text',
                    'properties' => [
                        'autocomplete' => true,
                        'default' => 'Default value',
                        'max' => 15,
                        'placeholder' => 'Placeholder text',
                        'size' => 20,
                        'required' => true,
                        'trim' => true,
                        'valuePicker' => [
                            'items' => [
                                'Spring' => 'spring',
                                'Summer' => 'summer',
                                'Autumn' => 'autumn',
                                'Winter' => 'winter',
                            ],
                        ],
                    ],
                ],
            ],

            'uniqueColumnName' => 'cb_example_myText',
            'expected' => [
                'getSql' => '`cb_example_myText` VARCHAR(20) DEFAULT \'\' NOT NULL',
                'getTca' => [
                    'label' => 'LLL:test-path-for-input.xlf:text.label',
                    'description' => 'LLL:test-path-for-input.xlf:text.description',
                    'config' => [
                        'type' => 'input',
                        'size' => 20,
                        'max' => 15,
                        'default' => 'Default value',
                        'placeholder' => 'Placeholder text',
                        'required' => true,
                        'autocomplete' => true,
                        'valuePicker' => [
                           'items' => [
                              ['spring', 'Spring'],
                              ['summer', 'Summer'],
                              ['autumn', 'Autumn'],
                              ['winter', 'Winter'],
                           ],
                        ],
                    ],
                    'exclude' => 1,
                ],
            ],
        ];
    }

    /**
     * InputFieldConfiguration Test
     *
     * @test
     * @dataProvider checkInputFieldConfigurationDataProvider
     */
    public function checkInputFieldConfiguration(array $config, string $uniqueColumnName, array $expected): void
    {
        // Inputfield test

        $inputfield = new InputFieldConfiguration($config['text']);
        self::assertSame($expected['getSql'], $inputfield->getSql($uniqueColumnName));

        self::assertSame($expected['getTca'], $inputfield->getTca());
    }
}
