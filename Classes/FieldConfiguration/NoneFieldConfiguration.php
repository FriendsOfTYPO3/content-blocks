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

namespace TYPO3\CMS\ContentBlocks\FieldConfiguration;

use TYPO3\CMS\ContentBlocks\Enumeration\FieldType;

class NoneFieldConfiguration implements FieldConfigurationInterface
{
    use UniqueIdentifierTrait;

    public function getTca(string $languagePath, bool $useExistingField): array
    {
        $tca['config'] = [
            'type' => 'none',
            'pass_content' => true,
        ];
        return $tca;
    }

    /**
     * Get SQL definition for this inputfield
     */
    public function getSql(string $uniqueColumnName): string
    {
        return "`$uniqueColumnName` VARCHAR(55) DEFAULT '' NOT NULL";
    }

    public static function createFromArray(array $settings): static
    {
        return new self();
    }

    /**
     * Get the InputFieldConfiguration as array
     */
    public function toArray(): array
    {
        return [];
//        return [
//            'identifier' => $this->identifier,
//            'type' => 'none',
//            'properties' => [
//                'type' => 'none',
//                'pass_content' => true,
//            ],
//            '_path' => $this->path,
//            '_identifier' =>  $this->uniqueIdentifier,
//        ];
    }


    public function getHtmlTemplate(int $indentation, string $uniqueIdentifier): string
    {
        return '';
    }

    public function getFieldType(): FieldType
    {
        return FieldType::NONE;
    }
}
