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

final class SelectFieldConfiguration implements FieldConfigurationInterface
{
    private FieldType $fieldType = FieldType::SELECT;
    private string|int $default = '';
    private string $renderType = '';
    private bool $readOnly = false;
    private int $size = 0;
    private string $MM = '';
    private bool $MM_hasUidField = false;
    private string $MM_opposite_field = '';
    private array $MM_insert_fields = [];
    private array $MM_match_fields = [];
    private string $MM_oppositeUsage = '';
    private string $MM_table_where = '';
    private string $dontRemapTablesOnCopy = '';
    private bool $localizeReferencesAtParentLocalization = false;
    private int $maxitems = 0;
    private int $minitems = 0;
    private string $foreign_table = '';
    private string $itemsProcFunc = '';
    private bool $allowNonIdValues = false;
    private string $authMode = '';
    private bool $disableNoMatchingValueElement = false;
    private string $exclusiveKeys = '';
    private array $fileFolderConfig = [];
    private string $foreign_table_prefix = '';
    private string $foreign_table_where = '';
    private array $itemGroups = [];
    private array $items = [];
    private array $sortItems = [];

    // Only for renderType="selectCheckBox"
    private array $appearance = [];

    // Only for renderType="selectTree"
    private array $treeConfig = [];

    public static function createFromArray(array $settings): SelectFieldConfiguration
    {
        $self = new self();
        $properties = $settings['properties'] ?? [];
        $default = $properties['default'] ?? $self->default;
        if (is_string($default) || is_int($default)) {
            $self->default = $default;
        }
        $self->renderType = (string)($properties['renderType'] ?? $self->renderType);
        $self->readOnly = (bool)($properties['readOnly'] ?? $self->readOnly);
        $self->size = (int)($properties['size'] ?? $self->size);
        $self->MM = (string)($properties['MM'] ?? $self->MM);
        $self->MM_hasUidField = (bool)($properties['MM_hasUidField'] ?? $self->MM_hasUidField);
        $self->MM_opposite_field = (string)($properties['MM_opposite_field'] ?? $self->MM_opposite_field);
        $self->MM_insert_fields = (array)($properties['MM_insert_fields'] ?? $self->MM_insert_fields);
        $self->MM_match_fields = (array)($properties['MM_match_fields'] ?? $self->MM_match_fields);
        $self->MM_oppositeUsage = (string)($properties['MM_oppositeUsage'] ?? $self->MM_oppositeUsage);
        $self->MM_table_where = (string)($properties['MM_table_where'] ?? $self->MM_table_where);
        $self->dontRemapTablesOnCopy = (string)($properties['dontRemapTablesOnCopy'] ?? $self->dontRemapTablesOnCopy);
        $self->localizeReferencesAtParentLocalization = (bool)($properties['localizeReferencesAtParentLocalization'] ?? $self->localizeReferencesAtParentLocalization);
        $self->maxitems = (int)($properties['maxitems'] ?? $self->maxitems);
        $self->minitems = (int)($properties['minitems'] ?? $self->minitems);
        $self->foreign_table = (string)($properties['foreign_table'] ?? $self->foreign_table);
        $self->itemsProcFunc = (string)($properties['itemsProcFunc'] ?? $self->itemsProcFunc);
        $self->allowNonIdValues = (bool)($properties['allowNonIdValues'] ?? $self->allowNonIdValues);
        $self->authMode = (string)($properties['authMode'] ?? $self->authMode);
        $self->disableNoMatchingValueElement = (bool)($properties['disableNoMatchingValueElement'] ?? $self->disableNoMatchingValueElement);
        $self->exclusiveKeys = (string)($properties['exclusiveKeys'] ?? $self->exclusiveKeys);
        $self->fileFolderConfig = (array)($properties['fileFolderConfig'] ?? $self->fileFolderConfig);
        $self->foreign_table_prefix = (string)($properties['foreign_table_prefix'] ?? $self->foreign_table_prefix);
        $self->foreign_table_where = (string)($properties['foreign_table_where'] ?? $self->foreign_table_where);
        $self->itemGroups = (array)($properties['itemGroups'] ?? $self->itemGroups);
        $self->items = (array)($properties['items'] ?? $self->items);
        $self->sortItems = (array)($properties['sortItems'] ?? $self->sortItems);
        $self->appearance = (array)($properties['appearance'] ?? $self->appearance);
        $self->treeConfig = (array)($properties['treeConfig'] ?? $self->treeConfig);

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
        if ($this->renderType !== '') {
            $config['renderType'] = $this->renderType;
        }
        if ($this->default !== '') {
            $config['default'] = $this->default;
        }
        if ($this->readOnly) {
            $config['readOnly'] = true;
        }
        if ($this->size > 0) {
            $config['size'] = $this->size;
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
        if ($this->MM_insert_fields !== []) {
            $config['MM_insert_fields'] = $this->MM_insert_fields;
        }
        if ($this->MM_match_fields !== []) {
            $config['MM_match_fields'] = $this->MM_match_fields;
        }
        if ($this->MM_oppositeUsage !== '') {
            $config['MM_oppositeUsage'] = $this->MM_oppositeUsage;
        }
        if ($this->MM_table_where !== '') {
            $config['MM_table_where'] = $this->MM_table_where;
        }
        if ($this->dontRemapTablesOnCopy !== '') {
            $config['dontRemapTablesOnCopy'] = $this->dontRemapTablesOnCopy;
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
        if ($this->foreign_table !== '') {
            $config['foreign_table'] = $this->foreign_table;
        }
        if ($this->itemsProcFunc !== '') {
            $config['itemsProcFunc'] = $this->itemsProcFunc;
        }
        if ($this->allowNonIdValues) {
            $config['allowNonIdValues'] = true;
        }
        if ($this->authMode !== '') {
            $config['authMode'] = $this->authMode;
        }
        if ($this->disableNoMatchingValueElement) {
            $config['disableNoMatchingValueElement'] = true;
        }
        if ($this->exclusiveKeys !== '') {
            $config['exclusiveKeys'] = $this->exclusiveKeys;
        }
        if ($this->fileFolderConfig !== []) {
            $config['fileFolderConfig'] = $this->fileFolderConfig;
        }
        if ($this->foreign_table_prefix !== '') {
            $config['foreign_table_prefix'] = $this->foreign_table_prefix;
        }
        if ($this->foreign_table_where !== '') {
            $config['foreign_table_where'] = $this->foreign_table_where;
        }
        if ($this->itemGroups !== []) {
            $config['itemGroups'] = $this->itemGroups;
        }
        if ($this->items !== []) {
            $config['items'] = $this->items;
        }
        if ($this->sortItems !== []) {
            $config['sortItems'] = $this->sortItems;
        }
        if ($this->appearance !== []) {
            $config['appearance'] = $this->appearance;
        }
        if ($this->treeConfig !== []) {
            $config['treeConfig'] = $this->treeConfig;
        }
        $tca['config'] = $config;
        return $tca;
    }

    public function getSql(string $uniqueColumnName): string
    {
        return "`$uniqueColumnName` VARCHAR(255) DEFAULT '' NOT NULL";
    }

    public function getFieldType(): FieldType
    {
        return $this->fieldType;
    }
}
