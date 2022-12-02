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

/**
 * class CollectionFieldConfiguration
 */
class CollectionFieldConfiguration extends AbstractFieldConfiguration implements FieldConfigurationInterface
{
    public bool $collapseAll = true;

    public array|bool $enabledControls = true;

    public bool $enableSorting = true;

    public bool $expandSingle = true;

    public array $fields = [];

    public int $maxItems = 0;

    public int $minItems = 0;

    public string $useAsLabel = '';

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
            'type' => $this->type,
            'foreign_table' => $this->rawData['properties']['foreign_table'],
            'foreign_field' => $this->rawData['properties']['foreign_field'],
            'appearance' => [
                'collapseAll' => $this->collapseAll,
                'expandSingle' => $this->expandSingle,
                'useSortable' => $this->enableSorting,
                'enabledControls' => [
                    'delete' => !isset($this->enabledControls['delete']) || $this->enabledControls['delete'],
                    'dragdrop' => !isset($this->enabledControls['dragdrop']) || $this->enabledControls['dragdrop'],
                    'new' => !isset($this->enabledControls['new']) || $this->enabledControls['new'],
                    'hide' => !isset($this->enabledControls['hide']) || $this->enabledControls['hide'],
                    'info' => !isset($this->enabledControls['info']) || $this->enabledControls['info'],
                    'localize' => !isset($this->enabledControls['localize']) || $this->enabledControls['localize'],
                ],
            ],
        ];

        if ( $this->maxItems !== 0) {
            $tca['config']['maxitems'] = $this->maxItems;
        }
        if ( $this->minItems !== 0) {
            $tca['config']['minitems'] = $this->minItems;
        }

        // Label field:
        if ($this->useAsLabel !== '' && count($this->fields) > 0) {
            $labelField = array_column($this->fields, null, 'identifier');
            $labelField = $labelField[ $this->useAsLabel ];

            if (
                strlen('' . $labelField['identifier']) > 0
                && FieldType::from($labelField['type'])->dataProcessingBehaviour() === 'renderable'
            ) {
                $tca['config']['foreign_label'] = $this->useAsLabel;
            }
        }
        return $tca;
    }

    /**
     * Get SQL definition for this inputfield
     */
    public function getSql(string $uniqueColumnName): string
    {
        return "`$uniqueColumnName` int(11) DEFAULT '0' NOT NULL";
    }

    /**
     * Fills the properties from array infos
     */
    protected function createFromArray(array $settings): self
    {
        parent::createFromArray($settings);
        $this->type = FieldType::COLLECTION->getTcaType();
        $this->collapseAll = $settings['properties']['collapseAll'] ?? $this->collapseAll;
        $this->enabledControls = $settings['properties']['enabledControls'] ?? $this->enabledControls;
        $this->enableSorting = $settings['properties']['enableSorting'] ?? $this->enableSorting;
        $this->expandSingle = $settings['properties']['expandSingle'] ?? $this->expandSingle;
        $this->fields = ($settings['properties']['fields'] ?? $this->fields);
        $this->useAsLabel = ($settings['properties']['useAsLabel'] ?? $this->useAsLabel);

        if (isset($settings['properties']['maxItems']) && is_int($settings['properties']['maxItems'])) {
            $this->maxItems = (int)$settings['properties']['maxItems'];
        }

        if (isset($settings['properties']['minItems']) && is_int($settings['properties']['minItems'])) {
            $this->minItems = (int)$settings['properties']['minItems'];
        }
        return $this;
    }

    /**
     * Get the InputFieldConfiguration as array
     */
    public function toArray(): array
    {
        return [
            'identifier' => $this->identifier,
            'type' => $this->type,
            'properties' => [
                'collapseAll' => $this->collapseAll,
                'enabledControls' => $this->enabledControls,
                'enableSorting' => $this->enableSorting,
                'expandSingle' => $this->expandSingle,
                'fields' => $this->fields,
                'maxItems' => $this->maxItems,
                'minItems' => $this->minItems,
                'useAsLabel' => $this->useAsLabel,
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
        return [
            'rows' => 'double',
            // property "required" is a "boolean" -> e.g. should be rendered as a checkbox
            'required' => 'boolean',
        ];
    }

    public function getTemplateHtml(int $indentation): string
    {
        return str_repeat(' ', $indentation * 4) . '<p>{' . $this->uniqueIdentifier . '}</p>' . "\n";
    }
}
