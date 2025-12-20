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

namespace TYPO3\CMS\ContentBlocks\Definition\Factory;

use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentTypeDefinitionCollection;
use TYPO3\CMS\ContentBlocks\Definition\TcaFieldDefinition;
use TYPO3\CMS\ContentBlocks\FieldType\FieldTypeInterface;

readonly class TcaFieldFactory
{
    public static function create(array $array): TcaFieldDefinition
    {
        $arguments = [];
        $arguments['uniqueIdentifier'] = (string)($array['uniqueIdentifier'] ?? '');
        $arguments['parentContentType'] = ContentType::getByTable($array['parentTable'] ?? '');
        $arguments['identifier'] = $array['config']['identifier'];
        $arguments['labelPath'] = $array['labelPath'] ?? '';
        $arguments['descriptionPath'] = $array['descriptionPath'] ?? '';
        $arguments['useExistingField'] = $array['config']['useExistingField'] ?? false;
        /** @var FieldTypeInterface $fieldType */
        $fieldType = $array['type'];
        $fieldTypeHydrated = $fieldType->createFromArray($array['config']);
        $arguments['fieldType'] = $fieldTypeHydrated;
        $tcaType = $fieldType->getTcaType();
        $typeDefinitions = $array['config']['typeOverrides'] ?? [];
        if ($typeDefinitions !== [] && ($tcaType === 'file' || $tcaType === 'inline')) {
            $table = match ($tcaType) {
                'file' => 'sys_file_reference',
                'inline' => $fieldTypeHydrated->getTca()['config']['foreign_table'],
            };
            $typeDefinitionCollection = ContentTypeDefinitionCollection::createFromArray($typeDefinitions, $table);
            $arguments['typeOverrides'] = $typeDefinitionCollection;
        }
        $tcaFieldDefinition = new TcaFieldDefinition(...$arguments);
        return $tcaFieldDefinition;
    }
}
