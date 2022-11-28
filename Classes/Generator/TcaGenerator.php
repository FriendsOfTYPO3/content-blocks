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
use TYPO3\CMS\ContentBlocks\Domain\Model\ContentBlockConfiguration;
use TYPO3\CMS\ContentBlocks\Domain\Repository\ContentBlockConfigurationRepository;
use TYPO3\CMS\ContentBlocks\FieldConfiguration\AbstractFieldConfiguration;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class TcaGenerator
{
    /**
     * @var ContentBlockConfigurationRepository
     */
    protected $cbConfigRepository;

    public function __construct(
        ContentBlockConfigurationRepository $cbConfigRepository
    ) {
        $this->cbConfigRepository = $cbConfigRepository;
    }

    /**
     * Manages to set up the TCA for each ContentBlock
     *
     * $contentBlocksConfig as method param mainly for unti test purposes.
     *
     * @param array $contentBlocksConfig    configuration of all ContentBlocks
     */
    public function setTca(array $contentBlocksConfig = null): bool
    {
        if ($contentBlocksConfig === null) {
            $contentBlocksConfig = $this->cbConfigRepository->findAll();
        }

        /** @var ContentBlockConfiguration $contentBlock */
        foreach ($contentBlocksConfig as $contentBlock) {
            /***************
             * Add Content Element
             */
            if (
                !isset($GLOBALS['TCA']['tt_content']['types'][$contentBlock->getCType()]) ||
                !is_array($GLOBALS['TCA']['tt_content']['types'][$contentBlock->getCType()])
            ) {
                $GLOBALS['TCA']['tt_content']['types'][$contentBlock->getCType()] = [];
            }

            // PreviewRenderer
            $GLOBALS['TCA']['tt_content']['types'][$contentBlock->getCType()]['previewRenderer'] = PreviewRenderer::class;

            /***************
             * Assign Icon
             */
            $GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes'][$contentBlock->getCType()] = $contentBlock->getCType();


            /***************
             * Add content element to selector list
             */
            ExtensionManagementUtility::addTcaSelectItem(
                'tt_content',
                'CType',
                [
                    'LLL:' . $contentBlock->editorLLL . ':' . $contentBlock->vendor
                    . '.' . $contentBlock->package . '.title',
                    $contentBlock->getCType(),
                    $contentBlock->getCType(),
                ],
                'header',
                'after'
            );

            /***************
             * Add columns to table TCA of tt_content and tx_contentblocks_reg_api_collection
             */
            $ttContentShowitemFields = '';
            $ttContentColumns = [];
            $ttContentColumnsOverrides = [];
            $collectionColumns = [];
            if (is_array($contentBlock->fieldsConfig)
                && count($contentBlock->fieldsConfig) > 0
            ) {
                $fieldsList = $contentBlock->fieldsConfig;
                /** @var AbstractFieldConfiguration $field */
                foreach ($fieldsList as $field) {
                    $tempUniqueColumnName = $field->uniqueColumnName($contentBlock->getCType(), $field->uniqueIdentifier);

                    // Add fields to tt_content (first level)
                    if (isset($field->uniqueIdentifier) && isset($field->type ) && count($field->path) == 1) {
                        // re-use existing
                        if (
                            isset($field->useExistingField)
                            && $field->useExistingField === true
                            // check if there is a column configuration
                            && array_key_exists($field->identifier, $GLOBALS['TCA']['tt_content']['columns'])
                        ) {
                            // @todo: make use existing field usable
                            $ttContentShowitemFields .= "\n" . $field->identifier . ',';
                            // $newConfigForExistingElement = $this->tcaFieldService->getMatchedTcaConfig($contentBlock, $field);

                            // this is not allowed: https://docs.typo3.org/m/typo3/reference-tca/main/en-us/Types/Properties/ColumnsOverrides.html
                            // unset($newConfigForExistingElement['config']['type']);

                            // if exclude is set, this leads to a unexpected user rights behaviour (e. g. bodytext is not available in cb)
                            // https://docs.typo3.org/m/typo3/reference-tca/main/en-us/Columns/Properties/Exclude.html?highlight=excl
                            // unset($newConfigForExistingElement['exclude']);

                            // $ttContentColumnsOverrides[$field->identifier] = $newConfigForExistingElement;
                        } else {
                            // The "normal" way to add fields
                            // @todo: add normal columns
                            $ttContentShowitemFields .= "\n" . $tempUniqueColumnName . ',';
                            // $ttContentColumns[$tempUniqueColumnName] = $this->tcaFieldService->getMatchedTcaConfig($contentBlock, $field);
                        }
                    }

                    // Add collection fields
                    // @todo: add collections
                    /* elseif (
                        isset($field['_identifier'])
                        && isset($field['type'])
                        && count($field['_path']) > 1
                        && (
                            !isset($field['properties']['useExistingField'])
                            || $field['properties']['useExistingField'] === false
                            || !array_key_exists($field['identifier'], $GLOBALS['TCA'][Constants::COLLECTION_FOREIGN_TABLE]['columns'])
                        )
                    ) {
                        $collectionColumns[$tempUniqueColumnName] = $this->tcaFieldService->getMatchedTcaConfig($contentBlock, $field);
                        // TODO: else throw usefull exeption if not supported
                    } */
                }
            }
            $GLOBALS['TCA']['tt_content']['columns'] = array_replace_recursive(
                $GLOBALS['TCA']['tt_content']['columns'],
                $ttContentColumns
            );

            // @todo: add collection tables
            /* $GLOBALS['TCA'][Constants::COLLECTION_FOREIGN_TABLE]['columns'] = array_replace_recursive(
                $GLOBALS['TCA'][Constants::COLLECTION_FOREIGN_TABLE]['columns'],
                $collectionColumns
            ); */

            // 2022-11-12 FIX: TYPO3\CMS\Backend\Form\FormDataGroup\OrderedProviderList->copile([]) removes collection columns in frontend_editing,
            // if we do not add them to [TCA]['tx_contentblocks_reg_api_collection']['types']['1']['showitem']. It does not process the OverridesChildTca in tt_content.
            // @todo: check if new collection definition works
            // if (count($collectionColumns) > 0) {
            //     $collectionCollumnKeys = array_keys($collectionColumns);
            //     $GLOBALS['TCA'][Constants::COLLECTION_FOREIGN_TABLE]['types']['1']['showitem'] = implode(',', $collectionCollumnKeys) . ',' . $GLOBALS['TCA'][Constants::COLLECTION_FOREIGN_TABLE]['types']['1']['showitem'];
            // }

            /***************
             * Configure element type
             */
            // Feature: enable pallette frame via extConf
            $enableLayoutOptions = (bool)GeneralUtility::makeInstance(ExtensionConfiguration::class)
                            ->get('content_blocks', 'enableLayoutOptions');

            $GLOBALS['TCA']['tt_content']['types'][$contentBlock->getCType() ] = array_replace_recursive(
                $GLOBALS['TCA']['tt_content']['types'][$contentBlock->getCType()],
                [
                    'showitem' => '
                        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                            --palette--;;general,
                            header,' . $ttContentShowitemFields . '
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
                    ',
                ]
            );

            // Feature, reuse an existing field: overwrite the column config for this cType
            // if (count($ttContentColumnsOverrides) > 0) {
            //     $GLOBALS['TCA']['tt_content']['types'][$contentBlock['CType']]['columnsOverrides'] = $ttContentColumnsOverrides;
            // }


        }

        return true;
    }
}
