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

use TYPO3\CMS\ContentBlocks\Backend\Preview\PreviewRenderer;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\ContentBlocks\Generator\TcaGenerator;
use TYPO3\CMS\Core\Configuration\Event\AfterTcaCompilationEvent;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class TcaGeneratorTest extends UnitTestCase
{
    public function checkTcaFieldTypesDataProvider(): iterable
    {
        yield 'Two simple content block create two types and two columns in tt_content table' => [
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
                                    'default' => 'Default value',
                                    'placeholder' => 'Placeholder text',
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'composerJson' => [
                        'name' => 't3ce/testblock',
                    ],
                    'icon' => '',
                    'iconProvider' => '',
                    'yaml' => [
                        'fields' => [
                            [
                                'identifier' => 'text',
                                'type' => 'Text',
                                'properties' => [
                                    'default' => '',
                                    'placeholder' => '',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'expected' => [
                'tt_content' => [
                    'ctrl' => [
                        'typeicon_classes' => [
                            't3ce_example' => 't3ce_example',
                            't3ce_testblock' => 't3ce_testblock',
                        ],
                    ],
                    'types' => [
                        't3ce_example' => [
                            'showitem' => '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,--palette--;;general,header,cb_t3ce_example_text,--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.frames;frames,--palette--;;appearanceLinks,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,--palette--;;language,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,--palette--;;hidden,--palette--;;access,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,categories,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,rowDescription,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended',
                            'previewRenderer' => PreviewRenderer::class,
                        ],
                        't3ce_testblock' => [
                            'showitem' => '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,--palette--;;general,header,cb_t3ce_testblock_text,--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.frames;frames,--palette--;;appearanceLinks,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,--palette--;;language,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,--palette--;;hidden,--palette--;;access,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,categories,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,rowDescription,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended',
                            'previewRenderer' => PreviewRenderer::class,
                        ],
                    ],
                    'columns' => [
                        'cb_t3ce_example_text' => [
                            'label' => 'LLL:' . Environment::getProjectPath() . '/typo3conf/content-blocks/example/Resources/Private/Language/Labels.xlf:text.label',
                            'description' => 'LLL:' . Environment::getProjectPath() . '/typo3conf/content-blocks/example/Resources/Private/Language/Labels.xlf:text.description',
                            'config' => [
                                'type' => 'input',
                                'default' => 'Default value',
                                'placeholder' => 'Placeholder text',
                            ],
                            'exclude' => true,
                        ],
                        'cb_t3ce_testblock_text' => [
                            'label' => 'LLL:' . Environment::getProjectPath() . '/typo3conf/content-blocks/testblock/Resources/Private/Language/Labels.xlf:text.label',
                            'description' => 'LLL:' . Environment::getProjectPath() . '/typo3conf/content-blocks/testblock/Resources/Private/Language/Labels.xlf:text.description',
                            'config' => [
                                'type' => 'input',
                            ],
                            'exclude' => true,
                        ],
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
        $tcaGenerator = new TcaGenerator($tableDefinitionCollection);
        $afterTcaCompilationEvent = new AfterTcaCompilationEvent([]);

        $tcaGenerator->generate($afterTcaCompilationEvent);

        self::assertEquals($expected, $afterTcaCompilationEvent->getTca());
    }
}
