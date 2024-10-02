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

use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

final readonly class FieldTypeRegistry
{
    public function __construct(
        #[AutowireLocator(FieldType::TAG_NAME, indexAttribute: 'name')]
        private ServiceLocator $types,
        #[AutowireIterator(FieldType::TAG_NAME)]
        private iterable $fieldTypes,
    ) {}

    public function has(string $type): bool
    {
        return $this->types->has($type);
    }

    public function get(string $type): FieldTypeInterface
    {
        return $this->types->get($type);
    }

    /**
     * @return iterable<FieldTypeInterface>
     */
    public function all(): iterable
    {
        return $this->fieldTypes;
    }

    /**
     * @return FieldTypeInterface[]
     */
    public function toArray(): array
    {
        $allFieldTypes = [];
        foreach ($this->fieldTypes as $fieldType) {
            $allFieldTypes[] = $fieldType;
        }
        return $allFieldTypes;
    }

    public function getAttribute(FieldTypeInterface $fieldType): FieldType
    {
        $reflectionClass = new \ReflectionClass($fieldType::class);
        $attribute = $reflectionClass->getAttributes(FieldType::class)[0];
        $instance = $attribute->newInstance();
        return $instance;
    }
}
