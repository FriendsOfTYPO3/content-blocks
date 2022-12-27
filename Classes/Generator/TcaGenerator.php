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
use TYPO3\CMS\ContentBlocks\CodeGenerator\TcaCodeGenerator;
use TYPO3\CMS\ContentBlocks\Definition\ContentElementDefinition;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinition;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\ContentBlocks\Definition\TypeDefinition;
use TYPO3\CMS\ContentBlocks\Domain\Repository\ContentBlockConfigurationRepository;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

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
     * @param TableDefinitionCollection $contentBlocksConfig    configuration of all ContentBlocks
     */
    public function setTca(TableDefinitionCollection $contentBlocksConfig = null): array
    {
        if ($contentBlocksConfig === null) {
            /** @var TableDefinitionCollection $contentBlocksConfig */
            $contentBlocksConfig = $this->cbConfigRepository->findAll();
        }

        /** @var TableDefinition $tableDefinition */
        foreach ($contentBlocksConfig as $tableName => $tableDefinition) {
            // make sure, TCA entry exists for work with it later
            if (
                !isset($GLOBALS['TCA'][$tableName]) ||
                !is_array($GLOBALS['TCA'][$tableName])
            ) {
                $GLOBALS['TCA'][$tableName]= [
                    'columns' => [],
                ];
            }

            /***************
             * Add Content Element
             */
            /** @var TypeDefinition|ContentElementDefinition $typeDefinition */
            foreach ($tableDefinition->getTypeDefinitionCollection() as $typeDefinition) {

                if ($tableName == 'tt_content') {
                    $tcaColumns = [];
                    foreach ($tableDefinition->getTcaColumnsDefinition() as $columnName => $columnFieldDefinition) {
                        $tcaColumns[$columnName] = $columnFieldDefinition->getTca();
                    }
                    $GLOBALS['TCA'][$tableName]['columns'] = array_replace_recursive(
                        $GLOBALS['TCA'][$tableName]['columns'],
                        $tcaColumns
                    );

                    $showItems = TcaCodeGenerator::getTtContentStandardShowItems($tcaColumns);

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
                    $tcaColumns = [];
                    $labelFallback = $typeDefinition->getLabel();

                    foreach ($tableDefinition->getTcaColumnsDefinition() as $columnName => $columnFieldDefinition) {
                        $tcaColumns[$columnName] = $columnFieldDefinition->getTca();
                        if ($labelFallback === '' && $columnFieldDefinition->getFieldType()->dataProcessingBehaviour() === 'renderable') {
                            $labelFallback = $columnFieldDefinition->getName();
                        }
                    }
                    $GLOBALS['TCA'][$tableName] = TcaCodeGenerator::getCollectionTableStandardTca($tcaColumns, $tableName, $labelFallback);
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
}
