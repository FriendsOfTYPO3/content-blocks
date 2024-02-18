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

namespace TYPO3\CMS\ContentBlocks\Definition\Factory;

use Symfony\Component\VarExporter\LazyObjectInterface;
use Symfony\Component\VarExporter\VarExporter;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinition;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\ContentBlocks\Definition\TcaFieldDefinitionCollection;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\Core\Cache\Frontend\PhpFrontend;

/**
 * @internal Not part of TYPO3's public API.
 */
final class TableDefinitionCollectionFactory
{
    protected TableDefinitionCollection $tableDefinitionCollection;

    public function __construct(
        protected readonly LazyObjectInterface|PhpFrontend $cache,
        protected readonly ContentBlockCompiler $contentBlockCompiler,
    ) {}

    public function create(ContentBlockRegistry $contentBlockRegistry): TableDefinitionCollection
    {
        if (!$this->cache->isLazyObjectInitialized()) {
            $this->tableDefinitionCollection = $this->tableDefinitionCollection ?? $this->createUncached(
                $contentBlockRegistry
            );
            return $this->tableDefinitionCollection;
        }
        if (($tableDefinitionCollection = $this->getFromCache()) !== false) {
            $this->tableDefinitionCollection = $tableDefinitionCollection;
            return $this->tableDefinitionCollection;
        }
        $this->tableDefinitionCollection = $this->tableDefinitionCollection ?? $this->createUncached(
            $contentBlockRegistry
        );
        $this->setCache();
        return $this->tableDefinitionCollection;
    }

    public function initializeCache(): void
    {
        $this->cache->initializeLazyObject();
        if (isset($this->tableDefinitionCollection) && $this->getFromCache() === false) {
            $this->setCache();
        }
    }

    public function createUncached(ContentBlockRegistry $contentBlockRegistry): TableDefinitionCollection
    {
        $compiledContentBlocks = $this->contentBlockCompiler->compile($contentBlockRegistry);
        $tableDefinitionCollection = $this->enrichTableDefinitions($compiledContentBlocks);
        return $tableDefinitionCollection;
    }

    private function enrichTableDefinitions(CompilationResult $compilationResult): TableDefinitionCollection
    {
        $automaticLanguageKeysRegistry = $compilationResult->getAutomaticLanguageKeys();
        $tableDefinitionCollection = new TableDefinitionCollection($automaticLanguageKeysRegistry);
        foreach ($compilationResult->getMergedTableDefinitions() as $table => $tableDefinition) {
            $newTableDefinition = TableDefinition::createFromTableArray($table, $tableDefinition);
            $newTableDefinition = $this->enrichCollectionsWithParentReference($compilationResult, $newTableDefinition);
            $tableDefinitionCollection->addTable($newTableDefinition);
        }
        return $tableDefinitionCollection;
    }

    private function enrichCollectionsWithParentReference(
        CompilationResult $compilationResult,
        TableDefinition $newTableDefinition
    ): TableDefinition {
        if (isset($compilationResult->getParentReferences()[$newTableDefinition->getTable()])) {
            $references = $compilationResult->getParentReferences()[$newTableDefinition->getTable()];
            $tcaFieldDefinitionCollection = TcaFieldDefinitionCollection::createFromArray(
                $references,
                $newTableDefinition->getTable()
            );
            $newTableDefinition = $this->enrichTableDefinition($tcaFieldDefinitionCollection, $newTableDefinition);
        }
        return $newTableDefinition;
    }

    private function enrichTableDefinition(
        TcaFieldDefinitionCollection $references,
        TableDefinition $newTableDefinition,
    ): TableDefinition {
        $newTableDefinition = $newTableDefinition->withParentReferences($references);
        // If root Content Type is a Content Element, allow the external table to be put in standard pages.
        foreach ($references as $reference) {
            if ($reference->getParentContentType() === ContentType::CONTENT_ELEMENT) {
                $capability = $newTableDefinition->getCapability();
                $capability = $capability->withIgnorePageTypeRestriction(true);
                $newTableDefinition = $newTableDefinition->withCapability($capability);
            }
        }
        return $newTableDefinition;
    }

    private function getFromCache(): false|TableDefinitionCollection
    {
        return $this->cache->require('TableDefinitionCollection');
    }

    private function setCache(): void
    {
        $data = 'return ' . VarExporter::export($this->tableDefinitionCollection) . ';';
        $this->cache->set('TableDefinitionCollection', $data);
    }
}
