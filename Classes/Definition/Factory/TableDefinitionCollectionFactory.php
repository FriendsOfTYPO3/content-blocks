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
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinition;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\ContentBlocks\Definition\TcaFieldDefinitionCollection;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;

/**
 * @internal Not part of TYPO3's public API.
 */
final class TableDefinitionCollectionFactory
{
    public function __construct(
        protected readonly LazyObjectInterface|FrontendInterface $cache,
        protected readonly ContentBlockCompiler $contentBlockCompiler,
    ) {}

    public function create(ContentBlockRegistry $contentBlockRegistry): TableDefinitionCollection
    {
        if (!$this->cache->isLazyObjectInitialized()) {
            return $this->createUncached($contentBlockRegistry);
        }
        $tableDefinitionCollection = $this->cache->get('Compiled_ContentBlocks');
        if ($tableDefinitionCollection === false) {
            $tableDefinitionCollection = $this->createUncached($contentBlockRegistry);
            $this->cache->set('Compiled_ContentBlocks', $tableDefinitionCollection);
        }
        return $tableDefinitionCollection;
    }

    public function initializeCache(): void
    {
        if (!$this->cache->isLazyObjectInitialized()) {
            $this->cache->initializeLazyObject();
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
}
