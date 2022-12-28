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
 * class FileFieldConfiguration
 */
class FileFieldConfiguration extends AbstractFieldConfiguration implements FieldConfigurationInterface
{
    public string $fileTypes = 'mixed';
    public string $allowedFileExtensions = '';
    public bool $enableImaManipulation = false;
    public int $maxItems = 0;
    public int $minItems = 0;

    public function getTca(): array
    {
        $tca = parent::getTcaTemplate();
        $tca['config'] = [
            'type' => $this->type,
        ];

        if ($this->allowedFileExtensions !== '') {
            $tca['config']['allowed'] = explode(',', $this->allowedFileExtensions);
        } else {
            $validatedFileTypes = ((in_array($this->fileTypes, ['image', 'video', 'audio', 'document', 'mixed']) ? $this->fileTypes : 'mixed'));
            $fileTypesTranslation = [
                'image' => 'common-image-types',
                'video' => 'common-media-types',
                'audio' => 'common-media-types',
                'document' => 'common-text-types',
                'mixed' => '',
            ];
            $allowed = $fileTypesTranslation[$validatedFileTypes];
            if ($validatedFileTypes !== 'mixed') {
                $tca['config']['allowed'] = $allowed;
            }
        }

        // @todo: what to do with enableImaManipulation?
        if ($this->enableImaManipulation) {
            $tca['config']['fieldWizard']['type'] = 'imageManipulation';
        }

        if ( $this->maxItems !== 0) {
            $tca['config']['maxitems'] = $this->maxItems;
        }
        if ( $this->minItems !== 0) {
            $tca['config']['minitems'] = $this->minItems;
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
    public static function createFromArray(array $settings): static
    {
        $self = parent::createFromArray($settings);
        $self->type = FieldType::FILE->getTcaType();
        $self->fileTypes = $settings['properties']['fileTypes'] ?? $self->fileTypes;
        $self->allowedFileExtensions = $settings['properties']['allowedFileExtensions'] ?? $self->allowedFileExtensions;
        $self->enableImaManipulation = $settings['properties']['enableImaManipulation'] ?? $self->enableImaManipulation;

        if (isset($settings['properties']['maxItems']) && is_int($settings['properties']['maxItems'])) {
            $self->maxItems = (int)$settings['properties']['maxItems'];
        }

        if (isset($settings['properties']['minItems']) && is_int($settings['properties']['minItems'])) {
            $self->minItems = (int)$settings['properties']['minItems'];
        }

        return $self;
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
                'fileTypes' => $this->fileTypes,
                'allowedFileExtensions' => $this->allowedFileExtensions,
                'enableImaManipulation' => $this->enableImaManipulation,
                'maxItems' => $this->maxItems,
                'minItems' => $this->minItems,
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
        if ($this->maxItems === 1 && $this->minItems === 1) {
            return str_repeat(' ', $indentation * 4) . '<f:image image="{' . $this->uniqueIdentifier . '}"/>' . "\n";
        }
        $imageTemplate = str_repeat(' ', $indentation * 4) .  '<f:for each="{' . $this->uniqueIdentifier . '}" as="i" iteration="iteration">' . "\n";
        $imageTemplate .= str_repeat(' ', ($indentation * 4) + 4) . '<f:image image="{i}" />' . "\n";
        $imageTemplate .= str_repeat(' ', $indentation * 4) . '</f:for>' . "\n";
        return $imageTemplate;
    }
}
