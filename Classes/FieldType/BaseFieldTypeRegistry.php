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

namespace TYPO3\CMS\ContentBlocks\FieldType;

use Psr\Container\ContainerInterface;

final readonly class BaseFieldTypeRegistry implements ContainerInterface
{
    /**
     * @param array<string, FieldTypeInterface> $baseFieldTypes
     */
    public function __construct(
        private array $baseFieldTypes,
    ) {}

    public function get(string $id): FieldTypeInterface
    {
        if ($this->has($id) === false) {
            throw new \InvalidArgumentException(
                'Base Field Type with TCA type "' . $id . '" does not exist.',
                1748638034
            );
        }
        return $this->baseFieldTypes[$id];
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->baseFieldTypes);
    }
}
