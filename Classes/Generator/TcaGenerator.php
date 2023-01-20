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

namespace TYPO3\CMS\ContentBlocks\Generator;

use TYPO3\CMS\ContentBlocks\Backend\Preview\PreviewRenderer;
use TYPO3\CMS\ContentBlocks\Definition\ContentElementDefinition;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\ContentBlocks\Definition\TcaColumnsDefinition;
use TYPO3\CMS\Core\Configuration\Event\AfterTcaCompilationEvent;
use TYPO3\CMS\Core\Preparations\TcaPreparation;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class TcaGenerator
{
    public function __construct(
        protected TableDefinitionCollection $tableDefinitionCollection
    ) {
    }

    public function __invoke(AfterTcaCompilationEvent $event): void
    {
        $event->setTca(array_replace_recursive($event->getTca(), $this->generate()));
    }

    public function generate(): array
    {
        $tca = [];
        foreach ($this->tableDefinitionCollection as $tableName => $tableDefinition) {
            if ($this->tableDefinitionCollection->isCustomTable($tableDefinition)) {
                $labelFallback = '';
                // Use first field as label.
                foreach ($tableDefinition->getTcaColumnsDefinition() as $columnFieldDefinition) {
                    $labelFallback = $columnFieldDefinition->getUniqueIdentifier();
                    break;
                }
                $tca[$tableName] = $this->getCollectionTableStandardTca($tableDefinition->getTcaColumnsDefinition(), $tableName, $labelFallback);
            }
            foreach ($tableDefinition->getTcaColumnsDefinition() as $column) {
                $tca[$tableName]['columns'][$column->getUniqueIdentifier()] = $column->getTca();
            }
            foreach ($tableDefinition->getTypeDefinitionCollection() ?? [] as $typeDefinition) {
                // @todo right now only tt_content elements are supported.
                if ($typeDefinition instanceof ContentElementDefinition) {
                    $tca[$tableName]['types'][$typeDefinition->getCType()] = [
                        'previewRenderer' => PreviewRenderer::class,
                        'showitem' => $this->getTtContentStandardShowItem($typeDefinition->getColumns()),
                    ];
                    $tca[$tableName]['ctrl']['typeicon_classes'][$typeDefinition->getCType()] = $typeDefinition->getCType();
                }
            }
        }
        return GeneralUtility::makeInstance(TcaPreparation::class)->prepare($tca);
    }

    protected function getTtContentStandardShowItem(array $columns): string
    {
        $parts = [
            '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general',
            '--palette--;;general',
            'header',
            implode(',', $columns),
            '--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance',
            '--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.frames;frames',
            '--palette--;;appearanceLinks',
            '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language',
            '--palette--;;language',
            '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access',
            '--palette--;;hidden',
            '--palette--;;access',
            '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories',
            'categories',
            '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes',
            'rowDescription',
            '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended',
        ];

        return implode(',', $parts);
    }

    protected function getCollectionTableStandardShowItems(TcaColumnsDefinition $columns): string
    {
        $generalTab = '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,';
        $appendLanguageTab = ',--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,--palette--;;language';
        $appendAccessTab = ',--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,--palette--;;hidden,--palette--;;access';
        return $generalTab . implode(',', $columns->getKeys()) . $appendLanguageTab . $appendAccessTab;
    }

    protected function getCollectionTableStandardTca(TcaColumnsDefinition $columns, string $table, string $labelField): array
    {
        return [
            'ctrl' => [
                'label' => $labelField,
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
                    'default' => 'content-blocks',
                ],
                'security' => [
                    'ignorePageTypeRestriction' => true,
                ],

            ],
            'types' => [
                '1' => [
                    'showitem' => $this->getCollectionTableStandardShowItems($columns),
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
                        'foreign_table' => $table,
                        'foreign_table_where' => 'AND ' . $table . '.pid=###CURRENT_PID### AND ' . $table . '.sys_language_uid IN (-1,0)',
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
                ]
            ]
        ];
    }
}
