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

namespace TYPO3\CMS\ContentBlocks\Domain\Model;

use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\ContentBlocks\FieldConfiguration\AbstractFieldConfiguration;
use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;

/**
 * Class ContentBlockConfiguration
 * Contains the configuration of a ContentBlock.
 *
 * Note: Some objects using this model:
 * - ContentBlocksDataProcessor
 * - [TODO: list to be continued]
 */
class ContentBlockConfiguration
{
    public array $composerJson = [];
    public string $package = '';
    public string $path = '';
    public string $vendor = '';
    public array $yamlConfig = [];
    public string $wizardGroup = 'common';
    public string $publicPath = '';
    public string $privatePath = '';
    public string $frontendTemplatesPath = '';
    public string $frontendPartialsPath = '';
    public string $frontendLayoutsPath = '';
    public string $editorPreviewHtml = '';
    public string $labelsXlfPath = '';
    public string $icon = '';
    public array $labelsXlfContent = [];
    /**
     * What to write in the TCA label of the ContentBlock
     */
    public string $editorLLL = '';
    /**
     * TODO: evaluate if we still need this
     * Note: at the moment yes.
     */
    public string $frontendLLL = '';

    /**
     * @var TableDefinitionCollection $tableDefinitions
     */
    public TableDefinitionCollection $tableDefinitions;

    public function __construct()
    {
        $this->tableDefinitions = new TableDefinitionCollection();
    }

    /**
     * Get the key
     */
    public function getKey(): string
    {
        return $this->package;
    }

    /**
     * Get the key
     */
    public function getCType(): string
    {
        return $this->vendor . '_' . $this->package;
    }

    /**
     * Add or override the value of labelsXlfContent
     */
    public function addLabelsXlfContent(string $labelsXlfContent, string $languageShort = 'default'): self
    {
        $this->labelsXlfContent[$languageShort] = $labelsXlfContent;

        return $this;
    }

    public function addFieldConfigs(AbstractFieldConfiguration $config): self
    {
        $this->fieldsConfig[$config->identifier] = $config;
        return $this;
    }

    public function toArray(): array
    {
        // @todo:
        // check recursive Collections
        $fieldsList = [];
        if (count($this->fieldsConfig) > 0) {
            foreach ($this->fieldsConfig as $key => $tempFieldsConfig) {
                $fieldsList[$tempFieldsConfig->identifier] = $tempFieldsConfig->toArray();
            }
        }
        return [
            '__warning' => 'Contents of this "cb" configuration are not API yet and might change!',
            'vendor' => $this->vendor,
            'package' => $this->package,
            'key' => $this->getKey(),
            'path' => $this->path,
            'privatePath' => $this->privatePath,
            'publicPath' => $this->publicPath,
            'icon' => $this->icon,
            'iconProviderClass' => SvgIconProvider::class,
            'CType' => $this->getCType(),
            'composerJson' => $this->composerJson,
            'fields' => $fieldsList,
            // 'collectionFields' => $collectionFields,
            // 'fileFields' => $fileFields,
            // 'tableDefinition' => $this->tableDefinitions->toArray(),
            'frontendTemplatesPath' => $this->frontendTemplatesPath,
            'frontendPartialsPath' => $this->frontendPartialsPath,
            'frontendLayoutsPath' => $this->frontendLayoutsPath,
            'EditorPreview.html' => $this->editorPreviewHtml,
            'labelsXlfPath' => $this->labelsXlfPath,
            'EditorLLL' => $this->editorLLL,
            'FrontendLLL' => $this->frontendLLL,
            'yaml' => $this->yamlConfig,
        ];
    }
}
