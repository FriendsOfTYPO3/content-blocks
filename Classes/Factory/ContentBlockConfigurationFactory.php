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

namespace TYPO3\CMS\ContentBlocks\Factory;

use TYPO3\CMS\ContentBlocks\Domain\Model\ContentBlockConfiguration;
use TYPO3\CMS\ContentBlocks\Enumeration\FieldType;
use TYPO3\CMS\ContentBlocks\Service\ConfigurationService;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ContentBlockConfigurationFactory implements SingletonInterface
{
    public function createFromArray(array $config): ContentBlockConfiguration
    {
        // basic check the $config array
        if (
            !isset($config['composerJson'])
            || !is_array($config['composerJson'])
            || count($config['composerJson']) < 1
        ) {
            throw new \Exception(sprintf('Cannot create ContentBlock from empty composer.json.'));
        }

        /** @var ContentBlockConfiguration */
        $cbConf = GeneralUtility::makeInstance(ContentBlockConfiguration::class);

        [$cbConf->vendor, $cbConf->package] = explode('/', $config['composerJson']['name']);
        $cbConf->path = ConfigurationService::getContentBlockLegacyPath() . $cbConf->package . '/';
        $cbConf->wizardGroup = $config['yaml']['group'] ?? $cbConf->wizardGroup;
        $cbConf->composerJson = $config['composerJson'];
        $cbConf->yamlConfig = $config['yaml'] ?? $cbConf->yamlConfig;

        $cbConf->publicPath = $cbConf->path . ConfigurationService::getContentBlocksPublicPath() . '/';

        // Setting the frontendTemplatesPath has to be before re-setting the privatePath.
        // Reason: trailing '/' must not be there for templates path
        $cbConf->frontendTemplatesPath = $cbConf->path . $cbConf->privatePath;

        $cbConf->privatePath = $cbConf->path . ConfigurationService::getContentBlocksPrivatePath() . '/';
        $cbConf->frontendPartialsPath = $cbConf->privatePath . 'Partials';
        $cbConf->frontendLayoutsPath = $cbConf->privatePath . 'Layouts';

        $cbConf->editorPreviewHtml = $cbConf->privatePath . 'EditorPreview.html';

        // translations
        $cbConf->labelsXlfPath = $cbConf->privatePath . 'Language' . '/' . 'Labels.xlf';

        $cbConf->editorLLL = 'LLL:' . $cbConf->labelsXlfPath . ':' . $cbConf->vendor . '.' . $cbConf->package;
        $cbConf->frontendLLL = 'LLL:' . $cbConf->labelsXlfPath . ':' . $cbConf->vendor . '.' . $cbConf->package;

        if (isset($config['translations']) && count($config['translations']) > 0) {
            $cbConf->labelsXlfContent = $config['translations'];
        }

        // fill in icon data
        $cbConf->icon = $config['icon'] ?? $cbConf->icon;

        // add fields: Use TableDefinitionCollection
        if (isset($config['yaml']['fields']) && count($config['yaml']['fields']) > 0) {
            foreach ($config['yaml']['fields'] as $fieldConfigFromYaml) {
                $fieldType = FieldType::from($fieldConfigFromYaml['type']);

                $fieldConfig = $fieldType->getFieldTypeConfiguration($fieldConfigFromYaml);
                $cbConf->fieldsConfig[$fieldConfig->identifier] = $fieldConfig;
            }
        }

        // fill missing data to composerJson
        $cbConf->composerJson['type'] = $cbConf->composerJson['type'] ?? ConfigurationService::getComposerType();
        $cbConf->composerJson['license'] = $cbConf->composerJson['license'] ?? 'GPL-2.0-or-later';
        $cbConf->composerJson['require'] = $cbConf->composerJson['require'] ?? ['typo3/cms-content-blocks' => '*'];

        return $cbConf;
    }
}
