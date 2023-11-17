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

/**
 * @internal Not part of TYPO3's public API.
 */
final class TcaFieldDefinitionCollection implements \IteratorAggregate, \Countable
{
    /** @var TcaFieldDefinition[] */
    private array $definitions = [];
    private string $table = '';

    public function addField(TcaFieldDefinition $definition): void
    {
        if (!$this->hasField($definition->getUniqueIdentifier())) {
            $this->definitions[$definition->getUniqueIdentifier()] = $definition;
        }
    }

    public function hasField(string $key): bool
    {
        return isset($this->definitions[$key]);
    }

    public function getField(string $key): TcaFieldDefinition
    {
        if ($this->hasField($key)) {
            return $this->definitions[$key];
        }

        throw new \OutOfBoundsException('A field with the key "' . $key . '" does not exist in table "' . $this->table . '".', 1629276302);
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public static function createFromArray(array $tca, string $table): TcaFieldDefinitionCollection
    {
        $tcaDefinition = new self();
        $tcaDefinition->table = $table;
        foreach ($tca as $definition) {
            $tcaDefinition->addField(TcaFieldDefinition::createFromArray($definition));
        }
        return $tcaDefinition;
    }

    public function getKeys(): array
    {
        return array_keys($this->definitions);
    }

    /**
     * @return \Iterator<TcaFieldDefinition>
     */
    public function getIterator(): \Iterator
    {
        return new \ArrayIterator($this->definitions);
    }

    public function count(): int
    {
        return count($this->definitions);
    }
}
