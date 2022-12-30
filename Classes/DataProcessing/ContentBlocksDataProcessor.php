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

namespace TYPO3\CMS\ContentBlocks\DataProcessing;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\ContentBlocks\Definition\ContentElementDefinition;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\ContentBlocks\Definition\TcaFieldDefinition;
use TYPO3\CMS\ContentBlocks\Domain\Model\ContentBlockConfiguration;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\WorkspaceRestriction;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Frontend\Resource\FileCollector;

/**
 * Adds information about the current content block to variable "cb".
 */
class ContentBlocksDataProcessor implements DataProcessorInterface
{
    protected string $cType;
    protected array $record;
    protected ContentElementDefinition $contentElementDefinition;
    protected ContentBlockConfiguration $cbConf;

    public function __construct(
        protected readonly TableDefinitionCollection $tableDefinitionCollection
    ) {
    }

    /**
     * @throws \Exception
     * @param ContentObjectRenderer $cObj The data of the content element or page
     * @param array $contentObjectConfiguration The configuration of Content Object
     * @param array $processorConfiguration The configuration of this processor
     * @param array $processedData Key/value store of processed data (e.g. to be passed to a Fluid View)
     * @return array the processed data as key/value store
     */
    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ) {
        $this->record = $processedData['data'];
        $this->cType = $this->record['CType'];

        $ttContentDefinition = $this->tableDefinitionCollection->getTable('tt_content');

        // @todo implement
//        $this->contentElementDefinition = $this->tableDefinitionCollection->findContentElementDefinition($this->cType);

        $cbData = [];

        /** @var TcaFieldDefinition $fieldDefinition */
        foreach ($ttContentDefinition->getTcaColumnsDefinition() as $column => $fieldDefinition) {
            $cbData = $this->_processField($fieldDefinition, $this->record, $cbData, 'tt_content');
        }

        $processedData = array_merge($processedData, $cbData);
        return $processedData;
    }

    /** process a field
     * @throws \Exception
     * @var TcaFieldDefinition $fieldConf, configuration of the field
     * @var array $record, the data base record (row) with the values inside
     * @var array $cbData, the data stack where to add the data
     * @return array|string|int
    */
    protected function _processField(TcaFieldDefinition $fieldConf, array $record, array $cbData, string $table)
    {
        $fieldType = $fieldConf->getFieldType();

        // feature: use existing field
        $columnInRecord = (($fieldConf->isUseExistingField()) ? $fieldConf->getName() : $fieldConf->getIdentifier());

        // check if column is available
        if (!array_key_exists($columnInRecord, $record)) {
            throw new \Exception(sprintf('It seems your field %s is missing in the database. Maybe a database compare could help you out.', $columnInRecord));
        }

        // columns for direct output without processing
        if ($fieldType->dataProcessingBehaviour() === 'renderable') {
            $cbData[$fieldConf->getName()] = $record[$columnInRecord];

        } else if ($fieldType->dataProcessingBehaviour() === 'file') {
            //process files
            /** @var FileCollector $fileCollector */
            $fileCollector = GeneralUtility::makeInstance(FileCollector::class);

            $fileCollector->addFilesFromRelation($table, $columnInRecord, $record);
            $files = $fileCollector->getFiles();

            $fileFieldTcaConfig = $fieldConf->getTca($this->contentElementDefinition);
            if (
                (isset($fileFieldTcaConfig['config']['minitems']) && $fileFieldTcaConfig['config']['minitems'] == 1) &&
                (isset($fileFieldTcaConfig['config']['maxitems']) && $fileFieldTcaConfig['config']['maxitems'] == 1)
            ) {
                $files = array_pop(array_reverse($files));
            }
            if ($files instanceof FileReference) {
                $cbData[$fieldConf->getName()] = [
                    $files
                ];
            } else {
                $cbData[$fieldConf->getName()] = $files;
            }

        } else if ($fieldType->dataProcessingBehaviour() === 'collection') {
            // handle collections
            $cbData[$fieldConf->getName()] = $this->_processCollection(
                    $table,
                    $record['_LOCALIZED_UID'] ?? $record['uid'],
                    $fieldConf
            );
        }

        return $cbData;
    }

    /**
     * Manage collections and sub fields.
     */
    protected function _processCollection(string $parentTable, int $parentUid, TcaFieldDefinition $parentFieldConf): array
    {
        $parentTca = $parentFieldConf->getTca($this->contentElementDefinition);
        $table = $parentTca['config']['foreign_table'];

        $collectionDefinition = $this->tableDefinitionCollection->getTable($table);
        // Managing Workspace overlays
        $workspaceId = GeneralUtility::makeInstance(Context::class)->getPropertyFromAspect('workspace', 'id', 0);

        $q = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable($table)
            ->createQueryBuilder();

        $q->getRestrictions()->add(
            GeneralUtility::makeInstance(WorkspaceRestriction::class, $workspaceId)
        );

        $stmt = $q->select('*')
            ->from($table)
            ->where(
                $q->expr()->eq(
                    'foreign_table_parent_uid',
                    $q->createNamedParameter($parentUid, Connection::PARAM_INT)
                )
            )
            ->orderBy('sorting')
            ->execute();

        $fieldData = [];

        while ($r = $stmt->fetch()) {
            // overlay workspaces
            if ($this->_isFrontend()) {
                GeneralUtility::makeInstance(PageRepository::class)
                    ->versionOL($table, $r);
                if (false === $r) {
                    continue;
                }
            } else {
                BackendUtility::workspaceOL($table, $r);
                if (false === $r) {
                    continue;
                }
            }

            $collectionData = [];
            // add the field infos
            /** @var TcaFieldDefinition $fieldDefinition */
            foreach ($collectionDefinition->getTcaColumnsDefinition() as $fieldDefinition) {
                $collectionData = $this->_processField($fieldDefinition, $r, $collectionData, $table);
            }

            // add uid to collection items
            if (!array_key_exists('uid', $collectionData)) {
                $collectionData['uid'] = $r['uid'];
            }
            $fieldData[] = $collectionData;
        }

        return $fieldData;
    }

    protected function _isFrontend()
    {
        return ($GLOBALS['TSFE'] ?? null) instanceof TypoScriptFrontendController;
    }
}
