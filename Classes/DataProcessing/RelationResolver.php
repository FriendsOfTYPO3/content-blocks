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

use Doctrine\DBAL\Types\Type;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentTypeInterface;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\ContentBlocks\Definition\TcaFieldDefinition;
use TYPO3\CMS\ContentBlocks\FieldConfiguration\FieldType;
use TYPO3\CMS\ContentBlocks\FieldConfiguration\FolderFieldConfiguration;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\RelationHandler;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Frontend\Resource\FileCollector;

/**
 * @internal Not part of TYPO3's public API.
 */
class RelationResolver
{
    protected ?ServerRequestInterface $serverRequest = null;

    public function __construct(
        protected readonly TableDefinitionCollection $tableDefinitionCollection,
        protected readonly FlexFormService $flexFormService,
    ) {}

    public function processField(TcaFieldDefinition $tcaFieldDefinition, ContentTypeInterface $typeDefinition, array $record, string $table): mixed
    {
        $fieldType = $tcaFieldDefinition->getFieldType();
        $recordIdentifier = $tcaFieldDefinition->getUniqueIdentifier();

        if (!array_key_exists($recordIdentifier, $record)) {
            throw new \RuntimeException(
                'The field "' . $recordIdentifier . '" is missing in the "' . $table . '" table. Probably a database schema update is needed.',
                1674222293
            );
        }

        $data = $record[$recordIdentifier];

        if ($fieldType === FieldType::FILE) {
            $fileCollector = GeneralUtility::makeInstance(FileCollector::class);
            $fileCollector->addFilesFromRelation($table, $recordIdentifier, $record);
            return $fileCollector->getFiles();
        }

        if ($fieldType === FieldType::COLLECTION) {
            return $this->processCollection($table, $record, $tcaFieldDefinition, $typeDefinition);
        }

        if ($fieldType === FieldType::CATEGORY) {
            return $this->processCategory($tcaFieldDefinition, $typeDefinition, $table, $record);
        }

        if ($fieldType === FieldType::RELATION) {
            return $this->processRelation($tcaFieldDefinition, $typeDefinition, $table, $record);
        }

        if ($fieldType === FieldType::FOLDER) {
            $fileCollector = GeneralUtility::makeInstance(FileCollector::class);
            $folders = GeneralUtility::trimExplode(',', (string)$data, true);
            /** @var FolderFieldConfiguration $folderFieldConfiguration */
            $folderFieldConfiguration = $tcaFieldDefinition->getFieldConfiguration();
            $fileCollector->addFilesFromFolders($folders, $folderFieldConfiguration->isRecursive());
            return $fileCollector->getFiles();
        }

        if ($fieldType === FieldType::SELECT) {
            return $this->processSelect($tcaFieldDefinition, $typeDefinition, $table, $record);
        }

        if ($fieldType === FieldType::FLEXFORM) {
            return $this->flexFormService->convertFlexFormContentToArray($data);
        }

        if ($fieldType === FieldType::JSON) {
            $platform = GeneralUtility::makeInstance(ConnectionPool::class)
                ->getConnectionForTable($table)
                ->getDatabasePlatform();
            return Type::getType('json')->convertToPHPValue($data, $platform);
        }

        return $data;
    }

    protected function processSelect(TcaFieldDefinition $tcaFieldDefinition, ContentTypeInterface $typeDefinition, string $parentTable, array $record): mixed
    {
        $tcaFieldConfig = $this->getMergedTcaFieldConfig($parentTable, $tcaFieldDefinition, $typeDefinition);
        $uniqueIdentifier = $tcaFieldDefinition->getUniqueIdentifier();
        if (($tcaFieldConfig['config']['foreign_table'] ?? '') !== '') {
            $foreignTable = $tcaFieldConfig['config']['foreign_table'];
            $result = $this->getRelations(
                uidList: (string)($record[$uniqueIdentifier] ?? ''),
                tableList: $foreignTable,
                mmTable: $tcaFieldConfig['config']['MM'] ?? '',
                uid: $this->getUidOfCurrentRecord($record),
                currentTable: $parentTable,
                tcaFieldConf: $tcaFieldConfig['config'] ?? []
            );
            if ($this->tableDefinitionCollection->hasTable($foreignTable)) {
                $tableDefinition = $this->tableDefinitionCollection->getTable($foreignTable);
                foreach ($result as $index => $row) {
                    foreach ($tableDefinition->getTcaFieldDefinitionCollection() as $childTcaFieldDefinition) {
                        $foreignTypeDefinition = ContentTypeResolver::resolve($tableDefinition, $row);
                        if ($foreignTypeDefinition === null) {
                            continue;
                        }
                        $result[$index][$childTcaFieldDefinition->getUniqueIdentifier()] = $this->processField(
                            tcaFieldDefinition: $childTcaFieldDefinition,
                            typeDefinition: $foreignTypeDefinition,
                            record: $row,
                            table: $foreignTable,
                        );
                    }
                }
            }
            if (($tcaFieldConfig['config']['renderType'] ?? '') === 'selectSingle') {
                return $result[0] ?? null;
            }
            return $result;
        }
        if (in_array(($tcaFieldConfig['config']['renderType'] ?? ''), ['selectCheckBox', 'selectSingleBox', 'selectMultipleSideBySide'], true)) {
            return ($record[$uniqueIdentifier] ?? '') !== '' ? explode(',', $record[$uniqueIdentifier]) : [];
        }
        return $record[$uniqueIdentifier] ?? '';
    }

    protected function processRelation(TcaFieldDefinition $tcaFieldDefinition, ContentTypeInterface $typeDefinition, string $parentTable, array $record): array
    {
        $tcaFieldConfig = $this->getMergedTcaFieldConfig($parentTable, $tcaFieldDefinition, $typeDefinition);
        $allowed = $tcaFieldConfig['config']['allowed'];
        $fieldValue = (string)($record[$tcaFieldDefinition->getUniqueIdentifier()] ?? '');
        $result = $this->getRelations(
            uidList: $fieldValue,
            tableList: $allowed,
            mmTable: $tcaFieldConfig['config']['MM'] ?? '',
            uid: $this->getUidOfCurrentRecord($record),
            currentTable: $parentTable,
            tcaFieldConf: $tcaFieldConfig['config'] ?? []
        );

        $tableList = null;
        if (str_contains($allowed, ',')) {
            $tableList = $this->getTableListFromTableUidPairs($fieldValue);
        }

        foreach ($result as $index => $row) {
            $currentTable = $tableList !== null ? $tableList[$index] : $allowed;
            $result[$index]['_table'] = $currentTable;
            if ($this->tableDefinitionCollection->hasTable($currentTable)) {
                $tableDefinition = $this->tableDefinitionCollection->getTable($currentTable);
                foreach ($tableDefinition->getTcaFieldDefinitionCollection() as $childTcaFieldDefinition) {
                    $foreignTypeDefinition = ContentTypeResolver::resolve($tableDefinition, $row);
                    if ($foreignTypeDefinition === null) {
                        continue;
                    }
                    $result[$index][$childTcaFieldDefinition->getUniqueIdentifier()] = $this->processField(
                        tcaFieldDefinition: $childTcaFieldDefinition,
                        typeDefinition: $foreignTypeDefinition,
                        record: $row,
                        table: $currentTable,
                    );
                }
            }
        }

        return $result;
    }

    protected function processCategory(TcaFieldDefinition $tcaFieldDefinition, ContentTypeInterface $typeDefinition, string $parentTable, array $record): array
    {
        $tcaFieldConfig = $this->getMergedTcaFieldConfig($parentTable, $tcaFieldDefinition, $typeDefinition);
        $uidList = $tcaFieldConfig['config']['relationship'] === 'manyToMany' ? '' : (string)($record[$tcaFieldDefinition->getUniqueIdentifier()] ?? '');
        $result = $this->getRelations(
            uidList: $uidList,
            tableList: $tcaFieldConfig['config']['foreign_table'] ?? '',
            mmTable: $tcaFieldConfig['config']['MM'] ?? '',
            uid: $this->getUidOfCurrentRecord($record),
            currentTable: $parentTable,
            tcaFieldConf: $tcaFieldConfig['config'] ?? []
        );
        return $result;
    }

    protected function processCollection(string $parentTable, array $record, TcaFieldDefinition $tcaFieldDefinition, ContentTypeInterface $typeDefinition): array
    {
        $tcaFieldConfig = $this->getMergedTcaFieldConfig($parentTable, $tcaFieldDefinition, $typeDefinition);
        $collectionTable = $tcaFieldConfig['config']['foreign_table'] ?? '';
        $uid = (string)($record[$tcaFieldDefinition->getUniqueIdentifier()] ?? '');
        $data = $this->getRelations(
            uidList: $uid,
            tableList: $collectionTable,
            mmTable: $tcaFieldConfig['config']['MM'] ?? '',
            uid: $this->getUidOfCurrentRecord($record),
            currentTable: $parentTable,
            tcaFieldConf: $tcaFieldConfig['config'] ?? []
        );

        // If this table is defined through TCA, return data as is.
        if (!$this->tableDefinitionCollection->hasTable($collectionTable)) {
            return $data;
        }

        $tableDefinition = $this->tableDefinitionCollection->getTable($collectionTable);
        foreach ($data as $index => $row) {
            foreach ($tableDefinition->getTcaFieldDefinitionCollection() as $childTcaFieldDefinition) {
                $childTypeDefinition = ContentTypeResolver::resolve($tableDefinition, $row);
                if ($childTypeDefinition === null) {
                    continue;
                }
                $data[$index][$childTcaFieldDefinition->getUniqueIdentifier()] = $this->processField(
                    tcaFieldDefinition: $childTcaFieldDefinition,
                    typeDefinition: $childTypeDefinition,
                    record: $row,
                    table: $collectionTable,
                );
            }
        }
        return $data;
    }

    /**
     * @param array<string, mixed> $tcaFieldConf
     */
    protected function getRelations(string $uidList, string $tableList, string $mmTable, int $uid, string $currentTable, array $tcaFieldConf = []): array
    {
        $pageRepository = $this->getPageRepository();
        $relationHandler = GeneralUtility::makeInstance(RelationHandler::class);
        $relationHandler->start($uidList, $tableList, $mmTable, $uid, $currentTable, $tcaFieldConf);
        foreach (array_keys($relationHandler->tableArray) as $table) {
            if (isset($GLOBALS['TCA'][$table])) {
                $autoHiddenSelection = -1;
                $ignoreWorkspaceFilter = ['pid' => true];
                $relationHandler->additionalWhere[$table] = $pageRepository->enableFields($table, $autoHiddenSelection, $ignoreWorkspaceFilter);
            }
        }
        $relationHandler->getFromDB();
        $relations = $relationHandler->getResolvedItemArray();
        $records = [];
        foreach ($relations as $relation) {
            $tableName = $relation['table'];
            $record = $relation['record'];
            $pageRepository->versionOL($tableName, $record);
            if (!is_array($record)) {
                continue;
            }
            $translatedRecord = $pageRepository->getLanguageOverlay($tableName, $record);
            if ($translatedRecord !== null) {
                $records[] = $translatedRecord;
            }
        }
        return $records;
    }

    public function setRequest(ServerRequestInterface $serverRequest): void
    {
        $this->serverRequest = $serverRequest;
    }

    protected function getMergedTcaFieldConfig(string $table, TcaFieldDefinition $tcaFieldDefinition, ContentTypeInterface $typeDefinition): array
    {
        return array_replace_recursive(
            $GLOBALS['TCA'][$table]['columns'][$tcaFieldDefinition->getUniqueIdentifier()] ?? [],
            $GLOBALS['TCA'][$table]['types'][$typeDefinition->getTypeName()]['columnsOverrides'][$tcaFieldDefinition->getUniqueIdentifier()] ?? []
        );
    }

    protected function getPageRepository(): PageRepository
    {
        $frontendController = $this->serverRequest?->getAttribute('frontend.controller');
        if ($frontendController instanceof TypoScriptFrontendController && $frontendController->sys_page instanceof PageRepository) {
            return $frontendController->sys_page;
        }
        return GeneralUtility::makeInstance(PageRepository::class);
    }

    protected function getUidOfCurrentRecord(array $record): int
    {
        if (isset($record['_ORIG_uid'])) {
            return (int)$record['_ORIG_uid'];
        }
        return (int)$record['uid'];
    }

    /**
     * @return array<string>
     */
    protected function getTableListFromTableUidPairs(string $tableUidPairs): array
    {
        $tableUidList = GeneralUtility::trimExplode(',', $tableUidPairs);
        $tableList = array_map(function (string $item): string {
            $parts = explode('_', $item);
            array_pop($parts);
            $table = implode('_', $parts);
            return $table;
        }, $tableUidList);
        return $tableList;
    }
}
