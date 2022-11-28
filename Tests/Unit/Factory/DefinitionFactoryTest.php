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

namespace TYPO3\CMS\ContentBlocks\Tests\Unit\Factory;

use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\ContentBlocks\Factory\DefinitionFactory;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class DefinitionFactoryTest extends UnitTestCase
{
    /**
     * dataprovider for checking TCA field types
     */
    public function checkCreateAllDataProvider(): iterable
    {
        /* yield 'Input field are processed correctly' => [
            'config' => [
                'fields' => [
                    [
                        'identifier' => 'text',
                        'type' => 'Text',
                        'properties' => [
                            'autocomplete' => '1',
                            'default' => 'Default value',
                            'max' => '15',
                            'placeholder' => 'Placeholder text',
                            'size' => '20',
                            'required' => '0',
                            'trim' => '1',
                        ],
                    ],
                ],
            ],
            'expected' => [
                [
                    'tt_content' => [
                        'columns' => [],
                        'showItemFields' => '',
                        'columnsOverrides' => [],
                    ],
                    'collections' => [
                        'columns' => [],
                    ],
                ],
            ],
        ]; */

        yield 'Input field are processed correctly' => [
            'config' => [
                'fields' => [
                    [
                        'identifier' => 'text',
                        'type' => 'Text',
                        'properties' => [
                            'autocomplete' => '1',
                            'default' => 'Default value',
                            'max' => '15',
                            'placeholder' => 'Placeholder text',
                            'size' => '20',
                            'required' => '0',
                            'trim' => '1',
                        ],
                    ],
                ],
            ],
            'expected' => [
                'result' => true,
            ]
        ];
    }

    /**
     * @test
     * @dataProvider checkCreateAllDataProvider
     */
    public function checkCreateAll(array $config, array $expected): void
    {
        // move to data provider, examples:
        // https://github.com/Gernott/mask/blob/main/Tests/Unit/CodeGenerator/TcaCodeGeneratorTest.php

        $definitionFactory = new DefinitionFactory;
        self::assertSame($expected['result'], $definitionFactory->createAll($config) instanceof TableDefinitionCollection);
    }
}
