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

namespace TYPO3\CMS\ContentBlocks\Tests\Unit\Generator;

use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\ContentBlocks\Domain\Repository\ContentBlockBuilder;
use TYPO3\CMS\ContentBlocks\Generator\TcaGenerator;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class TcaGeneratorTest extends UnitTestCase
{
    /**
     * dataprovider for checking TCA field types
     */
    public function checkTcaFieldTypesDataProvider(): iterable
    {
        yield 'Input field are processed correctly' => [
            'contentBlocks' => [
                [
                    'composerJson' => [
                        'name' => 't3ce/example',
                    ],
                    'icon' => '',
                    'iconProvider' => '',
                    'yaml' => [
                        'fields' => [
                            [
                                'identifier' => 'text',
                                'type' => 'Text',
                                'properties' => [
                                    'autocomplete' => 1,
                                    'default' => 'Default value',
                                    'max' => 15,
                                    'placeholder' => 'Placeholder text',
                                    'size' => 20,
                                    'required' => 0,
                                ],
                            ],
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
        ];
    }

    /**
     * @test
     * @dataProvider checkTcaFieldTypesDataProvider
     */
    public function checkTcaFieldTypes(array $contentBlocks, array $expected): void
    {
        $tableDefinitionCollection = TableDefinitionCollection::createFromArray($contentBlocks);

        $cbConfigRepository = new ContentBlockBuilder();
        $tcaGenerator = new TcaGenerator($cbConfigRepository);
    }
}
