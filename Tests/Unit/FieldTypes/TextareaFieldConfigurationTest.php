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
            'contentBlock' => [
                'EditorInterfaceXlf' => 'typo3conf/contentBlocks/example/src/Language/EditorInterface.xlf',
                'vendor' => 'typo3-contentblocks',
                'package' => 'example',
            ],
            'fieldsList' => [
                'textarea' => [
                    'identifier' => 'textarea',
                    'type' => 'Textarea',
                    'properties' => [
                        'cols' => 40,
                        'default' => 'Default value',
                        'enableRichtext' => true,
                        'max' => 150,
                        'placeholder' => 'Placeholder text',
                        'richtextConfiguration' => 'default',
                        'rows' => 15,
                        'required' => false,
                        'trim' => true,
                    ],
                    '_path' => [],
                    '_identifier' => 'textarea',
                ],
            ],
            'uniqueColumnName' => 'cb_example_textarea',
            'expected' => [
                'getSql' => '`cb_example_textarea` text',
                'construct' => [
                    'identifier' => 'textarea',
                    'type' => 'Textarea',
                    'properties' => [
                        'cols' => 40,
                        'default' => 'Default value',
                        'enableRichtext' => true,
                        'max' => 150,
                        'placeholder' => 'Placeholder text',
                        'richtextConfiguration' => 'default',
                        'rows' => 15,
                        'required' => false,
                        'trim' => true,
                    ],
                    '_path' => [],
                    '_identifier' => 'textarea',
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
    public function checkTextareaFieldConfiguration(array $contentBlock, array $fieldsList, string $uniqueColumnName, array $expected): void
    {
        // Textareafield test

        $textareaField = new TextareaFieldConfiguration($fieldsList['textarea']);
        self::assertSame($expected['getSql'], $textareaField->getSql($uniqueColumnName));

        self::assertSame($expected['construct'], $textareaField->toArray());
    }
}
