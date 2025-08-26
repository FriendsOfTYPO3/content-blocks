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

use Psr\Container\ContainerInterface;
use TYPO3\CMS\Core\Domain\Exception\RecordPropertyException;
use TYPO3\CMS\Core\Domain\RecordPropertyClosure;

class ContentBlockGridData implements ContainerInterface
{
    public function __construct(
        /** @var array<string, RelationGrid|null>|array<string, RenderedGridItem[]>|array<RecordPropertyClosure> */
        protected array $grids = [],
    ) {}

    public function get(string $id): mixed
    {
        if ($this->has($id) === false) {
            return null;
        }
        $property = $this->grids[$id];
        if ($property instanceof RecordPropertyClosure) {
            $property = $this->readPropertyClosure($id, $property);
            $this->grids[$id] = $property;
        }
        return $property;
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->grids);
    }

    private function readPropertyClosure(string $id, RecordPropertyClosure $closure): mixed
    {
        try {
            $property = $closure->instantiate();
        } catch (\Exception $exception) {
            // Consumers of this method can rely on catching ContainerExceptionInterface
            throw new RecordPropertyException(
                'An exception occurred while instantiating record property "' . $id . '"',
                1756308142,
                $exception
            );
        }
        return $property;
    }
}
