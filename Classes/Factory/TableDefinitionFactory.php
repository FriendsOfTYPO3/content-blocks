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
use TYPO3\CMS\ContentBlocks\Domain\Model\ContentBlockConfiguration;
use TYPO3\CMS\ContentBlocks\Enumeration\FieldType;
use TYPO3\CMS\ContentBlocks\FieldConfiguration\FieldConfigurationInterface;
use TYPO3\CMS\ContentBlocks\Service\ConfigurationService;
use TYPO3\CMS\Core\SingletonInterface;

class TableDefinitionFactory implements SingletonInterface
{
    /**
     * @var ConfigurationService
     */
    protected ConfigurationService $configurationService;

    protected array $tableDefinitionTemplate = [
        'sql' => [],
        'tca' => [],
        'elements' => [],
        'palettes' => [],
    ];

    public function __construct(ConfigurationService $configurationService)
    {
        $this->configurationService = $configurationService;
    }

    /**
     * Create a TableDefinitionCollection and everything what comes with it
     * due to the given ContentBlockConfigurations.
     *
     * @param ContentBlockConfiguration $cbConf     Needed to fill in path to translation files and so on.
     * @param array $fields     usually the yaml fields config for a CB.
     * @param string $cType     needed to prefix a collection unique vendor and package.
     */
    public function createFromArray(ContentBlockConfiguration $cbConf, array $fields, string $cType): TableDefinitionCollection
    {
        $preparedData = [];
        $collectionTablePrefix = $this->configurationService->getDatabaseCollectionTablePrefix() . $cType;
        $ttContentColumnPrefix = $this->configurationService->getDatabaseTtContentPrefix() . $cType;

        $preparedData = $this->processTable($cbConf, 'tt_content', $preparedData, $fields, $collectionTablePrefix, $ttContentColumnPrefix);

        return TableDefinitionCollection::createFromArray($preparedData);
    }

    /**
     * Reduce redundant code while using this method.
     */
    protected function processTable (ContentBlockConfiguration $cbConf, string $table, array $preparedData, array $fields, string $collectionTablePrefix, string $ttContentColumnPrefix = ''): array
    {
        $tableConfiguration = $this->tableDefinitionTemplate;

        foreach ($fields as $field) {
            $processedConfig = $field;
            $processedConfig['EditorInterfaceXlf'] = $cbConf->labelsXlfPath;
            $processedConfig['vendor'] = $cbConf->vendor;
            $processedConfig['package'] = $cbConf->package;

            $currentColumnName = (($ttContentColumnPrefix !== '') ? $ttContentColumnPrefix . '_' : '') . $field['identifier'];

            // handle collections
            if (
                $field['type'] === 'Collection'
                && isset($field['properties']['fields'])
                && count($field['properties']['fields']) > 0
            ) {
                $collectionTableName = $collectionTablePrefix . '_' . $field['identifier'];

                $processedConfig['properties']['foreign_table'] = $collectionTableName; // The table name of the child records
                $processedConfig['properties']['foreign_field'] = $currentColumnName; // the field of the child record pointing to the parent record. This defines where to store the uid of the parent record.
                $processedConfig['properties']['foreign_table_field'] = 'tt_content'; // the field of the child record pointing to the parent record. This defines where to store the table name of the parent record.
                $processedConfig['properties']['foreign_match_fields'] = [
                    'tt_content' => $currentColumnName
                ]; // Array of field-value pairs to both insert and match against when writing/reading IRRE relations.

                $preparedData = $this->processTable($cbConf, $collectionTableName, $preparedData, $field['properties']['fields'], $collectionTableName);
            }

            // @todo: palettes, element
            /** @var FieldConfigurationInterface $fieldConfig */
            $fieldConfig = FieldType::from($field['type'])->getFieldTypeConfiguration($processedConfig);
            $tableConfiguration['tca'][] = [
                'identifier' => $currentColumnName,
                'realTca' => $fieldConfig->getTca(),
            ];
            $fieldConfig->getSql($currentColumnName);
            $tableConfiguration['sql'][$currentColumnName] = [
                $table => [
                    $currentColumnName => $fieldConfig->getSql($currentColumnName),
                ],
            ];
        }

        // add data for tt_content
        $preparedData[] = [
            'table' => $table,
            'tableConfiguration' => $tableConfiguration,
        ];
        return $preparedData;
    }

}
