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

use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentTypeInterface;
use TYPO3\CMS\ContentBlocks\Definition\Factory\ContentTypeFactory;

/**
 * @internal Not part of TYPO3's public API.
 */
final class TypeDefinitionCollection implements \IteratorAggregate, \Countable
{
    /** @var ContentTypeInterface[] */
    private array $definitions = [];
    private string $table = '';

    public function addType(ContentTypeInterface $definition): void
    {
        if (!$this->hasType($definition->getIdentifier())) {
            $this->definitions[$definition->getIdentifier()] = $definition;
        }
    }

    public function hasType(string $identifier): bool
    {
        return isset($this->definitions[$identifier]);
    }

    public function getType(string $identifier): ContentTypeInterface
    {
        if ($this->hasType($identifier)) {
            return $this->definitions[$identifier];
        }

        throw new \OutOfBoundsException('A type with the identifier "' . $identifier . '" does not exist in table "' . $this->table . '".', 1629292879);
    }

    public function getFirst(): ContentTypeInterface
    {
        if ($this->count() === 0) {
            throw new \OutOfBoundsException('The table "' . $this->table . '" has no type definitions.', 1686340482);
        }
        return current($this->definitions);
    }

    public static function createFromArray(array $array, string $table): TypeDefinitionCollection
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

    public function getIterator(): \Traversable
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
        usort($types, fn (ContentTypeInterface $a, ContentTypeInterface $b): int => $b->getPriority() <=> $a->getPriority());
        return $types;
    }
}
