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
 * @internal Not part of TYPO3's public API.
 */
final class RelationFieldConfiguration implements FieldConfigurationInterface
{
    use WithCommonProperties;

    private FieldType $fieldType = FieldType::RELATION;
    private string|int $default = '';
    private string $allowed = '';
    private string $foreign_table = '';
    private bool $readOnly = false;
    private int $size = 0;
    private int $maxitems = 0;
    private int $minitems = 0;
    private int $autoSizeMax = 0;
    private bool $multiple = false;
    private string $MM = '';
    private string $MM_opposite_field = '';
    private array $MM_match_fields = [];
    private string $MM_oppositeUsage = '';
    private string $MM_table_where = '';
    private string $dontRemapTablesOnCopy = '';
    private bool $localizeReferencesAtParentLocalization = false;
    private bool $hideMoveIcons = false;
    private bool $hideSuggest = false;
    private bool $prepend_tname = false;
    private array $elementBrowserEntryPoints = [];
    private array $filter = [];
    private array $suggestOptions = [];
    private array $appearance = [];

    public static function createFromArray(array $settings): RelationFieldConfiguration
    {
        $self = new self();
        $self->setCommonProperties($settings);
        $default = $settings['default'] ?? $self->default;
        if (is_string($default) || is_int($default)) {
            $self->default = $default;
        }
        $self->allowed = (string)($settings['allowed'] ?? $self->allowed);
        $self->foreign_table = (string)($settings['foreign_table'] ?? $self->foreign_table);
        $self->readOnly = (bool)($settings['readOnly'] ?? $self->readOnly);
        $self->size = (int)($settings['size'] ?? $self->size);
        $self->maxitems = (int)($settings['maxitems'] ?? $self->maxitems);
        $self->minitems = (int)($settings['minitems'] ?? $self->minitems);
        $self->autoSizeMax = (int)($settings['autoSizeMax'] ?? $self->autoSizeMax);
        $self->multiple = (bool)($settings['multiple'] ?? $self->multiple);
        $self->MM = (string)($settings['MM'] ?? $self->MM);
        $self->MM_opposite_field = (string)($settings['MM_opposite_field'] ?? $self->MM_opposite_field);
        $self->MM_match_fields = (array)($settings['MM_match_fields'] ?? $self->MM_match_fields);
        $self->MM_oppositeUsage = (string)($settings['MM_oppositeUsage'] ?? $self->MM_oppositeUsage);
        $self->MM_table_where = (string)($settings['MM_table_where'] ?? $self->MM_table_where);
        $self->dontRemapTablesOnCopy = (string)($settings['dontRemapTablesOnCopy'] ?? $self->dontRemapTablesOnCopy);
        $self->localizeReferencesAtParentLocalization = (bool)($settings['localizeReferencesAtParentLocalization'] ?? $self->localizeReferencesAtParentLocalization);
        $self->hideMoveIcons = (bool)($settings['hideMoveIcons'] ?? $self->hideMoveIcons);
        $self->hideSuggest = (bool)($settings['hideSuggest'] ?? $self->hideSuggest);
        $self->prepend_tname = (bool)($settings['prepend_tname'] ?? $self->prepend_tname);
        $self->elementBrowserEntryPoints = (array)($settings['elementBrowserEntryPoints'] ?? $self->elementBrowserEntryPoints);
        $self->filter = (array)($settings['filter'] ?? $self->filter);
        $self->suggestOptions = (array)($settings['suggestOptions'] ?? $self->suggestOptions);
        $self->appearance = (array)($settings['appearance'] ?? $self->appearance);

        return $self;
    }

    public function getTca(): array
    {
        $tca = $this->toTca();
        $config['type'] = $this->fieldType->getTcaType();
        if ($this->default !== '') {
            $config['default'] = $this->default;
        }
        if ($this->allowed !== '') {
            $config['allowed'] = $this->allowed;
        }
        if ($this->foreign_table !== '') {
            $config['foreign_table'] = $this->foreign_table;
        }
        if ($this->readOnly) {
            $config['readOnly'] = true;
        }
        if ($this->size > 0) {
            $config['size'] = $this->size;
        }
        if ($this->maxitems > 0) {
            $config['maxitems'] = $this->maxitems;
        }
        if ($this->minitems > 0) {
            $config['minitems'] = $this->minitems;
        }
        if ($this->autoSizeMax > 0) {
            $config['autoSizeMax'] = $this->autoSizeMax;
        }
        if ($this->multiple) {
            $config['multiple'] = true;
        }
        if ($this->MM !== '') {
            $config['MM'] = $this->MM;
        }
        if ($this->MM_opposite_field !== '') {
            $config['MM_opposite_field'] = $this->MM_opposite_field;
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
        if ($this->hideMoveIcons) {
            $config['hideMoveIcons'] = true;
        }
        if ($this->hideSuggest) {
            $config['hideSuggest'] = true;
        }
        if ($this->prepend_tname) {
            $config['prepend_tname'] = true;
        }
        if ($this->elementBrowserEntryPoints !== []) {
            $config['elementBrowserEntryPoints'] = $this->elementBrowserEntryPoints;
        }
        if ($this->filter !== []) {
            $config['filter'] = $this->filter;
        }
        if ($this->suggestOptions !== []) {
            $config['suggestOptions'] = $this->suggestOptions;
        }
        if ($this->appearance !== []) {
            $config['appearance'] = $this->appearance;
        }
        $tca['config'] = array_replace($tca['config'] ?? [], $config);
        return $tca;
    }

    public function getSql(string $uniqueColumnName): string
    {
        return "`$uniqueColumnName` text";
    }

    public function getFieldType(): FieldType
    {
        return $this->fieldType;
    }
}
