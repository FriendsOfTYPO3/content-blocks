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

/**
 * class NoneFieldConfiguration
 */
class NoneFieldConfiguration extends AbstractFieldConfiguration implements FieldConfigurationInterface
{
    /**
     * Construct: setting from yaml file needed to create a field configuration.
     */
    public function __construct(array $settings)
    {
        $this->createFromArray($settings);
    }

    /**
     * Get TCA for this inputfield
     */
    public function getTca(): array
    {
        $tca = parent::getTcaTemplate();
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

    /**
     * Fills the properties from array infos
     */
    protected function createFromArray(array $settings): self
    {
        parent::createFromArray($settings);

        return $this;
    }

    /**
     * Get the InputFieldConfiguration as array
     */
    public function toArray(): array
    {
        return [
            'identifier' => $this->identifier,
            'type' => 'none',
            'properties' => [
                'type' => 'none',
                'pass_content' => true,
            ],
            '_path' => $this->path,
            '_identifier' =>  $this->uniqueIdentifier,
        ];
    }

    /**
     * TODO: Idea: say what is allowed (properties and values) e.g. for backend modul inspektor of a input field.
     */
    public function getAllowedSettings(): array
    {
        return [];
    }

    public function getTemplateHtml(int $indentation): string
    {
        return "";
    }
}
