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
                            [
                                'identifier' => 'textarea',
                                'type' => 'Textarea',
                            ],
                            [
                                'identifier' => 'number',
                                'type' => 'Number',
                            ],
                            [
                                'identifier' => 'email',
                                'type' => 'Email',
                            ],
                            [
                                'identifier' => 'check',
                                'type' => 'Checkbox',
                            ],
                            [
                                'identifier' => 'color',
                                'type' => 'Color',
                            ],
                            [
                                'identifier' => 'file',
                                'type' => 'File',
                                'properties' => [
                                    'enableImageManipulation' => 0,
                                ]
                            ],
                            [
                                'identifier' => 'collection',
                                'type' => 'Collection',
                                'properties' => [
                                    'fields' => [
                                        [
                                            'identifier' => 'text',
                                            'type' => 'Text',
                                        ]
                                    ]
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
                            'showitem' => '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,--palette--;;general,header,t3ce_example_text,t3ce_example_textarea,t3ce_example_number,t3ce_example_email,t3ce_example_check,t3ce_example_color,t3ce_example_file,t3ce_example_collection,--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.frames;frames,--palette--;;appearanceLinks,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,--palette--;;language,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,--palette--;;hidden,--palette--;;access,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,categories,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,rowDescription,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended',
                            'previewRenderer' => PreviewRenderer::class,
                        ],
                        't3ce_testblock' => [
                            'showitem' => '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,--palette--;;general,header,t3ce_testblock_text,--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.frames;frames,--palette--;;appearanceLinks,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,--palette--;;language,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,--palette--;;hidden,--palette--;;access,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,categories,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,rowDescription,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended',
                            'previewRenderer' => PreviewRenderer::class,
                        ],
                    ],
                    'columns' => [
                        't3ce_example_text' => [
                            'label' => 'LLL:typo3conf/content-blocks/example/Resources/Private/Language/Labels.xlf:text.label',
                            'description' => 'LLL:typo3conf/content-blocks/example/Resources/Private/Language/Labels.xlf:text.description',
                            'config' => [
                                'type' => 'input',
                                'default' => 'Default value',
                                'placeholder' => 'Placeholder text',
                            ],
                            'exclude' => true,
                        ],
                        't3ce_example_textarea' => [
                            'label' => 'LLL:typo3conf/content-blocks/example/Resources/Private/Language/Labels.xlf:textarea.label',
                            'description' => 'LLL:typo3conf/content-blocks/example/Resources/Private/Language/Labels.xlf:textarea.description',
                            'config' => [
                                'type' => 'text',
                            ],
                            'exclude' => true,
                        ],
                        't3ce_example_number' => [
                            'label' => 'LLL:typo3conf/content-blocks/example/Resources/Private/Language/Labels.xlf:number.label',
                            'description' => 'LLL:typo3conf/content-blocks/example/Resources/Private/Language/Labels.xlf:number.description',
                            'config' => [
                                'type' => 'number',
                            ],
                            'exclude' => true,
                        ],
                        't3ce_example_email' => [
                            'label' => 'LLL:typo3conf/content-blocks/example/Resources/Private/Language/Labels.xlf:email.label',
                            'description' => 'LLL:typo3conf/content-blocks/example/Resources/Private/Language/Labels.xlf:email.description',
                            'config' => [
                                'type' => 'email',
                            ],
                            'exclude' => true,
                        ],
                        't3ce_example_check' => [
                            'label' => 'LLL:typo3conf/content-blocks/example/Resources/Private/Language/Labels.xlf:check.label',
                            'description' => 'LLL:typo3conf/content-blocks/example/Resources/Private/Language/Labels.xlf:check.description',
                            'config' => [
                                'type' => 'check',
                            ],
                            'exclude' => true,
                        ],
                        't3ce_example_color' => [
                            'label' => 'LLL:typo3conf/content-blocks/example/Resources/Private/Language/Labels.xlf:color.label',
                            'description' => 'LLL:typo3conf/content-blocks/example/Resources/Private/Language/Labels.xlf:color.description',
                            'config' => [
                                'type' => 'color',
                            ],
                            'exclude' => true,
                        ],
                        't3ce_example_file' => [
                            'label' => 'LLL:typo3conf/content-blocks/example/Resources/Private/Language/Labels.xlf:file.label',
                            'description' => 'LLL:typo3conf/content-blocks/example/Resources/Private/Language/Labels.xlf:file.description',
                            'config' => [
                                'type' => 'file',
                                'foreign_table' => 'sys_file_reference',
                                'foreign_field' => 'uid_foreign',
                                'foreign_sortby' => 'sorting_foreign',
                                'foreign_table_field' => 'tablenames',
                                'foreign_match_fields' => [
                                    'fieldname' => 't3ce_example_file',
                                ],
                                'foreign_label' => 'uid_local',
                                'foreign_selector' => 'uid_local',
                            ],
                            'exclude' => true,
                        ],
                        't3ce_example_collection' => [
                            'label' => 'LLL:typo3conf/content-blocks/example/Resources/Private/Language/Labels.xlf:collection.label',
                            'description' => 'LLL:typo3conf/content-blocks/example/Resources/Private/Language/Labels.xlf:collection.description',
                            'config' => [
                                'type' => 'inline',
                                'foreign_table' => 't3ce_example_collection',
                                'foreign_field' => 'foreign_table_parent_uid',
                            ],
                            'exclude' => true,
                        ],
                        't3ce_testblock_text' => [
                            'label' => 'LLL:typo3conf/content-blocks/testblock/Resources/Private/Language/Labels.xlf:text.label',
                            'description' => 'LLL:typo3conf/content-blocks/testblock/Resources/Private/Language/Labels.xlf:text.description',
                            'config' => [
                                'type' => 'input',
                            ],
                            'exclude' => true,
                        ],
                    ],
                ],
                't3ce_example_collection' => [
                    'ctrl' => [
                        'label' => 'text',
                        'sortby' => 'sorting',
                        'tstamp' => 'tstamp',
                        'crdate' => 'crdate',
                        'delete' => 'deleted',
                        'editlock' => 'editlock',
                        'versioningWS' => true,
                        'origUid' => 't3_origuid',
                        'hideTable' => true,
                        'transOrigPointerField' => 'l10n_parent',
                        'translationSource' => 'l10n_source',
                        'transOrigDiffSourceField' => 'l10n_diffsource',
                        'languageField' => 'sys_language_uid',
                        'enablecolumns' => [
                            'disabled' => 'hidden',
                            'starttime' => 'starttime',
                            'endtime' => 'endtime',
                            'fe_group' => 'fe_group',
                        ],
                        'typeicon_classes' => [
                            'default' => 'ext-content_blocks',
                        ],
                        'security' => [
                            'ignorePageTypeRestriction' => true,
                        ],

                    ],
                    'types' => [
                        '1' => [
                            'showitem' => '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,text,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,--palette--;;language,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,--palette--;;hidden,--palette--;;access',
                        ],
                    ],
                    'palettes' => [
                        'language' => [
                            'showitem' => '
                        sys_language_uid;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:sys_language_uid_formlabel,l18n_parent
                    ',
                        ],
                        'hidden' => [
                            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.visibility',
                            'showitem' => 'hidden',
                        ],
                        'access' => [
                            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.access',
                            'showitem' => '
                        starttime;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:starttime_formlabel,
                        endtime;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:endtime_formlabel,
                        --linebreak--,
                        fe_group;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:fe_group_formlabel,
                        --linebreak--,editlock
                    ',
                        ],
                    ],
                    'columns' => [
                        'editlock' => [
                            'exclude' => true,
                            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:editlock',
                            'config' => [
                                'type' => 'check',
                                'renderType' => 'checkboxToggle',
                            ],
                        ],
                        'hidden' => [
                            'exclude' => true,
                            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.disable',
                            'config' => [
                                'type' => 'check',
                                'renderType' => 'checkboxToggle',
                            ],
                        ],
                        'fe_group' => [
                            'exclude' => true,
                            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.fe_group',
                            'config' => [
                                'type' => 'select',
                                'renderType' => 'selectMultipleSideBySide',
                                'size' => 5,
                                'maxitems' => 20,
                                'items' => [
                                    [
                                        'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hide_at_login',
                                        -1,
                                    ],
                                    [
                                        'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.any_login',
                                        -2,
                                    ],
                                    [
                                        'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.usergroups',
                                        '--div--',
                                    ],
                                ],
                                'exclusiveKeys' => '-1,-2',
                                'foreign_table' => 'fe_groups',
                            ],
                        ],
                        'starttime' => [
                            'exclude' => true,
                            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
                            'config' => [
                                'type' => 'datetime',
                                'default' => 0,
                            ],
                            'l10n_mode' => 'exclude',
                            'l10n_display' => 'defaultAsReadonly',
                        ],
                        'endtime' => [
                            'exclude' => true,
                            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
                            'config' => [
                                'type' => 'datetime',
                                'default' => 0,
                                'range' => [
                                    'upper' => mktime(0, 0, 0, 1, 1, 2038),
                                ],
                            ],
                            'l10n_mode' => 'exclude',
                            'l10n_display' => 'defaultAsReadonly',
                        ],
                        'sys_language_uid' => [
                            'exclude' => true,
                            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
                            'config' => [
                                'type' => 'language',
                            ]
                        ],
                        'l10n_parent' => [
                            'displayCond' => 'FIELD:sys_language_uid:>:0',
                            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
                            'config' => [
                                'type' => 'select',
                                'renderType' => 'selectSingle',
                                'items' => [
                                    [
                                        '',
                                        0
                                    ]
                                ],
                                'foreign_table' => 't3ce_example_collection',
                                'foreign_table_where' => 'AND t3ce_example_collection.pid=###CURRENT_PID### AND t3ce_example_collection.sys_language_uid IN (-1,0)',
                                'default' => 0
                            ]
                        ],
                        'l10n_diffsource' => [
                            'config' => [
                                'type' => 'passthrough'
                            ]
                        ],
                        'sorting' => [
                            'config' => [
                                'type' => 'passthrough',
                            ],
                        ],
                        'foreign_table_parent_uid' => [
                            'config' => [
                                'type' => 'passthrough'
                            ]
                        ],
                        'text' => [
                            'exclude' => true,
                            'label' => 'LLL:typo3conf/content-blocks/example/Resources/Private/Language/Labels.xlf:collection.text.label',
                            'description' => 'LLL:typo3conf/content-blocks/example/Resources/Private/Language/Labels.xlf:collection.text.description',
                            'config' => [
                                'type' => 'input',
                            ],
                        ],
                    ],
                ],
            ]
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

        $tca = $tcaGenerator->generate();

        self::assertEquals($expected, $tca);
    }
}
