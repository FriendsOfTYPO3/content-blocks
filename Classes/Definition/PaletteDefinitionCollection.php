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
final class PaletteDefinitionCollection implements \IteratorAggregate, \Countable
{
    /**
     * @var array<PaletteDefinition>
     */
    private array $definitions = [];
    public string $table = '';

    public function addPalette(PaletteDefinition $definition): void
    {
        if (!$this->hasPalette($definition->getIdentifier())) {
            $this->definitions[$definition->getIdentifier()] = $definition;
        }
    }

    public function hasPalette(string $key): bool
    {
        return isset($this->definitions[$key]);
    }

    public function getPalette(string $key): PaletteDefinition
    {
        if ($this->hasPalette($key)) {
            return $this->definitions[$key];
        }

        throw new \OutOfBoundsException('A palette with the key "' . $key . '" does not exist in table "' . $this->table . '".', 1629293912);
    }

    public static function createFromArray(array $array, string $table): PaletteDefinitionCollection
    {
        $paletteDefinitionCollection = new self();
        $paletteDefinitionCollection->table = $table;
        foreach ($array as $palette) {
            $paletteDefinitionCollection->addPalette(PaletteDefinition::createFromArray($palette));
        }
        return $paletteDefinitionCollection;
    }

    /**
     * @return \Iterator<PaletteDefinition>
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
