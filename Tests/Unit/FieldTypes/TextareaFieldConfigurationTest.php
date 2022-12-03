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

class TextareaFieldConfigurationTest extends UnitTestCase
{
    /**
     * dataprovider for checking TextareaFieldConfiguration
     */
    public function checkTextareaFieldConfigurationDataProvider(): iterable
    {
        yield 'Check textarea field configurations.' => [
            'config' => [
                'textarea' => [
                    'identifier' => 'textarea',
                    'languagePath' => 'test-path-for-textfield.xlf:test',
                    'properties' => [
                        'cols' => 40,
                        'default' => 'Default value',
                        'enableRichtext' => true,
                        'max' => 150,
                        'placeholder' => 'Placeholder text',
                        'richtextConfiguration' => 'default',
                        'rows' => 15,
                        'required' => true,
                        'trim' => true,
                    ],
                ],
            ],
            'uniqueColumnName' => 'cb_example_textarea',
            'expected' => [
                'getSql' => '`cb_example_textarea` text',
                'getTca' => [
                    'label' => 'LLL:test-path-for-textfield.xlf:test.label',
                    'description' => 'LLL:test-path-for-textfield.xlf:test.description',
                    'config' => [
                        'type' => 'text',
                        'cols' => 40,
                        'max' => 150,
                        'rows' => 15,
                        'default' => 'Default value',
                        'enableRichtext' => true,
                        'placeholder' => 'Placeholder text',
                        'richtextConfiguration' => 'default',
                        'required' => true,
                    ],
                    'exclude' => 1,
                ],
            ],
        ];
    }

    /**
     * TextareaFieldConfiguration Test
     *
     * @test
     * @dataProvider checkTextareaFieldConfigurationDataProvider
     */
    public function checkTextareaFieldConfiguration(array $config, string $uniqueColumnName, array $expected): void
    {
        // Textareafield test

        $textareaField = new TextareaFieldConfiguration($config['textarea']);
        self::assertSame($expected['getSql'], $textareaField->getSql($uniqueColumnName));

        self::assertSame($expected['getTca'], $textareaField->getTca());
    }
}
