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

namespace TYPO3\CMS\ContentBlocks\Definition;

use TYPO3\CMS\ContentBlocks\Enumeration\FieldType;
use TYPO3\CMS\ContentBlocks\Loader\ParsedContentBlock;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\ContentBlocks\Utility\UniqueNameUtility;

/**
 * @internal Not part of TYPO3's public API.
 */
final class TableDefinitionCollection implements \IteratorAggregate
{
    /** @var TableDefinition[] */
    private array $definitions = [];
    /** @var list<string> */
    private array $customTables = [];

    public function addTable(TableDefinition $tableDefinition, $isCustomTable = false): void
    {
        if (!$this->hasTable($tableDefinition->getTable())) {
            $this->definitions[$tableDefinition->getTable()] = $tableDefinition;
            if ($isCustomTable) {
                $this->customTables[] = $tableDefinition->getTable();
            }
        }
    }

    public function isCustomTable(TableDefinition $tableDefinition): bool
    {
        return in_array($tableDefinition->getTable(), $this->customTables, true);
    }

    public function getTable(string $table): TableDefinition
    {
        if ($this->hasTable($table)) {
            return $this->definitions[$table];
        }
        throw new \OutOfBoundsException('The table "' . $table . '" does not exist.', 1628925803);
    }

    public function hasTable(string $table): bool
    {
        return isset($this->definitions[$table]);
    }

    /**
     * @param ParsedContentBlock[] $contentBlocks
     */
    public static function createFromArray(array $contentBlocks): TableDefinitionCollection
    {
        $tableDefinitionCollection = new self();
        $tableDefinitionList = [];
        foreach ($contentBlocks as $contentBlock) {
            $table = $contentBlock->getYaml()['table'] ?? 'tt_content';
            $tableDefinitionList = $tableDefinitionCollection->processContentBlock(
                yaml: $contentBlock->getYaml(),
                contentBlock: $contentBlock,
                table: $table,
                rootTable: $table,
                tableDefinitionList: $tableDefinitionList,
            );
        }
        foreach ($tableDefinitionList as $table => $tableDefinition) {
            $tableDefinitionCollection->addTable(TableDefinition::createFromTableArray($table, $tableDefinition));
        }
        return $tableDefinitionCollection;
    }

    private function processContentBlock(array $yaml, ParsedContentBlock $contentBlock, string $table, string $rootTable, array $tableDefinitionList, ?LanguagePath $languagePath = null): array
    {
        $languagePath ??= new LanguagePath('LLL:' . $contentBlock->getPath() . '/' . ContentBlockPathUtility::getLanguageFilePath());
        $uniqueIdentifiers = [];
        $uniquePaletteIdentifiers = [];
        $uniqueTabIdentifiers = [];
        $columns = [];
        $showItems = [];
        $overrideColumns = [];
        $tableDefinition = [];
        $tableDefinition['useAsLabel'] = $yaml['useAsLabel'] ?? '';
        $isRootTable = $table === $rootTable;
        // @todo Enable to create a new root table if table does not exist already.
        $shouldCreateNewTable = !$isRootTable;
        foreach ($yaml['fields'] as $rootField) {
            $rootFieldType = FieldType::from($rootField['type']);
            if ($rootFieldType === FieldType::LINEBREAK) {
                throw new \InvalidArgumentException(
                    'Linebreaks are only allowed within Palettes in content block "' . $contentBlock->getName() . '".',
                    1679224392
                );
            }
            if (!isset($rootField['identifier'])) {
                throw new \InvalidArgumentException(
                    'A field is missing the required "identifier" in content block "' . $contentBlock->getName() . '".',
                    1679226075
                );
            }
            $uniqueRootColumnName = UniqueNameUtility::createUniqueColumnNameFromContentBlockName($contentBlock->getName(), $rootField['identifier']);
            if ($rootFieldType === FieldType::PALETTE) {
                // Ignore empty Palettes.
                if (($rootField['fields'] ?? []) === []) {
                    continue;
                }
                if (in_array($rootField['identifier'], $uniquePaletteIdentifiers, true)) {
                    throw new \InvalidArgumentException(
                        'The palette identifier "' . $rootField['identifier'] . '" in content block "' . $contentBlock->getName() . '" does exist more than once. Please choose unique identifiers.',
                        1679168022
                    );
                }
                $uniquePaletteIdentifiers[] = $rootField['identifier'];
                $fields = [];
                $paletteShowItems = [];
                foreach ($rootField['fields'] as $paletteField) {
                    $paletteFieldType = FieldType::from($paletteField['type']);
                    if ($paletteFieldType === FieldType::PALETTE) {
                        throw new \InvalidArgumentException(
                            'Palette "' . $paletteField['identifier'] . '" is not allowed inside palette "' . $rootField['identifier'] . '" in content block "' . $contentBlock->getName() . '".',
                            1679168602
                        );
                    }
                    if ($paletteFieldType === FieldType::TAB) {
                        throw new \InvalidArgumentException(
                            'Tab "' . $paletteField['identifier'] . '" is not allowed inside palette "' . $rootField['identifier'] . '" in content block "' . $contentBlock->getName() . '".',
                            1679245193
                        );
                    }
                    if ($paletteFieldType === FieldType::LINEBREAK) {
                        $paletteShowItems[] = '--linebreak--';
                    } else {
                        $fields[] = $paletteField;
                        if ($isRootTable) {
                            $paletteShowItems[] = ($paletteField['useExistingField'] ?? false)
                                ? $paletteField['identifier']
                                : UniqueNameUtility::createUniqueColumnNameFromContentBlockName($contentBlock->getName(), $paletteField['identifier']);
                        } else {
                            $paletteShowItems[] = $paletteField['identifier'];
                        }
                    }
                }
                $languagePath->addPathSegment('palettes.' . $rootField['identifier']);
                $palette = [
                    'label' => $languagePath->getCurrentPath() . '.label',
                    'description' => $languagePath->getCurrentPath() . '.description',
                    'showitem' => $paletteShowItems,
                ];
                if ($isRootTable) {
                    $tableDefinitionList[$table]['palettes'][$uniqueRootColumnName] = $palette;
                    $showItems[] = '--palette--;;' . $uniqueRootColumnName;
                } else {
                    $tableDefinition['palettes'][$rootField['identifier']] = $palette;
                    $showItems[] = '--palette--;;' . $rootField['identifier'];
                }
                $languagePath->popSegment();
            } elseif ($rootFieldType === FieldType::TAB) {
                if (in_array($rootField['identifier'], $uniqueTabIdentifiers, true)) {
                    throw new \InvalidArgumentException(
                        'The tab identifier "' . $rootField['identifier'] . '" in content block "' . $contentBlock->getName() . '" does exist more than once. Please choose unique identifiers.',
                        1679243686
                    );
                }
                $uniqueTabIdentifiers[] = $rootField['identifier'];
                $languagePath->addPathSegment('tabs.' . $rootField['identifier']);
                $showItems[] = '--div--;' . $languagePath->getCurrentPath();
                $languagePath->popSegment();
                continue;
            } else {
                if ($isRootTable) {
                    $showItems[] = ($rootField['useExistingField'] ?? false)
                        ? $rootField['identifier']
                        : UniqueNameUtility::createUniqueColumnNameFromContentBlockName($contentBlock->getName(), $rootField['identifier']);
                } else {
                    $showItems[] = $rootField['identifier'];
                }
                $fields = [$rootField];
            }

            foreach ($fields as $field) {
                $identifier = $field['identifier'];
                $languagePath->addPathSegment($identifier);
                if (in_array($identifier, $uniqueIdentifiers, true)) {
                    throw new \InvalidArgumentException(
                        'The identifier "' . $identifier . '" in content block "' . $contentBlock->getName() . '" does exist more than once. Please choose unique identifiers.',
                        1677407942
                    );
                }
                $uniqueIdentifiers[] = $identifier;

                // Recursive call for Collection (inline) fields.
                if (FieldType::from($field['type']) === FieldType::COLLECTION && !empty($field['fields'])) {
                    $inlineTable = UniqueNameUtility::createUniqueColumnNameFromContentBlockName($contentBlock->getName(), $identifier);
                    $field['properties']['foreign_table'] = $inlineTable;
                    $field['properties']['foreign_field'] = 'foreign_table_parent_uid';
                    $tableDefinitionList = $this->processContentBlock(
                        yaml: $field,
                        contentBlock: $contentBlock,
                        table: $inlineTable,
                        rootTable: $rootTable,
                        tableDefinitionList: $tableDefinitionList,
                        languagePath: $languagePath,
                    );
                }

                $field['languagePath'] = $languagePath->getCurrentPath();
                if ($isRootTable) {
                    $uniqueColumnName = ($field['useExistingField'] ?? false)
                        ? $field['identifier']
                        : UniqueNameUtility::createUniqueColumnNameFromContentBlockName($contentBlock->getName(), $field['identifier']);
                    $columns[] = $uniqueColumnName;
                    $fieldArray = [
                        'uniqueIdentifier' => $uniqueColumnName,
                        'config' => $field,
                    ];
                    $tableDefinitionList[$table]['fields'][$uniqueColumnName] = $fieldArray;
                    // @todo only needed, if overriding existing tables like tt_content.
                    if ($field['useExistingField'] ?? false) {
                        $overrideColumns[] = TcaFieldDefinition::createFromArray($fieldArray);
                    }
                }

                if ($shouldCreateNewTable) {
                    if ($this->hasTable($table)) {
                        throw new \InvalidArgumentException('A Collection field with the identifier "' . $yaml['identifier'] . '" exists more than once. Please choose another name.', 1672449082);
                    }
                    // useExistingField is not allowed on Collections.
                    $field['useExistingField'] = false;
                    $tableDefinition['fields'][$identifier] = [
                        'uniqueIdentifier' => $identifier,
                        'config' => $field,
                    ];
                }
                $languagePath->popSegment();
            }
            $tableDefinition['showItems'] = $showItems;
        }

        // If this is the root table, we add a brand-new element (content type), with columns, type field ect.
        if ($isRootTable) {
            [$vendor, $package] = explode('/', $contentBlock->getName());
            $tableDefinitionList[$table]['elements'][] = [
                'identifier' => $contentBlock->getName(),
                'columns' => $columns,
                'showItems' => $showItems,
                'overrideColumns' => $overrideColumns, // @todo only needed, if overriding existing tables like tt_content.
                'vendor' => $vendor,
                'package' => $package,
                'wizardGroup' => $contentBlock->getYaml()['group'] ?? null,
                'icon' => $contentBlock->getIcon(),
                'iconProvider' => $contentBlock->getIconProvider(),
                'typeField' => $contentBlock->getYaml()['typeField'] ?? 'CType',
                'typeName' => $contentBlock->getYaml()['typeName'] ?? UniqueNameUtility::contentBlockNameToTypeIdentifier($contentBlock->getName()),
                'priority' => (int)($contentBlock->getYaml()['priority'] ?? 0),
            ];
        }

        // Collection fields are unique and require always an own table definition, which can't be shared across
        // other content blocks.
        if ($shouldCreateNewTable) {
            $this->addTable(
                tableDefinition: TableDefinition::createFromTableArray($table, $tableDefinition),
                isCustomTable: true
            );
        }

        return $tableDefinitionList;
    }

    public function getContentElementDefinition(string $CType): ?ContentElementDefinition
    {
        if (!$this->hasTable('tt_content')) {
            return null;
        }
        foreach ($this->getTable('tt_content')->getTypeDefinitionCollection() as $typeDefinition) {
            if (!$typeDefinition instanceof ContentElementDefinition) {
                continue;
            }
            if ($typeDefinition->getTypeName() === $CType) {
                return $typeDefinition;
            }
        }
        return null;
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->definitions);
    }
}
