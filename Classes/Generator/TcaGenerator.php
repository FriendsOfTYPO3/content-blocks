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
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class TcaGenerator
{
    public function __construct(
        protected TableDefinitionCollection $tableDefinitionCollection
    ) {
    }

    public function setTca(): array
    {
        foreach ($this->tableDefinitionCollection as $tableName => $tableDefinition) {
            // make sure, TCA entry exists for work with it later
            if (
                !isset($GLOBALS['TCA'][$tableName]) ||
                !is_array($GLOBALS['TCA'][$tableName])
            ) {
                $GLOBALS['TCA'][$tableName]= [
                    'columns' => [],
                ];
            }

            /** @var ContentElementDefinition $typeDefinition */
            foreach ($tableDefinition->getTypeDefinitionCollection() as $typeDefinition) {
                // @todo right now only tt_content elements are supported.
                $tcaColumns = [];
                if ($tableName === 'tt_content') {
                    foreach ($tableDefinition->getTcaColumnsDefinition() as $columnName => $columnFieldDefinition) {
                        $tcaColumns[$columnName] = $columnFieldDefinition->getTca();
                    }
                    $GLOBALS['TCA'][$tableName]['columns'] = array_replace_recursive(
                        $GLOBALS['TCA'][$tableName]['columns'],
                        $tcaColumns
                    );

                    $showItems = self::getTtContentStandardShowItems($tcaColumns);

                    $GLOBALS['TCA'][$tableName]['types'][$typeDefinition->getCType()] = [
                        'previewRenderer' => PreviewRenderer::class,
                        'showitem' => $showItems,
                    ];

                    /***************
                     * Assign Icon
                     */
                    $GLOBALS['TCA'][$tableName]['ctrl']['typeicon_classes'][$typeDefinition->getCType()] = $typeDefinition->getCType();

                    /***************
                     * Add content element to selector list
                     */
                    ExtensionManagementUtility::addTcaSelectItem(
                        'tt_content',
                        'CType',
                        [
                            'LLL:' . $typeDefinition->getPrivatePath() . 'Language' . '/' . 'Labels.xlf:' . $typeDefinition->getVendor()
                            . '.' . $typeDefinition->getPackage() . '.title',
                            $typeDefinition->getCType(),
                            $typeDefinition->getCType(),
                        ],
                        'header',
                        'after'
                    );
                } else {
                    // Collection tables
                    $labelFallback = $typeDefinition->getLabel();

                    foreach ($tableDefinition->getTcaColumnsDefinition() as $columnName => $columnFieldDefinition) {
                        $tcaColumns[$columnName] = $columnFieldDefinition->getTca();
                        if ($labelFallback === '' && $columnFieldDefinition->getFieldType()->dataProcessingBehaviour() === 'renderable') {
                            $labelFallback = $columnFieldDefinition->getName();
                        }
                    }
                    $GLOBALS['TCA'][$tableName] = self::getCollectionTableStandardTca($tcaColumns, $tableName, $labelFallback);
                    $GLOBALS['TCA'][$tableName]['columns'] = array_replace_recursive(
                        $GLOBALS['TCA'][$tableName]['columns'],
                        $tcaColumns
                    );
                }
            }

        }

        // return TCA config for testing
        return $GLOBALS['TCA'];
    }

    public static function getTtContentStandardShowItems(array $columns): string
    {
        $columnNames = array_keys($columns);
        $ttContentShowitemFields = implode(',', $columnNames);

        $enableLayoutOptions = (bool)GeneralUtility::makeInstance(ExtensionConfiguration::class)
            ->get('content_blocks', 'enableLayoutOptions');

        return '
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                --palette--;;general,
                header,' . $ttContentShowitemFields . ',
                content_block,
            --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,' . (($enableLayoutOptions) ? '
                --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.frames;frames,' : '') . '
                --palette--;;appearanceLinks,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
                --palette--;;language,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                --palette--;;hidden,
                --palette--;;access,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,
                categories,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,
                rowDescription,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended,
        ';
    }

    public static function getCollectionTableStandardShowItems(array $columns): string
    {
        $columnNames = array_keys($columns);
        $showitemFields = implode(',', $columnNames);
        return $showitemFields . ', --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.visibility;visibility, --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.access;access, --palette--;;hiddenLanguagePalette,';
    }

    public static function getCollectionTableStandardTca(array $columns, string $table, string $labelField): array
    {
        // @todo what is EXT:lang?
        if (ExtensionManagementUtility::isLoaded('lang')) {
            $generalLanguageFile = 'EXT:lang/Resources/Private/Language/locallang_general.xlf';
        } else {
            $generalLanguageFile = 'EXT:core/Resources/Private/Language/locallang_general.xlf';
        }
        return [
            'ctrl' => [
                'label' => $labelField,
                // 'label_alt' => 'test_label_alt',
                'sortby' => 'sorting',
                'tstamp' => 'tstamp',
                'crdate' => 'crdate',
                'cruser_id' => 'cruser_id',
                'title' => 'This is a TEST CTA',
                'delete' => 'deleted',
                'versioningWS' => true,
                'origUid' => 't3_origuid',
                'hideTable' => true,
                'hideAtCopy' => true,
                'prependAtCopy' => 'LLL:' . $generalLanguageFile . ':LGL.prependAtCopy',
                'transOrigPointerField' => 'l10n_parent',
                'transOrigDiffSourceField' => 'l10n_diffsource',
                'languageField' => 'sys_language_uid',
                'enablecolumns' => [
                    'disabled' => 'hidden',
                    'starttime' => 'starttime',
                    'endtime' => 'endtime',
                ],
                'typeicon_classes' => [
                    'default' => 'ext-content_blocks',
                ],
                'security' => [
                    'ignorePageTypeRestriction' => true,
                ],
            ],
            'interface' => [
                'showRecordFieldList' => '
                    hidden
                ',
            ],
            'types' => [
                '1' => [
                    'showitem' => self::getCollectionTableStandardShowItems($columns),
                ],
            ],
            'palettes' => [
                '1' => [
                    'showitem' => ''
                ],
            ],
            'columns' => [
                'hidden' => [
                    'exclude' => true,
                    'label' => 'LLL:' . $generalLanguageFile . ':LGL.hidden',
                    'config' => [
                        'type' => 'check',
                        'items' => [
                            '1' => [
                                '0' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:hidden.I.0'
                            ]
                        ]
                    ]
                ],
                'starttime' => [
                    'exclude' => true,
                    'label' => 'LLL:' . $generalLanguageFile . ':LGL.starttime',
                    'config' => [
                        'type' => 'input',
                        'renderType' => 'inputDateTime',
                        'eval' => 'datetime',
                        'default' => 0
                    ],
                    'l10n_mode' => 'exclude',
                    'l10n_display' => 'defaultAsReadonly'
                ],
                'endtime' => [
                    'exclude' => true,
                    'label' => 'LLL:' . $generalLanguageFile . ':LGL.endtime',
                    'config' => [
                        'type' => 'input',
                        'renderType' => 'inputDateTime',
                        'eval' => 'datetime',
                        'default' => 0,
                        'range' => [
                            'upper' => mktime(0, 0, 0, 1, 1, 2038)
                        ]
                    ],
                    'l10n_mode' => 'exclude',
                    'l10n_display' => 'defaultAsReadonly'
                ],
                'sys_language_uid' => [
                    'exclude' => 1,
                    'label' => 'LLL:' . $generalLanguageFile . ':LGL.language',
                    'config' => [
                        'type' => 'language',
                    ]
                ],
                'l10n_parent' => [
                    'displayCond' => 'FIELD:sys_language_uid:>:0',
                    'exclude' => 1,
                    'label' => 'LLL:' . $generalLanguageFile . ':LGL.l18n_parent',
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
            ]
        ];
    }
}
