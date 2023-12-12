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

namespace TYPO3\CMS\ContentBlocks\Definition;

use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentElementDefinition;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;
use TYPO3\CMS\ContentBlocks\Registry\AutomaticLanguageKeysRegistry;

/**
 * @internal Not part of TYPO3's public API.
 */
final class TableDefinitionCollection implements \IteratorAggregate
{
    /** @var TableDefinition[] */
    private array $definitions = [];

    public function __construct(private readonly AutomaticLanguageKeysRegistry $automaticLanguageKeysRegistry) {}

    /**
     * @return \Iterator<TableDefinition>
     */
    public function getIterator(): \Iterator
    {
        return new \ArrayIterator($this->definitions);
    }

    public function addTable(TableDefinition $tableDefinition): void
    {
        if (!$this->hasTable($tableDefinition->getTable())) {
            $this->definitions[$tableDefinition->getTable()] = $tableDefinition;
        }
    }

    public function getTable(string $table): TableDefinition
    {
        if ($this->hasTable($table)) {
            return $this->definitions[$table];
        }
        throw new \OutOfBoundsException('The table "' . $table . '" does not exist.', 1628925803);
    }

    public function hasTable(string $table): bool
    {
        return isset($this->definitions[$table]);
    }

    public function getContentElementDefinition(string $CType): ContentElementDefinition
    {
        $contentElementTable = ContentType::CONTENT_ELEMENT->getTable();
        if (!$this->hasTable($contentElementTable)) {
            throw new \InvalidArgumentException(
                'No definition for table "' . $contentElementTable . '" exists.',
                1702413869
            );
        }
        $contentElementCollection = $this->getTable($contentElementTable)
            ->getContentTypeDefinitionCollection();
        if (!$contentElementCollection->hasType($CType)) {
            throw new \InvalidArgumentException(
                'No Content Element for typeName "' . $CType . '" exists.',
                1702413909
            );
        }
        /** @var ContentElementDefinition $contentElementDefinition */
        $contentElementDefinition = $contentElementCollection->getType($CType);
        return $contentElementDefinition;
    }

    public function getAutomaticLanguageKeysRegistry(): AutomaticLanguageKeysRegistry
    {
        return $this->automaticLanguageKeysRegistry;
    }
}
