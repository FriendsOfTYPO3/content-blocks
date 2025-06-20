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

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\VarExporter\VarExporter;
use TYPO3\CMS\ContentBlocks\Definition\Capability\TableDefinitionCapability;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentTypeDefinitionCollection;
use TYPO3\CMS\ContentBlocks\Definition\PaletteDefinitionCollection;
use TYPO3\CMS\ContentBlocks\Definition\SqlColumnDefinitionCollection;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinition;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\ContentBlocks\Definition\TcaFieldDefinition;
use TYPO3\CMS\ContentBlocks\Definition\TcaFieldDefinitionCollection;
use TYPO3\CMS\ContentBlocks\FieldType\FieldTypeRegistry;
use TYPO3\CMS\ContentBlocks\Loader\ContentBlockLoader;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\ContentBlocks\Schema\SimpleTcaSchemaFactory;
use TYPO3\CMS\Core\Cache\Frontend\PhpFrontend;

/**
 * @internal Not part of TYPO3's public API.
 */
final class TableDefinitionCollectionFactory
{
    protected TableDefinitionCollection $tableDefinitionCollection;

    public function __construct(
        #[Autowire(service: 'cache.core')]
        protected readonly PhpFrontend $cache,
        protected readonly ContentBlockCompiler $contentBlockCompiler,
        protected readonly ContentBlockLoader $contentBlockLoader,
    ) {}

    public function create(
        FieldTypeRegistry $fieldTypeRegistry,
        SimpleTcaSchemaFactory $simpleTcaSchemaFactory,
    ): TableDefinitionCollection {
        if (isset($this->tableDefinitionCollection)) {
            return $this->tableDefinitionCollection;
        }
        if (($tableDefinitionCollection = $this->getFromCache()) !== false) {
            $this->tableDefinitionCollection = $tableDefinitionCollection;
            return $this->tableDefinitionCollection;
        }
        $contentBlockRegistry = $this->contentBlockLoader->loadUncached();
        $this->tableDefinitionCollection = $this->createUncached(
            $contentBlockRegistry,
            $fieldTypeRegistry,
            $simpleTcaSchemaFactory
        );
        $this->setCache();
        return $this->tableDefinitionCollection;
    }

    public function createUncached(
        ContentBlockRegistry $contentBlockRegistry,
        FieldTypeRegistry $fieldTypeRegistry,
        SimpleTcaSchemaFactory $simpleTcaSchemaFactory
    ): TableDefinitionCollection {
        $compiledContentBlocks = $this->contentBlockCompiler->compile($contentBlockRegistry, $fieldTypeRegistry, $simpleTcaSchemaFactory);
        $tableDefinitionCollection = $this->enrichTableDefinitions($compiledContentBlocks);
        return $tableDefinitionCollection;
    }

    private function enrichTableDefinitions(CompilationResult $compilationResult): TableDefinitionCollection
    {
        $automaticLanguageKeysRegistry = $compilationResult->getAutomaticLanguageKeys();
        $tableDefinitionCollection = new TableDefinitionCollection($automaticLanguageKeysRegistry);
        foreach ($compilationResult->getMergedTableDefinitions() as $table => $tableDefinition) {
            $arguments = [];
            $arguments['table'] = $table;
            $arguments['typeField'] = $tableDefinition['typeField'] ?? null;
            $arguments['contentType'] = $tableDefinition['contentType'];
            $arguments['tcaFieldDefinitionCollection'] = TcaFieldDefinitionCollection::createFromArray($tableDefinition['fields'] ?? [], $table);
            $arguments['sqlColumnDefinitionCollection'] = SqlColumnDefinitionCollection::createFromArray($tableDefinition['fields'] ?? [], $table);
            $arguments['paletteDefinitionCollection'] = PaletteDefinitionCollection::createFromArray($tableDefinition['palettes'] ?? [], $table);
            $typeDefinitions = $tableDefinition['typeDefinitions'] ?? [];
            $typeDefinitionCollection = ContentTypeDefinitionCollection::createFromArray($typeDefinitions, $table);
            $arguments['contentTypeDefinitionCollection'] = $typeDefinitionCollection;
            $capability = TableDefinitionCapability::createFromArray($tableDefinition['raw'] ?? []);
            $references = $compilationResult->getParentReferences()[$table] ?? [];
            $parentReferences = [];
            foreach ($references as $reference) {
                $parentReferences[] = TcaFieldFactory::create($reference);
            }
            $arguments['parentReferences'] = $parentReferences;
            $capability = $this->extendCapability($parentReferences, $capability);
            $arguments['capability'] = $capability;
            $newTableDefinition = new TableDefinition(...$arguments);
            $tableDefinitionCollection->addTable($newTableDefinition);
        }
        return $tableDefinitionCollection;
    }

    /**
     * @param TcaFieldDefinition[] $parentReferences
     */
    private function extendCapability(array $parentReferences, TableDefinitionCapability $capability): TableDefinitionCapability
    {
        // If root Content Type is a Content Element, allow the external table to be put in standard pages.
        foreach ($parentReferences as $reference) {
            if (in_array($reference->parentContentType, [ContentType::CONTENT_ELEMENT, ContentType::PAGE_TYPE], true)) {
                $capability = $capability->withIgnorePageTypeRestriction(true);
            }
        }
        return $capability;
    }

    private function getFromCache(): false|TableDefinitionCollection
    {
        return $this->cache->require('ContentBlocks_Compiled');
    }

    private function setCache(): void
    {
        $data = 'return ' . VarExporter::export($this->tableDefinitionCollection) . ';';
        $this->cache->set('ContentBlocks_Compiled', $data);
    }
}
