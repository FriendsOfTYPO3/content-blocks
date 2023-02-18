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
use TYPO3\CMS\Core\Resource\AbstractFile;

final class ExistingFieldConfiguration implements FieldConfigurationInterface
{
    private FieldType $fieldType = FieldType::EXISTING;
    private string $default = '';
    private bool $readOnly = false;
    private int $size = 0;
    private bool $required = false;
    private int $max = 0;
    private int $min = 0;
    private bool $nullable = false;
    private string $mode = '';
    private string $placeholder = '';
    private string $is_in = '';
    private array $valuePicker = [];
    private array $eval = [];
    private ?bool $autocomplete = null;
    private int $maxitems = 0;
    private int $minitems = 0;
    private string $exclusiveKeys = '';
    private array $treeConfig = [];
    private string $relationship = '';
    private bool $invertStateDisplay = false;
    private string $itemsProcFunc = '';
    private int|string $cols = 0;
    private array $validation = [];
    private array $items = [];
    private bool $localizeReferencesAtParentLocalization = false;
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
    private array $range = [];
    private bool $disableAgeDisplay = false;
    private string $format = '';
    private array|string $allowed = [];
    private array|string $disallowed = [];
    private bool $enableImageManipulation = true;
    private array $allowedTypes = [];
    private array $slider = [];
    private bool $multiple = false;
    private array $MM_insert_fields = [];
    private array $MM_match_fields = [];
    private string $MM_oppositeUsage = '';
    private string $MM_table_where = '';
    private string $dontRemapTablesOnCopy = '';
    private bool $hideMoveIcons = false;
    private bool $hideSuggest = false;
    private bool $prepend_tname = false;
    private array $elementBrowserEntryPoints = [];
    private array $suggestOptions = [];
    private string $renderType = '';
    private bool $allowNonIdValues = false;
    private string $authMode = '';
    private bool $disableNoMatchingValueElement = false;
    private array $fileFolderConfig = [];
    private string $foreign_table_prefix = '';
    private string $foreign_table_where = '';
    private array $itemGroups = [];
    private array $sortItems = [];
    private int $rows = 0;
    private bool $enableTabulator = false;
    private bool $fixedFont = false;
    private string $wrap = '';
    private bool $enableRichtext = false;
    private string $richtextConfiguration = '';


    public static function createFromArray(array $settings): ExistingFieldConfiguration
    {
        $self = new self();
        $properties = $settings['properties'] ?? [];
        $default = $properties['default'] ?? $self->default;
        if (is_string($default) || is_int($default)) {
            $self->default = $default;
        }
        $self->readOnly = (bool)($properties['readOnly'] ?? $self->readOnly);
        $self->size = (int)($properties['size'] ?? $self->size);
        $self->required = (bool)($properties['required'] ?? $self->required);
        $self->max = (int)($properties['max'] ?? $self->max);
        $self->min = (int)($properties['min'] ?? $self->min);
        $self->nullable = (bool)($properties['nullable'] ?? $self->nullable);
        $self->mode = (string)($properties['mode'] ?? $self->mode);
        $self->placeholder = (string)($properties['placeholder'] ?? $self->placeholder);
        $self->is_in = (string)($properties['is_in'] ?? $self->is_in);
        $self->eval = (array)($properties['eval'] ?? $self->eval);
        if (isset($properties['autocomplete'])) {
            $self->autocomplete = (bool)($properties['autocomplete'] ?? $self->autocomplete);
        }
        $self->valuePicker = (array)($properties['valuePicker'] ?? $self->valuePicker);
        $self->maxitems = (int)($properties['maxitems'] ?? $self->maxitems);
        $self->minitems = (int)($properties['minitems'] ?? $self->minitems);
        $self->exclusiveKeys = (string)($properties['exclusiveKeys'] ?? $self->exclusiveKeys);
        $self->treeConfig = (array)($properties['treeConfig'] ?? $self->treeConfig);
        $self->relationship = (string)($properties['relationship'] ?? $self->relationship);
        $self->invertStateDisplay = (bool)($properties['invertStateDisplay'] ?? $self->invertStateDisplay);
        $self->itemsProcFunc = (string)($properties['itemsProcFunc'] ?? $self->itemsProcFunc);
        $self->cols = (int)($properties['cols'] ?? $self->cols);
        $self->validation = (array)($properties['validation'] ?? $self->validation);
        $self->items = (array)($properties['items'] ?? $self->items);
        $self->localizeReferencesAtParentLocalization = (bool)($properties['localizeReferencesAtParentLocalization'] ?? $self->localizeReferencesAtParentLocalization);
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
        $self->range = (array)($properties['range'] ?? $self->range);
        $self->disableAgeDisplay = (bool)($properties['disableAgeDisplay'] ?? $self->disableAgeDisplay);
        $self->format = (string)($properties['format'] ?? $self->format);
        $allowed = $properties['allowed'] ?? $self->allowed;
        if (is_array($allowed) || is_string($allowed)) {
            $self->allowed = $allowed;
        }
        $disallowed = $properties['disallowed'] ?? $self->disallowed;
        if (is_array($disallowed) || is_string($disallowed)) {
            $self->disallowed = $disallowed;
        }
        $self->enableImageManipulation = (bool)($properties['enableImageManipulation'] ?? $self->enableImageManipulation);
        $self->allowedTypes = (array)($properties['allowedTypes'] ?? $self->allowedTypes);
        $self->slider = (array)($properties['slider'] ?? $self->slider);
        $self->multiple = (bool)($properties['multiple'] ?? $self->multiple);
        $self->MM_insert_fields = (array)($properties['MM_insert_fields'] ?? $self->MM_insert_fields);
        $self->MM_match_fields = (array)($properties['MM_match_fields'] ?? $self->MM_match_fields);
        $self->MM_oppositeUsage = (string)($properties['MM_oppositeUsage'] ?? $self->MM_oppositeUsage);
        $self->MM_table_where = (string)($properties['MM_table_where'] ?? $self->MM_table_where);
        $self->dontRemapTablesOnCopy = (string)($properties['dontRemapTablesOnCopy'] ?? $self->dontRemapTablesOnCopy);
        $self->hideMoveIcons = (bool)($properties['hideMoveIcons'] ?? $self->hideMoveIcons);
        $self->hideSuggest = (bool)($properties['hideSuggest'] ?? $self->hideSuggest);
        $self->prepend_tname = (bool)($properties['prepend_tname'] ?? $self->prepend_tname);
        $self->elementBrowserEntryPoints = (array)($properties['elementBrowserEntryPoints'] ?? $self->elementBrowserEntryPoints);
        $self->suggestOptions = (array)($properties['suggestOptions'] ?? $self->suggestOptions);
        $self->renderType = (string)($properties['renderType'] ?? $self->renderType);
        $self->allowNonIdValues = (bool)($properties['allowNonIdValues'] ?? $self->allowNonIdValues);
        $self->authMode = (string)($properties['authMode'] ?? $self->authMode);
        $self->disableNoMatchingValueElement = (bool)($properties['disableNoMatchingValueElement'] ?? $self->disableNoMatchingValueElement);
        $self->fileFolderConfig = (array)($properties['fileFolderConfig'] ?? $self->fileFolderConfig);
        $self->foreign_table_prefix = (string)($properties['foreign_table_prefix'] ?? $self->foreign_table_prefix);
        $self->foreign_table_where = (string)($properties['foreign_table_where'] ?? $self->foreign_table_where);
        $self->itemGroups = (array)($properties['itemGroups'] ?? $self->itemGroups);
        $self->sortItems = (array)($properties['sortItems'] ?? $self->sortItems);
        $self->rows = (int)($properties['rows'] ?? $self->rows);
        $self->enableTabulator = (bool)($properties['enableTabulator'] ?? $self->enableTabulator);
        $self->fixedFont = (bool)($properties['fixedFont'] ?? $self->fixedFont);
        $self->wrap = (string)($properties['wrap'] ?? $self->wrap);
        $self->enableRichtext = (bool)($properties['enableRichtext'] ?? $self->enableRichtext);
        $self->richtextConfiguration = (string)($properties['richtextConfiguration'] ?? $self->richtextConfiguration);

        return $self;
    }

    public function getTca(string $languagePath, bool $useExistingField): array
    {
        $tca['label'] = $languagePath . '.label';
        $tca['description'] = $languagePath . '.description';
        // todo: we don't need the type of the field here because we use the type form the existing one
        // $config['type'] = $this->fieldType->getTcaType();
        if ($this->size !== 0) {
            $config['size'] = $this->size;
        }
        if ($this->default !== '') {
            $config['default'] = $this->default;
            // @todo: handling of Time/Datetime and timestampConvert()
        }
        if ($this->readOnly) {
            $config['readOnly'] = true;
        }
        if ($this->max > 0) {
            $config['max'] = $this->max;
        }
        if ($this->min > 0) {
            $config['min'] = $this->min;
        }
        if ($this->nullable) {
            $config['nullable'] = true;
        }
        if ($this->mode !== '') {
            $config['mode'] = $this->mode;
        }
        if ($this->placeholder !== '') {
            $config['placeholder'] = $this->placeholder;
        }
        if ($this->is_in !== '') {
            $config['is_in'] = $this->is_in;
        }
        if ($this->required) {
            $config['required'] = true;
        }
        if ($this->eval !== []) {
            $config['eval'] = implode(',', $this->eval);
        }
        if (isset($this->autocomplete)) {
            $config['autocomplete'] = $this->autocomplete;
        }
        if (($this->valuePicker['items'] ?? []) !== []) {
            $config['valuePicker'] = $this->valuePicker;
        }
        if ($this->localizeReferencesAtParentLocalization) {
            $config['localizeReferencesAtParentLocalization'] = true;
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
        if ($this->range !== []) {
            $config['range'] = $this->range;
            // @todo: handling of Time/Datetime and timestampConvert()
        }
        if ($this->disableAgeDisplay) {
            $config['disableAgeDisplay'] = true;
        }
        if ($this->format !== '') {
            $config['format'] = $this->format;
        }
        if ($this->allowed !== [] && $this->allowed !== '') {
            $config['allowed'] = $this->allowed;
        }
        if ($this->disallowed !== [] && $this->disallowed !== '') {
            $config['disallowed'] = $this->disallowed;
        }
        if ($this->enableImageManipulation) {
            $config['overrideChildTca'] = [
                'types' => [
                    '0' => [
                        'showitem' => '--palette--;;imageoverlayPalette,--palette--;;filePalette',
                    ],
                    AbstractFile::FILETYPE_TEXT => [
                        'showitem' => '--palette--;;imageoverlayPalette,--palette--;;filePalette',
                    ],
                    AbstractFile::FILETYPE_IMAGE => [
                        'showitem' => '--palette--;;imageoverlayPalette,--palette--;;filePalette',
                    ],
                    AbstractFile::FILETYPE_AUDIO => [
                        'showitem' => '--palette--;;audioOverlayPalette,--palette--;;filePalette',
                    ],
                    AbstractFile::FILETYPE_VIDEO => [
                        'showitem' => '--palette--;;videoOverlayPalette,--palette--;;filePalette',
                    ],
                    AbstractFile::FILETYPE_APPLICATION => [
                        'showitem' => '--palette--;;imageoverlayPalette,--palette--;;filePalette',
                    ],
                ],
            ];
        }
        if ($this->allowedTypes !== []) {
            $config['allowedTypes'] = $this->allowedTypes;
        }
        if ($this->slider !== []) {
            $config['slider'] = $this->slider;
        }
        if ($this->multiple) {
            $config['multiple'] = true;
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
        if ($this->suggestOptions !== []) {
            $config['suggestOptions'] = $this->suggestOptions;
        }
        if ($this->renderType !== '') {
            $config['renderType'] = $this->renderType;
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
        if ($this->sortItems !== []) {
            $config['sortItems'] = $this->sortItems;
        }
        if ($this->rows !== 0) {
            $config['rows'] = $this->rows;
        }
        if ($this->enableTabulator) {
            $config['enableTabulator'] = true;
        }
        if ($this->fixedFont) {
            $config['fixedFont'] = true;
        }
        if ($this->wrap !== '') {
            $config['wrap'] = $this->wrap;
        }
        if ($this->enableRichtext) {
            $config['enableRichtext'] = true;
        }
        if ($this->richtextConfiguration !== '') {
            $config['richtextConfiguration'] = $this->richtextConfiguration;
        }
        $tca['config'] = $config;

        // @todo: move the tca config to overrideChildTca:
        // This should be done outside of FieldConfiguration, as we need the CType to do this.

        return $tca;
    }

    /**
     * Existing field: no need to add a new column to the database.
     */
    public function getSql(string $uniqueColumnName): string
    {
        return "";
    }

    public function getFieldType(): FieldType
    {
        return $this->fieldType;
    }
}
