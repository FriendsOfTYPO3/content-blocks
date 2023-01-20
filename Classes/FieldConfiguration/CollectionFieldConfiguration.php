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

final class CollectionFieldConfiguration implements FieldConfigurationInterface
{
    private FieldType $fieldType = FieldType::COLLECTION;
    private bool $readOnly = false;
    private int $size = 0;
    private bool $localizeReferencesAtParentLocalization = false;
    private int $maxitems = 0;
    private int $minitems = 0;
    private string $MM = '';
    private bool $MM_hasUidField = false;
    private string $MM_opposite_field = '';
    private string $foreign_table = '';
    private int $autoSizeMax = 0;
    private array $filter = [];
    private array $appearance = [];
    private array $behaviour = [];
    private array $customControls = [];
    private string $foreign_default_sortby = '';
    private string $foreign_field = '';
    private string $foreign_label = '';
    private array $foreign_match_fields = [];
    private string $foreign_selector = '';
    private string $foreign_sortby = '';
    private string $foreign_table_field = '';
    private string $foreign_unique = '';
    private array $overrideChildTca = [];
    private string $symmetric_field = '';
    private string $symmetric_label = '';
    private string $symmetric_sortby = '';

    public static function createFromArray(array $settings): CollectionFieldConfiguration
    {
        $self = new self();
        $properties = $settings['properties'] ?? [];
        $self->readOnly = (bool)($properties['readOnly'] ?? $self->readOnly);
        $self->size = (int)($properties['size'] ?? $self->size);
        $self->localizeReferencesAtParentLocalization = (bool)($properties['localizeReferencesAtParentLocalization'] ?? $self->localizeReferencesAtParentLocalization);
        $self->maxitems = (int)($properties['maxitems'] ?? $self->maxitems);
        $self->minitems = (int)($properties['minitems'] ?? $self->minitems);
        $self->MM = (string)($properties['MM'] ?? $self->MM);
        $self->MM_hasUidField = (bool)($properties['MM_hasUidField'] ?? $self->MM_hasUidField);
        $self->MM_opposite_field = (string)($properties['MM_opposite_field'] ?? $self->MM_opposite_field);
        $self->foreign_table = (string)($properties['foreign_table'] ?? $self->foreign_table);
        $self->autoSizeMax = (int)($properties['autoSizeMax'] ?? $self->autoSizeMax);
        $self->filter = (array)($properties['filter'] ?? $self->filter);
        $self->appearance = (array)($properties['appearance'] ?? $self->appearance);
        $self->behaviour = (array)($properties['behaviour'] ?? $self->behaviour);
        $self->customControls = (array)($properties['customControls'] ?? $self->customControls);
        $self->foreign_default_sortby = (string)($properties['foreign_default_sortby'] ?? $self->foreign_default_sortby);
        $self->foreign_field = (string)($properties['foreign_field'] ?? $self->foreign_field);
        $self->foreign_label = (string)($properties['foreign_label'] ?? $self->foreign_label);
        $self->foreign_match_fields = (array)($properties['foreign_match_fields'] ?? $self->foreign_match_fields);
        $self->foreign_selector = (string)($properties['foreign_selector'] ?? $self->foreign_selector);
        $self->foreign_sortby = (string)($properties['foreign_sortby'] ?? $self->foreign_sortby);
        $self->foreign_table_field = (string)($properties['foreign_table_field'] ?? $self->foreign_table_field);
        $self->foreign_unique = (string)($properties['foreign_unique'] ?? $self->foreign_unique);
        $self->overrideChildTca = (array)($properties['overrideChildTca'] ?? $self->overrideChildTca);
        $self->symmetric_field = (string)($properties['symmetric_field'] ?? $self->symmetric_field);
        $self->symmetric_label = (string)($properties['symmetric_label'] ?? $self->symmetric_label);
        $self->symmetric_sortby = (string)($properties['symmetric_sortby'] ?? $self->symmetric_sortby);
        return $self;
    }

    public function getTca(string $languagePath, bool $useExistingField): array
    {
        if (!$useExistingField) {
            $tca['exclude'] = true;
        }
        $tca['label'] = 'LLL:' . $languagePath . '.label';
        $tca['description'] = 'LLL:' . $languagePath . '.description';
        $config['type'] = $this->fieldType->getTcaType();
        if ($this->readOnly) {
            $config['readOnly'] = true;
        }
        if ($this->size > 0) {
            $config['size'] = $this->size;
        }
        if ($this->localizeReferencesAtParentLocalization) {
            $config['localizeReferencesAtParentLocalization'] = true;
        }
        if ($this->maxitems > 0) {
            $config['maxitems'] = $this->maxitems;
        }
        if ($this->minitems > 0) {
            $config['minitems'] = $this->minitems;
        }
        if ($this->MM !== '') {
            $config['MM'] = $this->MM;
        }
        if ($this->MM_hasUidField) {
            $config['MM_hasUidField'] = true;
        }
        if ($this->MM_opposite_field !== '') {
            $config['MM_opposite_field'] = $this->MM_opposite_field;
        }
        if ($this->foreign_table !== '') {
            $config['foreign_table'] = $this->foreign_table;
        }
        if ($this->autoSizeMax > 0) {
            $config['autoSizeMax'] = $this->autoSizeMax;
        }
        if ($this->filter !== []) {
            $config['filter'] = $this->filter;
        }
        if ($this->appearance !== []) {
            $config['appearance'] = $this->appearance;
        }
        if ($this->behaviour !== []) {
            $config['behaviour'] = $this->behaviour;
        }
        if ($this->customControls !== []) {
            $config['customControls'] = $this->customControls;
        }
        if ($this->foreign_default_sortby !== '') {
            $config['foreign_default_sortby'] = $this->foreign_default_sortby;
        }
        if ($this->foreign_field !== '') {
            $config['foreign_field'] = $this->foreign_field;
        }
        if ($this->foreign_label !== '') {
            $config['foreign_label'] = $this->foreign_label;
        }
        if ($this->foreign_match_fields !== []) {
            $config['foreign_match_fields'] = $this->foreign_match_fields;
        }
        if ($this->foreign_selector !== '') {
            $config['foreign_selector'] = $this->foreign_selector;
        }
        if ($this->foreign_sortby !== '') {
            $config['foreign_sortby'] = $this->foreign_sortby;
        }
        if ($this->foreign_table_field !== '') {
            $config['foreign_table_field'] = $this->foreign_table_field;
        }
        if ($this->foreign_unique !== '') {
            $config['foreign_unique'] = $this->foreign_unique;
        }
        if ($this->overrideChildTca !== []) {
            $config['overrideChildTca'] = $this->overrideChildTca;
        }
        if ($this->symmetric_field !== '') {
            $config['symmetric_field'] = $this->symmetric_field;
        }
        if ($this->symmetric_label !== '') {
            $config['symmetric_label'] = $this->symmetric_label;
        }
        if ($this->symmetric_sortby !== '') {
            $config['symmetric_sortby'] = $this->symmetric_sortby;
        }
        $tca['config'] = $config;
        return $tca;
    }

    public function getSql(string $uniqueColumnName): string
    {
        return "`$uniqueColumnName` int(11) DEFAULT '0' NOT NULL";
    }

    public function getFieldType(): FieldType
    {
        return $this->fieldType;
    }
}
