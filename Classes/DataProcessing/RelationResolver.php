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
use TYPO3\CMS\ContentBlocks\Definition\TableDefinition;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\ContentBlocks\Definition\TcaFieldDefinition;
use TYPO3\CMS\ContentBlocks\FieldType\FieldType;
use TYPO3\CMS\ContentBlocks\FieldType\FolderFieldType;
use TYPO3\CMS\ContentBlocks\Schema\SimpleTcaSchemaFactory;
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
        protected readonly SimpleTcaSchemaFactory $simpleTcaSchemaFactory,
        protected readonly FlexFormService $flexFormService,
        protected readonly RelationResolverSession $relationResolverSession,
    ) {}

    public function setRequest(ServerRequestInterface $serverRequest): void
    {
        $this->serverRequest = $serverRequest;
    }

    public function resolve(
        ContentTypeInterface $contentTypeDefinition,
        TableDefinition $tableDefinition,
        array $data,
        string $table,
    ): ResolvedRelation {
        $resolvedRelation = new ResolvedRelation();
        $resolvedRelation->table = $table;
        $resolvedRelation->raw = $data;
        // @todo remove _PAGES_OVERLAY_UID in v13.
        $identifier = $table . '-' . ($data['_PAGES_OVERLAY_UID'] ?? $data['_LOCALIZED_UID'] ?? $data['uid']);
        $this->relationResolverSession->addRelation($identifier, $resolvedRelation);
        foreach ($contentTypeDefinition->getColumns() as $column) {
            $tcaFieldDefinition = $tableDefinition->getTcaFieldDefinitionCollection()->getField($column);
            $fieldType = $tcaFieldDefinition->getFieldType();
            $fieldTypeEnum = FieldType::tryFrom($fieldType::getName());
            if ($fieldTypeEnum->isStructureField()) {
                continue;
            }
            $resolvedField = $this->processField(
                $tcaFieldDefinition,
                $contentTypeDefinition,
                $data,
                $table
            );
            $data[$tcaFieldDefinition->getUniqueIdentifier()] = $resolvedField;
        }
        $resolvedRelation->resolved = $data;
        return $resolvedRelation;
    }

    public function processField(
        TcaFieldDefinition $tcaFieldDefinition,
        ContentTypeInterface $typeDefinition,
        array $record,
        string $table
    ): mixed {
        $fieldType = $tcaFieldDefinition->getFieldType();
        $tcaType = $fieldType::getTcaType();
        $recordIdentifier = $tcaFieldDefinition->getUniqueIdentifier();
        if (!array_key_exists($recordIdentifier, $record)) {
            throw new \RuntimeException(
                'The field "' . $recordIdentifier . '" is missing in the "' . $table
                . '" table. Probably a database schema update is needed.',
                1674222293
            );
        }
        $rawValue = $record[$recordIdentifier];
        $processedValue = match ($tcaType) {
            'file' => $this->processFileReference($table, $recordIdentifier, $record),
            'inline' => $this->processCollection($table, $record, $tcaFieldDefinition, $typeDefinition),
            'category' => $this->processCategory($tcaFieldDefinition, $typeDefinition, $table, $record),
            'group' => $this->processRelation($tcaFieldDefinition, $typeDefinition, $table, $record),
            'folder' => $this->processFolder($rawValue, $tcaFieldDefinition),
            'select' => $this->processSelect($tcaFieldDefinition, $typeDefinition, $table, $record),
            'flex' => $this->flexFormService->convertFlexFormContentToArray($rawValue),
            'json' => $this->processJson($table, $rawValue),
            default => $rawValue,
        };
        return $processedValue;
    }

    protected function processSelect(
        TcaFieldDefinition $tcaFieldDefinition,
        ContentTypeInterface $typeDefinition,
        string $parentTable,
        array $record
    ): mixed {
        $tcaFieldConfig = $this->getMergedTcaFieldConfig($parentTable, $tcaFieldDefinition, $typeDefinition);
        $uniqueIdentifier = $tcaFieldDefinition->getUniqueIdentifier();
        $renderType = $tcaFieldConfig['config']['renderType'] ?? '';
        $foreignTable = $tcaFieldConfig['config']['foreign_table'] ?? '';
        $rawValue = $record[$uniqueIdentifier] ?? '';
        if ($foreignTable !== '') {
            $resolvedRelations = $this->getRelations(
                (string)$rawValue,
                $foreignTable,
                $tcaFieldConfig['config']['MM'] ?? '',
                $this->getUidOfCurrentRecord($record),
                $parentTable,
                $tcaFieldConfig['config'] ?? []
            );
            // If this table is defined by Content Blocks, process child relations.
            if ($this->tableDefinitionCollection->hasTable($foreignTable)) {
                $resolvedRelations = $this->processChildRelations($resolvedRelations);
            }
            // For convenience selectSingle is returned as single value, even though
            // renderType should normally only be relevant in FormEngine context.
            if ($renderType === 'selectSingle') {
                return $resolvedRelations[0] ?? null;
            }
            return $resolvedRelations;
        }
        $multiValueRenderTypes = ['selectCheckBox', 'selectSingleBox', 'selectMultipleSideBySide'];
        if (in_array($renderType, $multiValueRenderTypes, true)) {
            if ($rawValue === '') {
                return [];
            }
            $valueAsList = explode(',', $rawValue);
            return $valueAsList;
        }
        return $rawValue;
    }

    protected function processRelation(
        TcaFieldDefinition $tcaFieldDefinition,
        ContentTypeInterface $typeDefinition,
        string $parentTable,
        array $record
    ): array {
        $tcaFieldConfig = $this->getMergedTcaFieldConfig($parentTable, $tcaFieldDefinition, $typeDefinition);
        $allowed = $tcaFieldConfig['config']['allowed'];
        $fieldValue = (string)($record[$tcaFieldDefinition->getUniqueIdentifier()] ?? '');
        $result = $this->getRelations(
            $fieldValue,
            $allowed,
            $tcaFieldConfig['config']['MM'] ?? '',
            $this->getUidOfCurrentRecord($record),
            $parentTable,
            $tcaFieldConfig['config'] ?? []
        );
        $result = $this->processChildRelations($result);
        return $result;
    }

    protected function processCategory(
        TcaFieldDefinition $tcaFieldDefinition,
        ContentTypeInterface $typeDefinition,
        string $parentTable,
        array $record
    ): array {
        $tcaFieldConfig = $this->getMergedTcaFieldConfig($parentTable, $tcaFieldDefinition, $typeDefinition);
        $uidList = $tcaFieldConfig['config']['relationship'] === 'manyToMany'
            ? ''
            : (string)($record[$tcaFieldDefinition->getUniqueIdentifier()] ?? '');
        $result = $this->getRelations(
            $uidList,
            $tcaFieldConfig['config']['foreign_table'] ?? '',
            $tcaFieldConfig['config']['MM'] ?? '',
            $this->getUidOfCurrentRecord($record),
            $parentTable,
            $tcaFieldConfig['config'] ?? []
        );
        return $result;
    }

    protected function processCollection(
        string $parentTable,
        array $record,
        TcaFieldDefinition $tcaFieldDefinition,
        ContentTypeInterface $typeDefinition
    ): array {
        $tcaFieldConfig = $this->getMergedTcaFieldConfig($parentTable, $tcaFieldDefinition, $typeDefinition);
        $collectionTable = $tcaFieldConfig['config']['foreign_table'] ?? '';
        $uid = (string)($record[$tcaFieldDefinition->getUniqueIdentifier()] ?? '');
        $result = $this->getRelations(
            $uid,
            $collectionTable,
            $tcaFieldConfig['config']['MM'] ?? '',
            $this->getUidOfCurrentRecord($record),
            $parentTable,
            $tcaFieldConfig['config'] ?? []
        );
        // If this table is defined by Content Blocks, process child relations.
        if ($this->tableDefinitionCollection->hasTable($collectionTable)) {
            $result = $this->processChildRelations($result);
        }
        return $result;
    }

    protected function processFileReference(string $table, string $recordIdentifier, array $record): array
    {
        $fileCollector = GeneralUtility::makeInstance(FileCollector::class);
        $fileCollector->addFilesFromRelation($table, $recordIdentifier, $record);
        $files = $fileCollector->getFiles();
        return $files;
    }

    protected function processFolder(string|int|null $data, TcaFieldDefinition $tcaFieldDefinition): array
    {
        $fileCollector = GeneralUtility::makeInstance(FileCollector::class);
        $folders = GeneralUtility::trimExplode(',', (string)$data, true);
        /** @var FolderFieldType $folderFieldType */
        $folderFieldType = $tcaFieldDefinition->getFieldType();
        $fileCollector->addFilesFromFolders($folders, $folderFieldType->isRecursive());
        $files = $fileCollector->getFiles();
        return $files;
    }

    protected function processJson(string $table, string|null $data): array|bool|null
    {
        $platform = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable($table)
            ->getDatabasePlatform();
        $jsonType = Type::getType('json');
        $phpArray = $jsonType->convertToPHPValue($data, $platform);
        return $phpArray;
    }

    /**
     * @param ResolvedRelation[] $resolvedRelations
     * @return ResolvedRelation[]
     */
    protected function processChildRelations(array $resolvedRelations): array
    {
        foreach ($resolvedRelations as $index => $resolvedRelation) {
            // If this table is not defined by Content Blocks, skip processing.
            if (!$this->tableDefinitionCollection->hasTable($resolvedRelation->table)) {
                continue;
            }
            $tableDefinition = $this->tableDefinitionCollection->getTable($resolvedRelation->table);
            $identifier = $resolvedRelation->table . '-' . $resolvedRelation->raw['uid'];
            if ($this->relationResolverSession->hasRelation($identifier)) {
                $resolvedRelations[$index] = $this->relationResolverSession->getRelation($identifier);
                continue;
            }
            // Feed plain row into session. In case this record should be resolved inside itself,
            // which would cause infinite recursion, this plain row will be used instead.
            $this->relationResolverSession->addRelation($identifier, $resolvedRelation);
            foreach ($tableDefinition->getTcaFieldDefinitionCollection() as $childTcaFieldDefinition) {
                $foreignTypeDefinition = ContentTypeResolver::resolve($tableDefinition, $resolvedRelation->raw);
                if ($foreignTypeDefinition === null) {
                    continue;
                }
                $processedField = $this->processField(
                    $childTcaFieldDefinition,
                    $foreignTypeDefinition,
                    $resolvedRelation->raw,
                    $resolvedRelation->table,
                );
                $resolvedRelation->resolved[$childTcaFieldDefinition->getUniqueIdentifier()] = $processedField;
            }
            // Override previously set raw relation with resolved relation.
            $this->relationResolverSession->addRelation($identifier, $resolvedRelation);
        }
        return $resolvedRelations;
    }

    /**
     * @param array<string, mixed> $tcaFieldConf
     * @return ResolvedRelation[]
     */
    protected function getRelations(
        string $uidList,
        string $tableList,
        string $mmTable,
        int $uid,
        string $currentTable,
        array $tcaFieldConf = []
    ): array {
        $pageRepository = $this->getPageRepository();
        $relationHandler = GeneralUtility::makeInstance(RelationHandler::class);
        $relationHandler->start($uidList, $tableList, $mmTable, $uid, $currentTable, $tcaFieldConf);
        foreach (array_keys($relationHandler->tableArray) as $table) {
            if ($this->simpleTcaSchemaFactory->has($table)) {
                $autoHiddenSelection = -1;
                $ignoreWorkspaceFilter = ['pid' => true];
                $additionalWhere = $pageRepository->enableFields($table, $autoHiddenSelection, $ignoreWorkspaceFilter);
                $relationHandler->additionalWhere[$table] = $additionalWhere;
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
                $resolvedRelation = new ResolvedRelation();
                // Save associated table and raw record for later usage.
                $resolvedRelation->table = $tableName;
                $resolvedRelation->raw = $translatedRecord;
                $resolvedRelation->resolved = $translatedRecord;
                $records[] = $resolvedRelation;
            }
        }
        return $records;
    }

    protected function getMergedTcaFieldConfig(
        string $table,
        TcaFieldDefinition $tcaFieldDefinition,
        ContentTypeInterface $typeDefinition
    ): array {
        $identifier = $tcaFieldDefinition->getUniqueIdentifier();
        $tableTca = $GLOBALS['TCA'][$table] ?? [];
        $baseConfig = $tableTca['columns'][$identifier] ?? [];
        $overrides = $tableTca['types'][$typeDefinition->getTypeName()]['columnsOverrides'][$identifier] ?? [];
        $mergedTcaFieldConfig = array_replace_recursive($baseConfig, $overrides);
        return $mergedTcaFieldConfig;
    }

    protected function getPageRepository(): PageRepository
    {
        $frontendController = $this->serverRequest?->getAttribute('frontend.controller');
        if (
            $frontendController instanceof TypoScriptFrontendController
            && $frontendController->sys_page instanceof PageRepository
        ) {
            return $frontendController->sys_page;
        }
        return GeneralUtility::makeInstance(PageRepository::class);
    }

    protected function getUidOfCurrentRecord(array $record): int
    {
        if (isset($record['_ORIG_uid'])) {
            return (int)$record['_ORIG_uid'];
        }
        if (isset($record['_LOCALIZED_UID'])) {
            return (int)$record['_LOCALIZED_UID'];
        }
        // @todo remove in v13
        if (isset($record['_PAGES_OVERLAY_UID'])) {
            return (int)$record['_PAGES_OVERLAY_UID'];
        }
        return (int)$record['uid'];
    }
}
