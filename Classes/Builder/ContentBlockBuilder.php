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

namespace TYPO3\CMS\ContentBlocks\Builder;

use Symfony\Component\Yaml\Yaml;
use TYPO3\CMS\ContentBlocks\CodeGenerator\HtmlTemplateCodeGenerator;
use TYPO3\CMS\ContentBlocks\Domain\Model\ContentBlockConfiguration;
use TYPO3\CMS\ContentBlocks\Service\ConfigurationService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Finds the configuration of all ContentBlocks or a single ContentBlock.
 */
class ContentBlockBuilder
{
    protected string $publicPath = '';
    protected string $privatePath = '';

    /**
     * Writes a ContentBlock to file system.
     */
    public function create(ContentBlockConfiguration $contentBlockConf): self
    {
        $cbBasePath = ConfigurationService::getContentBlockLegacyPath() . $contentBlockConf->package;
        // check if directory exists, if so, stop.
        if (file_exists($cbBasePath)) {
            throw new \RuntimeException('A content block with the identifier "' . $contentBlockConf->package . '" already exists.');
        }
        $htmlTemplateGenerator = GeneralUtility::makeInstance(HtmlTemplateCodeGenerator::class);

        // create directory structure
        mkdir($cbBasePath);
        $cbBasePath .= '/';
        mkdir($cbBasePath . ConfigurationService::getContentBlocksPublicPath(), 0777, true);
        mkdir($cbBasePath . ConfigurationService::getContentBlocksPrivatePath() . '/' . 'Language', 0777, true);

        // create files
        file_put_contents(
            $cbBasePath . 'composer.json',
            json_encode($contentBlockConf->composerJson, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
        );
        file_put_contents(
            $cbBasePath . ConfigurationService::getContentBlocksPrivatePath() . '/' . 'EditorInterface.yaml',
            Yaml::dump($contentBlockConf->yamlConfig, 10)
        );
        file_put_contents(
            $cbBasePath . ConfigurationService::getContentBlocksPrivatePath() . '/' . 'EditorPreview.html',
            $htmlTemplateGenerator->getHtmlTemplateEditorPreview($contentBlockConf)
        );
        file_put_contents(
            $cbBasePath . ConfigurationService::getContentBlocksPrivatePath() . '/' . 'Frontend.html',
            $htmlTemplateGenerator->getHtmlTemplateFrontend($contentBlockConf)
        );
        if (count($contentBlockConf->labelsXlfContent) > 0) {
            foreach ($contentBlockConf->labelsXlfContent as $key => $translation) {
                $localLangPrefix = ($key === 'default' ? '' : $key . '.');
                file_put_contents(
                    $cbBasePath . ConfigurationService::getContentBlocksPrivatePath() . '/' . 'Language/' . $localLangPrefix . 'Labels.xlf',
                    $translation
                );
            }
        }
        file_put_contents(
            $cbBasePath . ConfigurationService::getContentBlocksPublicPath() . '/' . 'EditorPreview.css',
            '/* Created by Content BlockWizard */'
        );
        file_put_contents(
            $cbBasePath . ConfigurationService::getContentBlocksPublicPath() . '/' . 'Frontend.css',
            '/* Created by Content BlockWizard */'
        );
        file_put_contents(
            $cbBasePath . ConfigurationService::getContentBlocksPublicPath() . '/' . 'Frontend.js',
            '/* Created by Content BlockWizard */'
        );

        // TODO: "transfer" the icon file
        return $this;
    }

    /**
     * At the moment: updates only the yaml file and the translations.
     */
    public function update(ContentBlockConfiguration $contentBlockConf): self
    {
        $cbBasePath = ConfigurationService::getContentBlockLegacyPath() . 'ContentBlockBuilder.php/' . $contentBlockConf->package;

        // check if directory exists, if not, create a new ContentBlock.
        if (!file_exists($cbBasePath)) {
            return $this->create($contentBlockConf);
        }

        // update the yaml file
        file_put_contents(
            $cbBasePath . ConfigurationService::getContentBlocksPrivatePath() . '/' . 'EditorInterface.yaml',
            Yaml::dump($contentBlockConf->yamlConfig, 10)
        );

        // Update translations
        if (count($contentBlockConf->labelsXlfContent) > 0) {
            foreach ($contentBlockConf->labelsXlfContent as $key => $translation) {
                $localLangPrefix = ($key === 'default' ? '' : $key . '.');
                file_put_contents(
                    $cbBasePath . ConfigurationService::getContentBlocksPrivatePath() . '/' . 'Language/' . $localLangPrefix . 'Labels.xlf',
                    $translation
                );
            }
        }
        return $this;
    }
}
