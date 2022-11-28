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

namespace TYPO3\CMS\ContentBlocks\Factory;

use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\ContentBlocks\Service\ConfigurationService;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class DefinitionFactory implements SingletonInterface
{
    /**
     * Create a TableDefinitionCollection and everything what comes with it
     * due to the given ContentBlockConfigurations.
     *
     * @param array $contentBlocks
     */
    public static function createAll(array $contentBlocks): TableDefinitionCollection
    {
        $preparedData = [];
        /** @param ConfigurationService */
        $configurationService = GeneralUtility::makeInstance(ConfigurationService::class);
        $collectionTablePrefix = $configurationService->getDatabaseCollectionTablePrefix();

        // tableDefinitionTemplate
        $tableDefinitionTemplate = [
            'sql' => [],
            'tca' => [],
            // elements: basics for contentElement or inline table
            'elements' => [],
            'palettes' => [],
        ];

        // tt_content data has to be collected and merged from all ContentBlocks
        $tt_contentData = $tableDefinitionTemplate;

        foreach ($contentBlocks as $cbConf) {
            // @todo: add tt_content fields
            foreach ($cbConf as $identifier => $fieldConfiguration) {
                // @todo: add process functionality
            }
            // NOTE: check useExistingfield
            // @todo: check if is a collection
            // @todo: check collection and add it to the table list
        }

        // add data for tt_content
        $preparedData[] = [
            'table' => 'tt_content',
            'tableConfiguration' => $tt_contentData,
        ];
        return TableDefinitionCollection::createFromArray($preparedData);
    }

}
