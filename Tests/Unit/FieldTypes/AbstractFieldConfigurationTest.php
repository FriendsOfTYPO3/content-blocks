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

use TYPO3\CMS\ContentBlocks\FieldConfiguration\AbstractFieldConfiguration;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class AbstractFieldConfigurationTest extends UnitTestCase
{
    /**
     * dataprovider for checking AbstractFieldConfiguration
     */
    public function checkAbstractFieldConfigurationDataProvider(): iterable
    {
        yield 'Check abstract field configurations.' => [
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
            'expected' => [
                'create' => 'text', // Create result (create from array via construct)
                'getTcaTemplate' => [ // Check getTcaTemplate
                    'exclude' => 1,
                    'label' => 'LLL:typo3conf/contentBlocks/example/src/Language/EditorInterface.xlf:typo3-contentblocks.example.text.label',
                    'description' => 'LLL:typo3conf/contentBlocks/example/src/Language/EditorInterface.xlf:typo3-contentblocks.example.text.description',
                    'config' => [],
                ],
            ],
        ];
    }

    /**
     * AbstractFieldConfiguration Test
     *
     * @test
     * @dataProvider checkAbstractFieldConfigurationDataProvider
     */
    public function checkAbstractFieldConfiguration(array $contentBlock, array $fieldsList, array $expected): void
    {
        // AbstractFieldConfiguration Test

        $fieldsConfig = new AbstractFieldConfiguration($fieldsList['text']);
        self::assertSame($expected['create'], $fieldsConfig->identifier);

        self::assertSame($expected['getTcaTemplate'], $fieldsConfig->getTcaTemplate($contentBlock));
    }
}
