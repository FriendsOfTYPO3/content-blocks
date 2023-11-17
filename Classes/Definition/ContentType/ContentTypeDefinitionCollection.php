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

namespace TYPO3\CMS\ContentBlocks\Definition\ContentType;

use TYPO3\CMS\ContentBlocks\Definition\Factory\ContentTypeFactory;

/**
 * @internal Not part of TYPO3's public API.
 */
final class ContentTypeDefinitionCollection implements \IteratorAggregate, \Countable
{
    /** @var array<string|int, ContentTypeInterface> */
    private array $definitions = [];
    private string $table = '';

    public function addType(ContentTypeInterface $definition): void
    {
        if (!$this->hasType($definition->getTypeName())) {
            $this->definitions[$definition->getTypeName()] = $definition;
        }
    }

    public function hasType(string|int $typeName): bool
    {
        return isset($this->definitions[$typeName]);
    }

    public function getType(string|int $typeName): ContentTypeInterface
    {
        if ($this->hasType($typeName)) {
            return $this->definitions[$typeName];
        }

        throw new \OutOfBoundsException('A type with the identifier "' . $typeName . '" does not exist in table "' . $this->table . '".', 1629292879);
    }

    public function getFirst(): ContentTypeInterface
    {
        if ($this->count() === 0) {
            throw new \OutOfBoundsException('The table "' . $this->table . '" has no type definitions.', 1686340482);
        }
        return current($this->definitions);
    }

    public static function createFromArray(array $array, string $table): ContentTypeDefinitionCollection
    {
        $contentTypeFactory = new ContentTypeFactory();
        $typeDefinitionCollection = new self();
        $typeDefinitionCollection->table = $table;
        foreach ($array as $type) {
            $contentTypeDefinition = $contentTypeFactory->create($type, $table);
            $typeDefinitionCollection->addType($contentTypeDefinition);
        }
        return $typeDefinitionCollection;
    }

    /**
     * @return \Iterator<ContentTypeInterface>
     */
    public function getIterator(): \Iterator
    {
        return new \ArrayIterator($this->sort());
    }

    public function count(): int
    {
        return count($this->definitions);
    }

    private function sort(): array
    {
        $types = $this->definitions;
        usort($types, fn(ContentTypeInterface $a, ContentTypeInterface $b): int => $b->getPriority() <=> $a->getPriority());
        return $types;
    }
}
