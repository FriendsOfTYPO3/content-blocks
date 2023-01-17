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
use TYPO3\CMS\ContentBlocks\Generator\HtmlTemplateCodeGenerator;
use TYPO3\CMS\ContentBlocks\Domain\Model\ContentBlockConfiguration;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ContentBlockBuilder
{
    public function __construct(
        protected HtmlTemplateCodeGenerator $htmlTemplateCodeGenerator,
    ) {
    }

    /**
     * Writes a ContentBlock to file system.
     */
    public function create(ContentBlockConfiguration $contentBlockConfiguration): self
    {
        $basePath = ContentBlockPathUtility::getAbsoluteContentBlockLegacyPath() . '/' .  $contentBlockConfiguration->package;
        if (file_exists($basePath)) {
            throw new \RuntimeException('A content block with the identifier "' . $contentBlockConfiguration->package . '" already exists.');
        }

        // create directory structure
        $privatePath = ContentBlockPathUtility::getAbsoluteContentBlocksPrivatePath($contentBlockConfiguration->package);
        $publicPath = ContentBlockPathUtility::getAbsoluteContentBlocksPublicPath($contentBlockConfiguration->package);
        GeneralUtility::mkdir_deep($publicPath);
        GeneralUtility::mkdir_deep($privatePath . '/Language');

        // create files
        file_put_contents(
            $basePath . '/composer.json',
            json_encode($contentBlockConfiguration->composerJson, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
        );
        file_put_contents(
            $privatePath . '/EditorInterface.yaml',
            Yaml::dump($contentBlockConfiguration->yamlConfig, 10)
        );
        file_put_contents(
            $privatePath . '/EditorPreview.html',
            $this->htmlTemplateCodeGenerator->getHtmlTemplateEditorPreview($contentBlockConfiguration)
        );
        file_put_contents(
            $privatePath . '/Frontend.html',
            $this->htmlTemplateCodeGenerator->getHtmlTemplateFrontend($contentBlockConfiguration)
        );
        foreach ($contentBlockConfiguration->labelsXlfContent as $key => $translation) {
            $localLangPrefix = ($key === 'default' ? '' : $key . '.');
            file_put_contents(
                $privatePath . '/Language/' . $localLangPrefix . 'Labels.xlf',
                $translation
            );
        }
        file_put_contents(
            $publicPath . '/EditorPreview.css',
            '/* Created by Content BlockWizard */'
        );
        file_put_contents(
            $publicPath . '/Frontend.css',
            '/* Created by Content BlockWizard */'
        );
        file_put_contents(
            $publicPath . '/Frontend.js',
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
        $cbBasePath = ContentBlockPathUtility::getAbsoluteContentBlockLegacyPath() . '/' . $contentBlockConf->package;

        // check if directory exists, if not, create a new ContentBlock.
        if (!file_exists($cbBasePath)) {
            return $this->create($contentBlockConf);
        }

        // update the yaml file
        file_put_contents(
            ContentBlockPathUtility::getAbsoluteContentBlocksPrivatePath($contentBlockConf->package) . '/EditorInterface.yaml',
            Yaml::dump($contentBlockConf->yamlConfig, 10)
        );

        // Update translations
        foreach ($contentBlockConf->labelsXlfContent as $key => $translation) {
            $localLangPrefix = ($key === 'default' ? '' : $key . '.');
            file_put_contents(
                ContentBlockPathUtility::getAbsoluteContentBlocksPrivatePath($contentBlockConf->package) . '/Language/' . $localLangPrefix . 'Labels.xlf',
                $translation
            );
        }
        return $this;
    }
}
