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

use TYPO3\CMS\ContentBlocks\Definition\Capability\LabelCapability;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;
use TYPO3\CMS\ContentBlocks\Definition\Factory\Processing\ProcessedFieldsResult;
use TYPO3\CMS\ContentBlocks\Definition\Factory\Processing\ProcessingInput;
use TYPO3\CMS\ContentBlocks\Definition\FlexForm\ContainerDefinition;
use TYPO3\CMS\ContentBlocks\Definition\FlexForm\FlexFormDefinition;
use TYPO3\CMS\ContentBlocks\Definition\FlexForm\SectionDefinition;
use TYPO3\CMS\ContentBlocks\Definition\FlexForm\SheetDefinition;
use TYPO3\CMS\ContentBlocks\Definition\PaletteDefinition;
use TYPO3\CMS\ContentBlocks\Definition\TCA\LinebreakDefinition;
use TYPO3\CMS\ContentBlocks\Definition\TCA\TabDefinition;
use TYPO3\CMS\ContentBlocks\Definition\TcaFieldDefinition;
use TYPO3\CMS\ContentBlocks\FieldConfiguration\FieldType;
use TYPO3\CMS\ContentBlocks\Loader\LoadedContentBlock;
use TYPO3\CMS\ContentBlocks\Registry\AutomaticLanguageKeysRegistry;
use TYPO3\CMS\ContentBlocks\Registry\AutomaticLanguageSource;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;

/**
 * This class does the main heavy-lifting of parsing and preparing loaded
 * Content Blocks and builds the hierarchy of table definitions, TCA
 * record types (here called Content Types) and other TCA specialties like
 * Palettes, Tabs or FlexForm. The result `CompilationResult` is used for
 * ContentBlockDefinitionCollectionFactory->create(). The resulting
 * immutable object is used as an interchange format for multiple generators
 * as read-only access. As such, this class does not have knowledge about real
 * TCA, only about the YAML definition and how to transform it into the
 * internal object format.
 *
 * List of what this compiler does:
 * - Builds the CompilationResult object for use in ContentBlockDefinitionCollectionFactory->create().
 * - Validates the YAML-schema and errors out in case of incorrect definition.
 * - Assigns and tracks unique identifiers for use in database column names.
 * - Collects automatic language keys, which can be used to generate the
 *   Labels.xlf file with a command.
 * - Recursively resolves Collections and creates additional anonymous Record
 *   Types based on them. Meaning one Content Block can indeed define more than
 *   one Content Type with the use of Collections.
 * - Tracks parent references which are used to automatically add `parent_uid`
 *   fields. This way a Record Type can be an aggregate root or not depending on
 *   whether it is used in a foreign_table relation or not.
 * - Replaces identifiers referenced in configuration options with the prefixed
 *   identifier (e.g. for the labelField).
 * - Dynamically adds new fields depending on config. For example the type field
 *   or the description column.
 * - (YAML-defined) FlexForm parsing / preparation.
 *
 * @internal Not part of TYPO3's public API.
 */
final class ContentBlockCompiler
{
    private AutomaticLanguageKeysRegistry $automaticLanguageKeysRegistry;

    /**
     * This property tracks Collection foreign_table parent references.
     * It is needed, because there can be a references to a Content Block,
     * which isn't processed yet. Thus, it needs to be collected throughout the run.
     *
     * @var array<string, array>
     */
    private array $parentReferences = [];

    public function compile(ContentBlockRegistry $contentBlockRegistry): CompilationResult
    {
        $this->automaticLanguageKeysRegistry = new AutomaticLanguageKeysRegistry();
        $tableDefinitionList = [];
        foreach ($contentBlockRegistry->getAll() as $contentBlock) {
            $table = $contentBlock->getYaml()['table'];
            $languagePath = $this->buildBaseLanguagePath($contentBlock);
            $processingInput = new ProcessingInput(
                yaml: $contentBlock->getYaml(),
                contentBlock: $contentBlock,
                table: $table,
                rootTable: $table,
                languagePath: $languagePath,
                contentType: $contentBlock->getContentType(),
                tableDefinitionList: $tableDefinitionList,
            );
            $tableDefinitionList = $this->processRootFields($processingInput);
        }
        $mergedTableDefinitionList = $this->mergeProcessingResult($tableDefinitionList);
        $compilationResult = new CompilationResult(
            $this->automaticLanguageKeysRegistry,
            $this->parentReferences,
            $mergedTableDefinitionList,
        );
        $this->resetState();
        return $compilationResult;
    }

    private function resetState(): void
    {
        $this->parentReferences = [];
        unset($this->automaticLanguageKeysRegistry);
    }

    private function mergeProcessingResult(array $tableDefinitionList): array
    {
        $mergedResult = [];
        foreach ($tableDefinitionList as $table => $definition) {
            $mergedResult[$table] = array_replace_recursive(...array_reverse($definition['tableDefinitions']));
            $mergedResult[$table]['typeDefinitions'] = $definition['typeDefinitions'];
        }
        return $mergedResult;
    }

    private function processRootFields(ProcessingInput $input): array
    {
        $result = new ProcessedFieldsResult($input);
        $this->initializeContentTypeLabelAndDescription($input, $result);
        $yaml = $input->yaml;
        $yaml = $this->prepareYaml($result, $yaml);
        $yamlFields = $yaml['fields'] ?? [];
        foreach ($yamlFields as $rootField) {
            $fields = $this->handleRootField($rootField, $input, $result);
            $this->processFields($input, $result, $fields);
        }
        $this->prefixTcaConfigFields($input, $result);
        $this->collectOverrideColumns($result);
        $this->collectDefinitions($input, $result);
        return $result->tableDefinitionList;
    }

    private function processFields(ProcessingInput $input, ProcessedFieldsResult $result, array $fields): void
    {
        foreach ($fields as $field) {
            $this->initializeField($input, $result, $field);
            $input->languagePath->addPathSegment($result->identifier);
            $field = $this->initializeFieldLabelAndDescription($input, $result, $field);
            if ($result->fieldType === FieldType::FLEXFORM) {
                $field = $this->processFlexForm($input, $field);
            }
            if ($result->fieldType->hasItems()) {
                $field = $this->collectItemLabels($input, $result, $field);
            }
            $result->tcaFieldDefinition = $this->buildTcaFieldDefinitionArray($input, $result, $field);
            if ($result->fieldType === FieldType::COLLECTION) {
                $this->processCollection($input, $result, $field);
            }
            $this->collectProcessedField($result);
            $input->languagePath->popSegment();
            $result->resetTemporaryState();
        }
    }

    private function initializeField(ProcessingInput $input, ProcessedFieldsResult $result, array $field): void
    {
        $result->identifier = (string)$field['identifier'];
        $this->assertUniqueFieldIdentifier($result, $input->contentBlock);
        $result->uniqueFieldIdentifiers[] = $result->identifier;
        $result->fieldType = $this->resolveType($input, $field);
        $result->uniqueIdentifier = $this->chooseIdentifier($input, $field);
        $result->identifierToUniqueMap[$result->identifier] = $result->uniqueIdentifier;
    }

    private function prepareYaml(ProcessedFieldsResult $result, array $yaml): array
    {
        $contentType = $result->tableDefinition->contentType;
        if ($contentType === ContentType::RECORD_TYPE && $result->tableDefinition->hasTypeField()) {
            $yamlFields = $yaml['fields'] ?? [];
            $yamlFields = $this->prependTypeFieldForRecordType($yamlFields, $result);
            $yaml['fields'] = $yamlFields;
        }
        $hasInternalDescription = $yaml['internalDescription'] ?? false;
        if ($contentType === ContentType::RECORD_TYPE && $hasInternalDescription) {
            $yamlFields = $yaml['fields'] ?? [];
            $yamlFields = $this->appendInternalDescription($yamlFields);
            $yaml['fields'] = $yamlFields;
        }
        if ($contentType === ContentType::PAGE_TYPE) {
            $yamlFields = $yaml['fields'] ?? [];
            $yamlFields = $this->prependPagesTitlePalette($yamlFields);
            $yaml['fields'] = $yamlFields;
        }
        return $yaml;
    }

    private function initializeContentTypeLabelAndDescription(ProcessingInput $input, ProcessedFieldsResult $result): void
    {
        $languagePathTitle = $input->languagePath->getCurrentPath();
        $languagePathDescription = $input->languagePath->getCurrentPath();
        $title = (string)($input->yaml['title'] ?? '');
        $description = (string)($input->yaml['description'] ?? '');
        if ($input->isRootTable()) {
            // Ensure there is always a title for a Content Type.
            $title = $title !== '' ? $title : $input->contentBlock->getName();
            $result->contentType->title = $title;
            $result->contentType->description = $description;
            $languagePathTitle = $languagePathTitle . 'title';
            $languagePathDescription = $languagePathDescription . 'description';
            $languagePathSource = new AutomaticLanguageSource($languagePathTitle, $title);
            $descriptionPathSource = new AutomaticLanguageSource($languagePathDescription, $description);
            $this->automaticLanguageKeysRegistry->addKey($input->contentBlock, $languagePathSource);
            $this->automaticLanguageKeysRegistry->addKey($input->contentBlock, $descriptionPathSource);
        } else {
            $languagePathTitle = $languagePathTitle . '.label';
            $languagePathDescription = $languagePathDescription . '.description';
            $result->contentType->title = $title;
            $result->contentType->description = $description;
        }
        $result->contentType->languagePathTitle = $languagePathTitle;
        $result->contentType->languagePathDescription = $languagePathDescription;
    }

    private function initializeFieldLabelAndDescription(ProcessingInput $input, ProcessedFieldsResult $result, array $field): array
    {
        $labelPath = $this->getFieldLabelPath($input->languagePath);
        $descriptionPath = $this->getFieldDescriptionPath($input->languagePath);
        $title = (string)($field['label'] ?? '');
        // Never fall back to identifiers for existing fields. They have their standard translation.
        $title = ($title !== '' || $this->isExistingField($field)) ? $title : $result->identifier;
        $field['label'] = $title;
        $description = $field['description'] ?? '';
        $labelPathSource = new AutomaticLanguageSource($labelPath, $title);
        $descriptionPathSource = new AutomaticLanguageSource($descriptionPath, $description);
        $this->automaticLanguageKeysRegistry->addKey($input->contentBlock, $labelPathSource);
        $this->automaticLanguageKeysRegistry->addKey($input->contentBlock, $descriptionPathSource);
        return $field;
    }

    private function collectItemLabels(ProcessingInput $input, ProcessedFieldsResult $result, array $field): array
    {
        $items = $field['items'] ?? [];
        $fieldType = $result->fieldType;
        foreach ($items as $index => $item) {
            $label = (string)($item['label'] ?? '');
            $currentPath = $input->languagePath->getCurrentPath();
            if ($fieldType === FieldType::CHECKBOX) {
                $labelPath = $currentPath . '.items.' . $index . '.label';
            } else {
                $value = (string)($item['value'] ?? '');
                if ($value === '') {
                    $labelPath = $currentPath . '.items.label';
                } else {
                    $labelPath = $currentPath . '.items.' . $value . '.label';
                }
            }
            $field['items'][$index]['labelPath'] = $labelPath;
            $labelPathSource = new AutomaticLanguageSource($labelPath, $label);
            $this->automaticLanguageKeysRegistry->addKey($input->contentBlock, $labelPathSource);
        }
        return $field;
    }

    private function buildTcaFieldDefinitionArray(
        ProcessingInput $input,
        ProcessedFieldsResult $result,
        array $field,
    ): array {
        $tcaFieldDefinition = [
            'parentTable' => $input->contentBlock->getContentType()->getTable(),
            'uniqueIdentifier' => $result->uniqueIdentifier,
            'config' => $field,
            'type' => $result->fieldType,
            'labelPath' => $this->getFieldLabelPath($input->languagePath),
            'descriptionPath' => $this->getFieldDescriptionPath($input->languagePath),
        ];
        return $tcaFieldDefinition;
    }

    private function getFieldLabelPath(LanguagePath $languagePath): string
    {
        return $languagePath->getCurrentPath() . '.label';
    }

    private function getFieldDescriptionPath(LanguagePath $languagePath): string
    {
        return $languagePath->getCurrentPath() . '.description';
    }

    private function prefixTcaConfigFields(ProcessingInput $input, ProcessedFieldsResult $result): void
    {
        $this->prefixSortFieldIfNecessary($input, $result);
        $this->prefixLabelFieldIfNecessary($input, $result);
        $this->prefixFallbackLabelFieldsIfNecessary($input, $result);
        $this->prefixDisplayCondFieldsIfNecessary($result);
    }

    private function prependTypeFieldForRecordType(array $yamlFields, ProcessedFieldsResult $result): array
    {
        $typeFieldDefinition = [
            'identifier' => $result->tableDefinition->typeField,
            'type' => FieldType::SELECT->value,
            'renderType' => 'selectSingle',
            'prefixField' => false,
            'default' => $result->contentType->typeName,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.type',
            'items' => [],
        ];
        // Prepend type field.
        array_unshift($yamlFields, $typeFieldDefinition);
        return $yamlFields;
    }

    private function appendInternalDescription(array $yamlFields): array
    {
        $tab = [
            'identifier' => 'internal_description_tab',
            'type' => 'Tab',
            'label' => 'LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes',
        ];
        $internalDescription = [
            'identifier' => 'internal_description',
            'type' => 'Textarea',
            'prefixField' => false,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.description',
            'rows' => 5,
            'cols' => 30,
        ];
        $yamlFields[] = $tab;
        $yamlFields[] = $internalDescription;
        return $yamlFields;
    }

    private function prependPagesTitlePalette(array $yamlFields): array
    {
        $titlePalette = [
            'identifier' => 'content_blocks_titleonly',
            'type' => 'Palette',
            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.title',
            'prefixField' => false,
            'fields' => [
                [
                    'identifier' => 'title',
                    'useExistingField' => true,
                    'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.title_formlabel',
                ],
                [
                    'type' => 'Linebreak',
                ],
                [
                    'identifier' => 'slug',
                    'useExistingField' => true,
                ],
                [
                    'type' => 'Linebreak',
                ],
                [
                    'identifier' => 'nav_title',
                    'useExistingField' => true,
                    'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.nav_title_formlabel',
                ],
            ],
        ];
        array_unshift($yamlFields, $titlePalette);
        return $yamlFields;
    }

    private function handleRootField(array $rootField, ProcessingInput $input, ProcessedFieldsResult $result): array
    {
        $rootFieldType = $this->resolveType($input, $rootField);
        $this->assertNoLinebreakOutsideOfPalette($rootFieldType, $input->contentBlock);
        $fields = match ($rootFieldType) {
            Fieldtype::PALETTE => $this->handlePalette($input, $result, $rootField),
            FieldType::TAB => $this->handleTab($input, $result, $rootField),
            default => $this->handleDefault($input, $result, $rootField)
        };
        return $fields;
    }

    private function handleDefault(ProcessingInput $input, ProcessedFieldsResult $result, array $field): array
    {
        $result->contentType->showItems[] = $this->chooseIdentifier($input, $field);
        return [$field];
    }

    private function handlePalette(ProcessingInput $input, ProcessedFieldsResult $result, array $rootPalette): array
    {
        $rootPaletteIdentifier = $rootPalette['identifier'];
        $this->assertUniquePaletteIdentifier($rootPaletteIdentifier, $result, $input->contentBlock);
        $result->uniquePaletteIdentifiers[] = $rootPaletteIdentifier;
        // Ignore empty Palettes.
        if (($rootPalette['fields'] ?? []) === []) {
            return [];
        }
        $fields = [];
        $paletteItems = [];
        foreach ($rootPalette['fields'] as $paletteField) {
            $paletteFieldType = $this->resolveType($input, $paletteField);
            if ($paletteFieldType === FieldType::LINEBREAK) {
                $paletteItems[] = new LinebreakDefinition();
            } else {
                $this->assertNoPaletteInPalette($paletteFieldType, $paletteField['identifier'], $rootPaletteIdentifier, $input->contentBlock);
                $this->assertNoTabInPalette($paletteFieldType, $paletteField['identifier'], $rootPaletteIdentifier, $input->contentBlock);
                $fields[] = $paletteField;
                $paletteItems[] = $this->chooseIdentifier($input, $paletteField);
            }
        }

        $input->languagePath->addPathSegment('palettes.' . $rootPaletteIdentifier);
        $label = (string)($rootPalette['label'] ?? '');
        $description = $rootPalette['description'] ?? '';
        $languagePathLabel = $input->languagePath->getCurrentPath() . '.label';
        $languagePathDescription = $input->languagePath->getCurrentPath() . '.description';
        $labelPathSource = new AutomaticLanguageSource($languagePathLabel, $label);
        $descriptionPathSource = new AutomaticLanguageSource($languagePathDescription, $description);
        $this->automaticLanguageKeysRegistry->addKey($input->contentBlock, $labelPathSource);
        $this->automaticLanguageKeysRegistry->addKey($input->contentBlock, $descriptionPathSource);
        $input->languagePath->popSegment();

        $paletteIdentifier = $this->chooseIdentifier($input, $rootPalette);
        $palette = [
            'contentBlockName' => $input->contentBlock->getName(),
            'identifier' => $paletteIdentifier,
            'label' => $label,
            'description' => $description,
            'languagePathLabel' => $languagePathLabel,
            'languagePathDescription' => $languagePathDescription,
            'items' => $paletteItems,
        ];
        $result->tableDefinition->palettes[$paletteIdentifier] = $palette;
        $result->contentType->showItems[] = PaletteDefinition::createFromArray($palette);
        return $fields;
    }

    private function handleTab(ProcessingInput $input, ProcessedFieldsResult $result, array $field): array
    {
        $identifier = $field['identifier'];
        $this->assertUniqueTabIdentifier($identifier, $result, $input->contentBlock);
        $result->uniqueTabIdentifiers[] = $identifier;

        $input->languagePath->addPathSegment('tabs.' . $identifier);
        $label = (string)($field['label'] ?? '');
        $label = $label !== '' ? $label : $identifier;
        $languagePathLabel = $input->languagePath->getCurrentPath();
        $languagePathSource = new AutomaticLanguageSource($languagePathLabel, $label);
        $this->automaticLanguageKeysRegistry->addKey($input->contentBlock, $languagePathSource);
        $input->languagePath->popSegment();

        $tabDefinitionArray = [
            'identifier' => $identifier,
            'contentBlockName' => $input->contentBlock->getName(),
            'label' => $label,
            'languagePathLabel' => $languagePathLabel,
        ];
        $tabDefinition = TabDefinition::createFromArray($tabDefinitionArray);
        $result->contentType->showItems[] = $tabDefinition;
        return [];
    }

    private function collectProcessedField(ProcessedFieldsResult $result): void
    {
        $result->tableDefinition->fields[$result->uniqueIdentifier] = $result->tcaFieldDefinition;
        $result->contentType->columns[] = $result->uniqueIdentifier;
    }

    private function collectOverrideColumns(ProcessedFieldsResult $result): void
    {
        foreach ($result->tableDefinition->fields as $uniqueIdentifier => $tcaFieldDefinition) {
            $isTypeField = $uniqueIdentifier === $result->tableDefinition->typeField;
            if (!$isTypeField) {
                $overrideColumn = TcaFieldDefinition::createFromArray($tcaFieldDefinition);
                $result->contentType->overrideColumns[] = $overrideColumn;
            }
        }
    }

    /**
     * Collect table definitions and Content Types and carry them over to the next stack.
     * This factory will merge the table definitions and type definitions at the very end.
     */
    private function collectDefinitions(ProcessingInput $input, ProcessedFieldsResult $result): void
    {
        $table = $input->table;
        $tableDefinition = $result->tableDefinition->toArray();
        $result->tableDefinitionList[$table]['tableDefinitions'][] = $tableDefinition;
        $isRootTable = $input->isRootTable();
        $identifier = $input->yaml['identifier'] ?? '';
        $typeDefinition = $result->contentType->toArray($isRootTable, $identifier);
        $result->tableDefinitionList[$table]['typeDefinitions'][] = $typeDefinition;
    }

    private function processCollection(ProcessingInput $input, ProcessedFieldsResult $result, array $field): void
    {
        $isExternalCollection = array_key_exists('foreign_table', $field);
        $this->assignRelationConfigToCollectionField($field, $result);
        $foreignTable = $result->tcaFieldDefinition['config']['foreign_table'];
        $this->parentReferences[$foreignTable][] = $result->tcaFieldDefinition;
        $fields = $field['fields'] ?? [];
        if ($isExternalCollection || $fields === []) {
            return;
        }
        $field['title'] = $field['label'];
        $newInput = new ProcessingInput(
            yaml: $field,
            contentBlock: $input->contentBlock,
            table: $foreignTable,
            rootTable: $input->rootTable,
            languagePath: $input->languagePath,
            contentType: ContentType::RECORD_TYPE,
            tableDefinitionList: $result->tableDefinitionList,
        );
        $result->tableDefinitionList = $this->processRootFields($newInput);
    }

    private function assignRelationConfigToCollectionField(array $field, ProcessedFieldsResult $result): void
    {
        $isExternalCollection = array_key_exists('foreign_table', $field);
        $result->tcaFieldDefinition['config']['foreign_field'] ??= 'foreign_table_parent_uid';
        if ($isExternalCollection) {
            if ($field['shareAcrossTables'] ?? false) {
                $result->tcaFieldDefinition['config']['foreign_table_field'] ??= 'tablenames';
            }
            if ($field['shareAcrossFields'] ?? false) {
                $result->tcaFieldDefinition['config']['foreign_match_fields']['fieldname'] = $result->uniqueIdentifier;
            }
        } else {
            $result->tcaFieldDefinition['config']['foreign_table'] = $field['table'] ?? $result->uniqueIdentifier;
        }
    }

    private function processFlexForm(ProcessingInput $input, array $field): array
    {
        $this->validateFlexFormHasOnlySheetsOrNoSheet($field, $input->contentBlock);
        $this->validateFlexFormContainsValidFieldTypes($field, $input->contentBlock);
        $flexFormDefinition = new FlexFormDefinition();
        $flexFormTypeName = $input->getTypeField() !== null ? $input->getTypeName() : 'default';
        $flexFormDefinition->setTypeName($flexFormTypeName);
        $flexFormDefinition->setContentBlockName($input->contentBlock->getName());
        $sheetDefinition = new SheetDefinition();
        $fields = $field['fields'] ?? [];
        if ($this->flexFormDefinitionHasDefaultSheet($fields)) {
            foreach ($fields as $flexFormField) {
                $sheetDefinition->addFieldOrSection($this->resolveFlexFormField($input, $flexFormField));
            }
            $flexFormDefinition->addSheet($sheetDefinition);
        } else {
            foreach ($fields as $sheet) {
                $sheetDefinition = new SheetDefinition();
                $identifier = $sheet['identifier'];
                $sheetDefinition->setIdentifier($identifier);

                $input->languagePath->addPathSegment('sheets.' . $sheetDefinition->getIdentifier());
                $languagePathLabel = $input->languagePath->getCurrentPath() . '.label';
                $descriptionPathLabel = $input->languagePath->getCurrentPath() . '.description';
                $linkTitlePathLabel = $input->languagePath->getCurrentPath() . '.linkTitle';
                $sheetDefinition->setLanguagePathLabel($languagePathLabel);
                $sheetDefinition->setLanguagePathDescription($descriptionPathLabel);
                $sheetDefinition->setLanguagePathLinkTitle($linkTitlePathLabel);
                $label = (string)($sheet['label'] ?? '');
                $label = $label !== '' ? $label : $identifier;
                $description = $sheet['description'] ?? '';
                $linkTitle = $sheet['linkTitle'] ?? '';
                $sheetDefinition->setLabel($label);
                $sheetDefinition->setDescription($description);
                $sheetDefinition->setLinkTitle($linkTitle);
                $languagePathSource = new AutomaticLanguageSource($languagePathLabel, $label);
                $descriptionPathSource = new AutomaticLanguageSource($descriptionPathLabel, $description);
                $linkTitlePathSource = new AutomaticLanguageSource($linkTitlePathLabel, $linkTitle);
                $this->automaticLanguageKeysRegistry->addKey($input->contentBlock, $languagePathSource);
                $this->automaticLanguageKeysRegistry->addKey($input->contentBlock, $descriptionPathSource);
                $this->automaticLanguageKeysRegistry->addKey($input->contentBlock, $linkTitlePathSource);
                $input->languagePath->popSegment();

                foreach ($sheet['fields'] ?? [] as $sheetField) {
                    $sheetDefinition->addFieldOrSection($this->resolveFlexFormField($input, $sheetField));
                }
                $flexFormDefinition->addSheet($sheetDefinition);
            }
        }

        if ($input->getTypeField() !== null) {
            $field['ds_pointerField'] = $input->getTypeField();
        }
        $field['flexFormDefinitions'][$flexFormDefinition->getTypeName()] = $flexFormDefinition;
        return $field;
    }

    private function flexFormDefinitionHasDefaultSheet(array $fields): bool
    {
        foreach ($fields as $flexFormField) {
            return FlexFormSubType::tryFrom($flexFormField['type']) !== FlexFormSubType::SHEET;
        }
        return true;
    }

    private function resolveFlexFormField(ProcessingInput $input, array $flexFormField): TcaFieldDefinition|SectionDefinition
    {
        if (FlexFormSubType::tryFrom($flexFormField['type']) === FlexFormSubType::SECTION) {
            return $this->processFlexFormSection($input, $flexFormField);
        }
        $identifier = $flexFormField['identifier'];

        $input->languagePath->addPathSegment($identifier);
        $labelPath = $input->languagePath->getCurrentPath() . '.label';
        $descriptionPath = $input->languagePath->getCurrentPath() . '.description';
        $label = (string)($flexFormField['label'] ?? '');
        $label = $label !== '' ? $label : $identifier;
        $flexFormField['label'] = $label;
        $description = $flexFormField['description'] ?? '';
        $languagePathSource = new AutomaticLanguageSource($labelPath, $label);
        $descriptionPathSource = new AutomaticLanguageSource($descriptionPath, $description);
        $this->automaticLanguageKeysRegistry->addKey($input->contentBlock, $languagePathSource);
        $this->automaticLanguageKeysRegistry->addKey($input->contentBlock, $descriptionPathSource);

        $flexFormFieldArray = [
            'uniqueIdentifier' => $identifier,
            'config' => $flexFormField,
            'type' => FieldType::from($flexFormField['type']),
            'labelPath' => $labelPath,
            'descriptionPath' => $descriptionPath,
        ];
        $input->languagePath->popSegment();
        $flexFormTcaDefinition = TcaFieldDefinition::createFromArray($flexFormFieldArray);
        return $flexFormTcaDefinition;
    }

    private function processFlexFormSection(ProcessingInput $input, array $section): SectionDefinition
    {
        $sectionDefinition = new SectionDefinition();
        $sectionIdentifier = $section['identifier'];
        $sectionDefinition->setIdentifier($sectionIdentifier);

        $input->languagePath->addPathSegment('sections.' . $sectionDefinition->getIdentifier());
        $sectionTitle = $input->languagePath->getCurrentPath() . '.title';
        $label = (string)($section['label'] ?? '');
        $label = $label !== '' ? $label : $sectionIdentifier;
        $sectionDefinition->setLanguagePathLabel($sectionTitle);
        $sectionDefinition->setLabel($label);
        $labelPathSource = new AutomaticLanguageSource($sectionTitle, $label);
        $this->automaticLanguageKeysRegistry->addKey($input->contentBlock, $labelPathSource);

        foreach ($section['container'] as $container) {
            $containerIdentifier = $container['identifier'];
            $containerDefinition = new ContainerDefinition();
            $containerDefinition->setIdentifier($containerIdentifier);

            $input->languagePath->addPathSegment('container.' . $containerDefinition->getIdentifier());
            $containerTitle = $input->languagePath->getCurrentPath() . '.title';
            $label = (string)($container['label'] ?? '');
            $label = $label !== '' ? $label : $containerIdentifier;
            $containerDefinition->setLanguagePathLabel($containerTitle);
            $containerDefinition->setLabel($label);
            $labelPathSource = new AutomaticLanguageSource($containerTitle, $label);
            $this->automaticLanguageKeysRegistry->addKey($input->contentBlock, $labelPathSource);

            foreach ($container['fields'] as $containerField) {
                $containerDefinition->addField($this->resolveFlexFormField($input, $containerField));
            }
            $sectionDefinition->addContainer($containerDefinition);
            $input->languagePath->popSegment();
        }
        $input->languagePath->popSegment();
        return $sectionDefinition;
    }

    private function prefixSortFieldIfNecessary(ProcessingInput $input, ProcessedFieldsResult $result): void
    {
        $sortField = $input->yaml['sortField'] ?? null;
        if ($sortField === null) {
            return;
        }
        $sortFieldArray = [];
        if (is_string($sortField)) {
            $sortFieldArray = [['identifier' => $sortField]];
            $result->tableDefinition->raw['sortField'] = [];
        }
        if (is_array($sortField)) {
            $sortFieldArray = $sortField;
        }
        if ($sortFieldArray === []) {
            return;
        }
        for ($i = 0; $i < count($sortFieldArray); $i++) {
            $sortFieldItem = $sortFieldArray[$i];
            $sortFieldIdentifier = $sortFieldItem['identifier'];
            $order = '';
            if (array_key_exists('order', $sortFieldItem)) {
                $order = strtolower((string)$sortFieldItem['order']);
                if (!in_array($order, ['asc', 'desc'], true)) {
                    throw new \InvalidArgumentException(
                        'order for sortField.order must be one of "asc" or "desc". "' . $order . '" provided.',
                        1694014891
                    );
                }
            }
            if ($sortFieldIdentifier !== '' && in_array($sortFieldIdentifier, $result->uniqueFieldIdentifiers, true)) {
                $result->tableDefinition->raw['sortField'][$i]['identifier'] = $result->identifierToUniqueMap[$sortFieldIdentifier];
                $result->tableDefinition->raw['sortField'][$i]['order'] = strtoupper($order);
            }
        }
    }

    private function prefixDisplayCondFieldsIfNecessary(ProcessedFieldsResult $result): void
    {
        foreach ($result->tableDefinition->fields as $currentIdentifier => $tcaFieldDefinition) {
            $field = $tcaFieldDefinition['config'];
            $displayCond = $field['displayCond'] ?? null;
            if ($displayCond === null) {
                continue;
            }
            $isCorrectType = is_string($displayCond) || is_array($displayCond);
            $isEmpty = $displayCond === [] || $displayCond === '';
            if (!$isCorrectType || $isEmpty) {
                continue;
            }
            $field['displayCond'] = $this->prefixDisplayCondFieldRecursive($displayCond, $tcaFieldDefinition, $result);
            $tcaFieldDefinition['config'] = $field;
            $result->tableDefinition->fields[$currentIdentifier] = $tcaFieldDefinition;
        }
    }

    private function prefixDisplayCondFieldRecursive(string|array $displayCond, array $field, ProcessedFieldsResult $result): string|array
    {
        if (is_string($displayCond)) {
            $displayCond = $this->prefixDisplayCondRule($displayCond, $result);
        } else {
            foreach ($displayCond as $indexOrOperator => $ruleOrGroup) {
                $displayCond[$indexOrOperator] = $this->prefixDisplayCondFieldRecursive($ruleOrGroup, $field, $result);
            }
        }
        return $displayCond;
    }

    private function prefixDisplayCondRule(string $displayCond, ProcessedFieldsResult $result): string
    {
        if (!str_starts_with($displayCond, 'FIELD:')) {
            return $displayCond;
        }
        $parts = explode(':', $displayCond);
        $identifier = $parts[1];
        if (!in_array($identifier, $result->uniqueFieldIdentifiers, true)) {
            return $displayCond;
        }
        $parts[1] = $result->identifierToUniqueMap[$identifier];
        $replacedDisplayCond = implode(':', $parts);
        return $replacedDisplayCond;
    }

    private function prefixLabelFieldIfNecessary(ProcessingInput $input, ProcessedFieldsResult $result): void
    {
        $labelCapability = LabelCapability::createFromArray($input->yaml);
        if (!$labelCapability->hasLabelField()) {
            return;
        }
        $labelFields = $labelCapability->getLabelFieldsAsArray();
        for ($i = 0; $i < count($labelFields); $i++) {
            $currentLabelField = $labelFields[$i];
            if (in_array($currentLabelField, $result->uniqueFieldIdentifiers, true)) {
                if (is_string($result->tableDefinition->raw['labelField'])) {
                    $result->tableDefinition->raw['labelField'] = [];
                }
                $result->tableDefinition->raw['labelField'][$i] = $result->identifierToUniqueMap[$currentLabelField];
            }
        }
    }

    private function prefixFallbackLabelFieldsIfNecessary(ProcessingInput $input, ProcessedFieldsResult $result): void
    {
        $labelCapability = LabelCapability::createFromArray($input->yaml);
        if (!$labelCapability->hasFallbackLabelFields()) {
            return;
        }
        $fallbackLabelFields = $labelCapability->getFallbackLabelFields();
        for ($i = 0; $i < count($fallbackLabelFields); $i++) {
            $currentLabelField = $fallbackLabelFields[$i];
            if (in_array($currentLabelField, $result->uniqueFieldIdentifiers, true)) {
                $result->tableDefinition->raw['fallbackLabelFields'][$i] = $result->identifierToUniqueMap[$currentLabelField];
            }
        }
    }

    private function isPrefixEnabledForField(LoadedContentBlock $contentBlock, array $fieldConfiguration): bool
    {
        if ($this->isExistingField($fieldConfiguration)) {
            return false;
        }
        if (array_key_exists('prefixField', $fieldConfiguration)) {
            return (bool)$fieldConfiguration['prefixField'];
        }
        return $contentBlock->prefixFields();
    }

    private function isExistingField(array $fieldConfiguration): bool
    {
        return (bool)($fieldConfiguration['useExistingField'] ?? false);
    }

    private function getPrefixType(LoadedContentBlock $contentBlock, array $fieldConfiguration): PrefixType
    {
        if (array_key_exists('prefixType', $fieldConfiguration)) {
            return PrefixType::from($fieldConfiguration['prefixType']);
        }
        return $contentBlock->getPrefixType();
    }

    private function resolveType(ProcessingInput $input, array $field): FieldType
    {
        $isExistingField = $this->isExistingField($field);
        if ($isExistingField) {
            $this->assertIdentifierExists($field, $input);
            $identifier = $field['identifier'];
            // Check if the field is defined as a "base" TCA field (NOT defined in TCA/Overrides).
            if (($GLOBALS['TCA'][$input->table]['columns'][$identifier] ?? []) !== []) {
                $fieldType = TypeResolver::resolve($field['identifier'], $input->table);
                return $fieldType;
            }
        }
        $this->assertTypeExists($field, $input);
        $fieldType = FieldType::tryFrom($field['type']);
        if ($fieldType === null) {
            $validTypesList = array_map(fn(FieldType $fieldType) => $fieldType->value, FieldType::cases());
            $validTypes = implode(', ', $validTypesList);
            throw new \InvalidArgumentException(
                'The type "' . $field['type'] . '" is not a valid type in Content Block "' . $input->contentBlock->getName() . '". Valid types are: ' . $validTypes . '.',
                1697625849
            );
        }
        if ($fieldType !== FieldType::LINEBREAK) {
            $this->assertIdentifierExists($field, $input);
        }
        return $fieldType;
    }

    private function chooseIdentifier(ProcessingInput $input, array $field): string
    {
        if (!$input->isRootTable()) {
            return $field['identifier'];
        }
        $prefixEnabled = $this->isPrefixEnabledForField($input->contentBlock, $field);
        if (!$prefixEnabled) {
            return $field['identifier'];
        }
        $prefixType = $this->getPrefixType($input->contentBlock, $field);
        $uniqueIdentifier = UniqueIdentifierCreator::prefixIdentifier($input->contentBlock, $prefixType, $field['identifier']);
        return $uniqueIdentifier;
    }

    private function buildBaseLanguagePath(LoadedContentBlock $contentBlock): LanguagePath
    {
        $baseExtPath = 'LLL:' . $contentBlock->getExtPath();
        $languagePathString = $baseExtPath . '/' . ContentBlockPathUtility::getLanguageFilePath();
        $languagePath = new LanguagePath($languagePathString);
        return $languagePath;
    }

    private function assertNoLinebreakOutsideOfPalette(FieldType $fieldType, LoadedContentBlock $contentBlock): void
    {
        if ($fieldType === FieldType::LINEBREAK) {
            throw new \InvalidArgumentException(
                'Linebreaks are only allowed within Palettes in Content Block "' . $contentBlock->getName() . '".',
                1679224392
            );
        }
    }

    private function assertIdentifierExists(array $field, ProcessingInput $input): void
    {
        if (!isset($field['identifier'])) {
            throw new \InvalidArgumentException(
                'A field is missing the required "identifier" in Content Block "' . $input->contentBlock->getName() . '".',
                1679226075
            );
        }
    }

    private function assertTypeExists(array $field, ProcessingInput $input): void
    {
        if (!isset($field['type'])) {
            throw new \InvalidArgumentException(
                'The field "' . ($field['identifier'] ?? '') . '" is missing the required "type" in Content Block "' . $input->contentBlock->getName() . '".',
                1694768937
            );
        }
    }

    private function assertUniquePaletteIdentifier(string $identifier, ProcessedFieldsResult $result, LoadedContentBlock $contentBlock): void
    {
        if (in_array($identifier, $result->uniquePaletteIdentifiers, true)) {
            throw new \InvalidArgumentException(
                'The palette identifier "' . $identifier . '" in Content Block "' . $contentBlock->getName() . '" does exist more than once. Please choose unique identifiers.',
                1679168022
            );
        }
    }

    private function assertNoPaletteInPalette(FieldType $fieldType, string $identifier, string $rootFieldIdentifier, LoadedContentBlock $contentBlock): void
    {
        if ($fieldType === FieldType::PALETTE) {
            throw new \InvalidArgumentException(
                'Palette "' . $identifier . '" is not allowed inside palette "' . $rootFieldIdentifier . '" in Content Block "' . $contentBlock->getName() . '".',
                1679168602
            );
        }
    }

    private function assertNoTabInPalette(FieldType $fieldType, string $identifier, string $rootFieldIdentifier, LoadedContentBlock $contentBlock): void
    {
        if ($fieldType === FieldType::TAB) {
            throw new \InvalidArgumentException(
                'Tab "' . $identifier . '" is not allowed inside palette "' . $rootFieldIdentifier . '" in Content Block "' . $contentBlock->getName() . '".',
                1679245193
            );
        }
    }

    private function assertUniqueTabIdentifier(string $identifier, ProcessedFieldsResult $result, LoadedContentBlock $contentBlock): void
    {
        if (in_array($identifier, $result->uniqueTabIdentifiers, true)) {
            throw new \InvalidArgumentException(
                'The tab identifier "' . $identifier . '" in Content Block "' . $contentBlock->getName() . '" does exist more than once. Please choose unique identifiers.',
                1679243686
            );
        }
    }

    private function assertUniqueFieldIdentifier(ProcessedFieldsResult $result, LoadedContentBlock $contentBlock): void
    {
        if (in_array($result->identifier, $result->uniqueFieldIdentifiers, true)) {
            throw new \InvalidArgumentException(
                'The identifier "' . $result->identifier . '" in Content Block "' . $contentBlock->getName() . '" does exist more than once. Please choose unique identifiers.',
                1677407942
            );
        }
    }

    private function validateFlexFormHasOnlySheetsOrNoSheet(array $field, LoadedContentBlock $contentBlock): void
    {
        foreach ($field['fields'] ?? [] as $flexField) {
            $flexFormType = FlexFormSubType::tryFrom($flexField['type']);
            if ($flexFormType !== FlexFormSubType::SHEET) {
                $flexFormType = 'nonSheet';
            }
            $currentType ??= $flexFormType;
            $isValid = $currentType === $flexFormType;
            if (!$isValid) {
                throw new \InvalidArgumentException(
                    'You must not mix Sheets with normal fields inside the FlexForm definition "' . $field['identifier'] . '" in Content Block "' . $contentBlock->getName() . '".',
                    1685217163
                );
            }
            $currentType = $flexFormType;
        }
    }

    private function validateFlexFormContainsValidFieldTypes(array $field, LoadedContentBlock $contentBlock): void
    {
        foreach ($field['fields'] ?? [] as $flexField) {
            if (FlexFormSubType::tryFrom($flexField['type']) === FlexFormSubType::SHEET) {
                $this->validateFlexFormContainsValidFieldTypes($flexField, $contentBlock);
                continue;
            }
            if (FlexFormSubType::tryFrom($flexField['type']) === FlexFormSubType::SECTION) {
                if (empty($flexField['container'])) {
                    throw new \InvalidArgumentException(
                        'FlexForm field "' . $field['identifier'] . '" has a Section "' . $flexField['identifier'] . '" without "container" defined. This is invalid, please add at least one item to "container" in Content Block "' . $contentBlock->getName() . '".',
                        1686330220
                    );
                }
                foreach ($flexField['container'] as $container) {
                    if (empty($container['fields'])) {
                        throw new \InvalidArgumentException(
                            'FlexForm field "' . $field['identifier'] . '" has a Container in Section "' . $flexField['identifier'] . '" without "fields" defined. This is invalid, please add at least one field to "fields" in Content Block "' . $contentBlock->getName() . '".',
                            1686331469
                        );
                    }
                    foreach ($container['fields'] as $containerField) {
                        $containerType = FieldType::from($containerField['type']);
                        if (!FieldType::isValidFlexFormField($containerType)) {
                            throw new \InvalidArgumentException(
                                'FlexForm field "' . $field['identifier'] . '" has an invalid field of type "' . $containerType->value . '" inside of a "container" item. Please use valid field types in Content Block "' . $contentBlock->getName() . '".',
                                1686330594
                            );
                        }
                    }
                }
                continue;
            }
            $type = FieldType::from($flexField['type']);
            if (!FieldType::isValidFlexFormField($type)) {
                throw new \InvalidArgumentException(
                    'Field type "' . $type->value . '" with identifier "' . $flexField['identifier'] . '" is not allowed inside FlexForm in Content Block "' . $contentBlock->getName() . '".',
                    1685220309
                );
            }
        }
    }
}
