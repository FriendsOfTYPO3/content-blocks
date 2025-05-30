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

readonly class BaseFieldTypeRegistryFactory
{
    public function __construct(
        protected FieldTypeRegistry $fieldTypeRegistry,
    ) {}

    public function create(): BaseFieldTypeRegistry
    {
        $fieldTypesByTcaType = [];
        foreach ($this->getBaseFieldTypes() as $type) {
            $baseFieldType = $this->fieldTypeRegistry->get($type);
            $fieldTypesByTcaType[$baseFieldType->getTcaType()] = $baseFieldType;
        }
        return new BaseFieldTypeRegistry($fieldTypesByTcaType);
    }

    /**
     * @return string[]
     */
    protected function getBaseFieldTypes(): array
    {
        $baseFieldTypes = [
            'Category',
            'Checkbox',
            'Collection',
            'Color',
            'DateTime',
            'Email',
            'File',
            'FlexForm',
            'Folder',
            'ImageManipulation',
            'Json',
            'Language',
            'Link',
            'Number',
            'Pass',
            'Password',
            'Radio',
            'Relation',
            'Select',
            'Slug',
            'Textarea',
            'Text',
            'Uuid',
        ];
        return $baseFieldTypes;
    }
}
