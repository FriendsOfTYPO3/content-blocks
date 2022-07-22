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
            'contentBlock' => [
                'EditorInterfaceXlf' => 'typo3conf/contentBlocks/example/src/Language/EditorInterface.xlf',
                'vendor' => 'typo3-contentblocks',
                'package' => 'example',
            ],
            'fieldsList' => [
                'text' => [
                    'identifier' => 'text',
                    'type' => 'Text',
                    'properties' => [
                        'autocomplete' => true,
                        'default' => 'Default value',
                        'max' => 15,
                        'placeholder' => 'Placeholder text',
                        'size' => 20,
                        'required' => false,
                        'trim' => true,
                    ],
                    '_path' => [],
                    '_identifier' => 'text',
                ],
            ],

            'uniqueColumnName' => 'cb_example_text',
            'expected' => [
                'getSql' => '`cb_example_text` VARCHAR(20) DEFAULT \'\' NOT NULL',
                'construct' => [
                    'identifier' => 'text',
                    'type' => 'Text',
                    'properties' => [
                        'autocomplete' => true,
                        'default' => 'Default value',
                        'max' => 15,
                        'placeholder' => 'Placeholder text',
                        'size' => 20,
                        'required' => false,
                        'trim' => true,
                    ],
                    '_path' => [],
                    '_identifier' => 'text',
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
    public function checkInputFieldConfiguration(array $contentBlock, array $fieldsList, string $uniqueColumnName, array $expected): void
    {
        // Inputfield test

        $inputfield = new InputFieldConfiguration($fieldsList['text']);
        self::assertSame($expected['getSql'], $inputfield->getSql($uniqueColumnName));

        self::assertSame($expected['construct'], $inputfield->toArray());
    }
}
