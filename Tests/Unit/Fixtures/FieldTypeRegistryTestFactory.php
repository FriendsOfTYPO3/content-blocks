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

namespace TYPO3\CMS\ContentBlocks\Tests\Unit\Fixtures;

use Symfony\Component\DependencyInjection\ServiceLocator;
use TYPO3\CMS\ContentBlocks\FieldType\CategoryFieldType;
use TYPO3\CMS\ContentBlocks\FieldType\CheckboxFieldType;
use TYPO3\CMS\ContentBlocks\FieldType\CollectionFieldType;
use TYPO3\CMS\ContentBlocks\FieldType\ColorFieldType;
use TYPO3\CMS\ContentBlocks\FieldType\DateTimeFieldType;
use TYPO3\CMS\ContentBlocks\FieldType\EmailFieldType;
use TYPO3\CMS\ContentBlocks\FieldType\FieldType;
use TYPO3\CMS\ContentBlocks\FieldType\FieldTypeInterface;
use TYPO3\CMS\ContentBlocks\FieldType\FieldTypeRegistry;
use TYPO3\CMS\ContentBlocks\FieldType\FileFieldType;
use TYPO3\CMS\ContentBlocks\FieldType\FlexFormFieldType;
use TYPO3\CMS\ContentBlocks\FieldType\FolderFieldType;
use TYPO3\CMS\ContentBlocks\FieldType\ImageManipulationFieldType;
use TYPO3\CMS\ContentBlocks\FieldType\JsonFieldType;
use TYPO3\CMS\ContentBlocks\FieldType\LanguageFieldType;
use TYPO3\CMS\ContentBlocks\FieldType\LinebreakFieldType;
use TYPO3\CMS\ContentBlocks\FieldType\LinkFieldType;
use TYPO3\CMS\ContentBlocks\FieldType\NumberFieldType;
use TYPO3\CMS\ContentBlocks\FieldType\PaletteFieldType;
use TYPO3\CMS\ContentBlocks\FieldType\PassFieldType;
use TYPO3\CMS\ContentBlocks\FieldType\PasswordFieldType;
use TYPO3\CMS\ContentBlocks\FieldType\RadioFieldType;
use TYPO3\CMS\ContentBlocks\FieldType\RelationFieldType;
use TYPO3\CMS\ContentBlocks\FieldType\SelectFieldType;
use TYPO3\CMS\ContentBlocks\FieldType\SlugFieldType;
use TYPO3\CMS\ContentBlocks\FieldType\TabFieldType;
use TYPO3\CMS\ContentBlocks\FieldType\TextareaFieldType;
use TYPO3\CMS\ContentBlocks\FieldType\TextFieldType;
use TYPO3\CMS\ContentBlocks\FieldType\UuidFieldType;

class FieldTypeRegistryTestFactory
{
    public static function create(): FieldTypeRegistry
    {
        $fieldTypes = [
            new CategoryFieldType(),
            new CheckboxFieldType(),
            new CollectionFieldType(),
            new ColorFieldType(),
            new DateTimeFieldType(),
            new EmailFieldType(),
            new FileFieldType(),
            new FlexFormFieldType(),
            new FolderFieldType(),
            new ImageManipulationFieldType(),
            new JsonFieldType(),
            new LanguageFieldType(),
            new LinebreakFieldType(),
            new LinkFieldType(),
            new NumberFieldType(),
            new PaletteFieldType(),
            new PasswordFieldType(),
            new RadioFieldType(),
            new RelationFieldType(),
            new SelectFieldType(),
            new SlugFieldType(),
            new TabFieldType(),
            new TextareaFieldType(),
            new TextFieldType(),
            new UuidFieldType(),
            new PassFieldType(),
        ];
        $keyedFieldTypes = [];
        foreach ($fieldTypes as $fieldType) {
            $reflectionClass = new \ReflectionClass($fieldType);
            $attributes = $reflectionClass->getAttributes(FieldType::class);
            $fieldTypeAttribute = $attributes[0]->newInstance();
            $fieldType->setName($fieldTypeAttribute->name);
            $fieldType->setTcaType($fieldTypeAttribute->tcaType);
            $fieldType->setSearchable($fieldTypeAttribute->searchable);
            $keyedFieldTypes[$fieldType->getName()] = fn(): FieldTypeInterface => $fieldType;
        }
        $serviceLocator = new ServiceLocator($keyedFieldTypes);
        return new FieldTypeRegistry($serviceLocator);
    }
}
