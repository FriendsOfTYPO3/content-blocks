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

namespace TYPO3\CMS\ContentBlocks\Tabs;

use TYPO3\CMS\ContentBlocks\FieldType\SpecialFieldType;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\ContentBlocks\Yaml\ContentBlocksYamlParserInterface;

/**
 * Contains main logic for Tab replacement. Tabs are YAML files local to a
 * Content Block, which are referenced by their file name in the root `tabs`
 * array of the config.yaml:
 *
 * my-content-block
 * |__ config.yaml < tabs: [settings]
 * |__ tabs
 *     |__ settings.yaml
 *
 * Each Tab is converted into a field of type `Tab` followed by the Tab's
 * fields and appended after the root fields. The Tab identifier defaults to
 * the file name. Tab files, which are not referenced, are ignored.
 *
 * Tabs must be applied before Basics, so that global `basics` are appended
 * after the Tabs and Basics used inside Tab files are resolved, too.
 *
 * @internal Not part of TYPO3's public API.
 */
final readonly class TabsService
{
    public function __construct(
        private ContentBlocksYamlParserInterface $contentBlocksYamlParser,
    ) {}

    public function applyTabs(string $absoluteContentBlockPath, array $yaml): array
    {
        if (array_key_exists('tabs', $yaml) === false) {
            return $yaml;
        }
        $name = (string)($yaml['name'] ?? '');
        if (is_array($yaml['tabs']) === false) {
            throw new \InvalidArgumentException(
                'The option "tabs" in Content Block "' . $name . '" must be a list of Tab names.',
                1794566428
            );
        }
        $fields = $yaml['fields'] ?? [];
        foreach ($yaml['tabs'] as $tabName) {
            $tabYaml = $this->loadTab($absoluteContentBlockPath, $tabName, $name);
            $tabFields = $this->createTabFields($tabYaml, $tabName);
            $fields = array_merge($fields, $tabFields);
        }
        $yaml['fields'] = $fields;

        return $yaml;
    }

    /**
     * Returns the parsed Tab file as is, e.g. for linting it against the JSON Schema.
     *
     * @param string $name name of the Content Block for error messages
     */
    public function loadTab(string $absoluteContentBlockPath, mixed $tabName, string $name): array
    {
        if (is_string($tabName) === false || $tabName === '') {
            throw new \InvalidArgumentException(
                'The option "tabs" in Content Block "' . $name . '" must only contain non-empty Tab names.',
                1794566429
            );
        }
        $tabPath = $absoluteContentBlockPath . '/' . ContentBlockPathUtility::getTabsFolder() . '/' . $tabName . '.yaml';
        if (file_exists($tabPath) === false) {
            throw new \RuntimeException(
                'The Tab "' . $tabName . '" in Content Block "' . $name . '" could not be found in "' . $tabPath . '".',
                1794566430
            );
        }
        $tabYaml = $this->contentBlocksYamlParser->parse($tabPath);
        if (is_array($tabYaml) === false || is_array($tabYaml['fields'] ?? null) === false) {
            throw new \RuntimeException(
                'Invalid Tab file in "' . $tabPath . '": Cannot find a "fields" list.',
                1794566431
            );
        }

        return $tabYaml;
    }

    /**
     * @return list<array> the Tab field followed by the fields of the Tab file
     */
    private function createTabFields(array $tabYaml, string $tabName): array
    {
        $tabField = $this->createTabField($tabYaml, $tabName);
        $tabFields = [$tabField, ...$tabYaml['fields']];

        return $tabFields;
    }

    private function createTabField(array $tabYaml, string $tabName): array
    {
        $tabField = [
            'identifier' => (string)($tabYaml['identifier'] ?? $tabName),
            'type' => SpecialFieldType::TAB->value,
        ];
        if (isset($tabYaml['label'])) {
            $tabField['label'] = (string)$tabYaml['label'];
        }

        return $tabField;
    }
}
