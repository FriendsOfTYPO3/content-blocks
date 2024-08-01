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

use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class FieldTypeRegistry implements \IteratorAggregate
{
    /**
     * @var FieldTypeInterface[]
     */
    protected array $fieldTypesArray;

    /**
     * @var FieldTypeInterface[]
     */
    protected array $nativeFieldTypesArray;

    public function __construct(
        /**
         * @var \IteratorAggregate<FieldTypeInterface>
         */
        #[TaggedIterator('content_blocks.field_type', defaultIndexMethod: 'getName')]
        protected \IteratorAggregate $fieldTypes,
        #[TaggedIterator('content_blocks.field_type', defaultIndexMethod: 'getTcaType')]
        protected \IteratorAggregate $nativeFieldTypes,
    ) {
        $this->fieldTypesArray = iterator_to_array($this->fieldTypes);
        $this->nativeFieldTypesArray = iterator_to_array($this->nativeFieldTypes);
    }

    public function has(string $fieldTypeName): bool
    {
        return array_key_exists($fieldTypeName, $this->fieldTypesArray);
    }

    public function get(string $fieldTypeName): FieldTypeInterface
    {

        if (
            !array_key_exists($fieldTypeName, $this->fieldTypesArray)
            && !array_key_exists($fieldTypeName, $this->nativeFieldTypesArray)
        ) {
            throw new \InvalidArgumentException(
                'Field type with name "' . $fieldTypeName . '" does not exist',
                1710083790
            );
        }
        return $this->fieldTypesArray[$fieldTypeName] ?? $this->nativeFieldTypesArray[$fieldTypeName];
    }

    /**
     * @return FieldTypeInterface[]
     */
    public function getAll(): array
    {
        return $this->fieldTypesArray;
    }

    public function getIterator(): \Traversable
    {
        return $this->fieldTypes;
    }
}
