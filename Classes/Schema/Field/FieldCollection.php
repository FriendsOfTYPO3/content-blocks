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

namespace TYPO3\CMS\ContentBlocks\Schema\Field;

/**
 * @internal Not part of TYPO3's public API.
 */
final class FieldCollection implements \ArrayAccess, \IteratorAggregate, \Countable
{
    public function __construct(
        /**
         * @var array<string, FieldTypeInterface> $fieldDefinitions
         */
        protected array $fieldDefinitions = []
    ) {}

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->fieldDefinitions[$offset]);
    }

    public function offsetGet(mixed $offset): ?FieldTypeInterface
    {
        return $this->fieldDefinitions[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->fieldDefinitions[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->fieldDefinitions[$offset]);
    }

    /**
     * @return \Traversable|FieldTypeInterface[]
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->fieldDefinitions);
    }

    public function count(): int
    {
        return count($this->fieldDefinitions);
    }
}
