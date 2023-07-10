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
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @internal Not part of TYPO3's public API.
 */
class ContentBlockSkeletonBuilder
{
    public function __construct(
        protected HtmlTemplateCodeGenerator $htmlTemplateCodeGenerator,
    ) {
    }

    /**
     * Writes a ContentBlock to file system.
     */
    public function create(ContentBlockConfiguration $contentBlockConfiguration): void
    {
        $vendor = $contentBlockConfiguration->getVendor();
        $package = $contentBlockConfiguration->getPackage();
        $basePath = $contentBlockConfiguration->getBasePath();
        if ($basePath === '') {
            throw new \RuntimeException('Path to package "' . $package . '" cannot be empty.', 1674225339);
        }
        $basePath .= '/' . $package;
        if (file_exists($basePath)) {
            throw new \RuntimeException('A content block with the identifier "' . $package . '" already exists.', 1674225340);
        }

        // create directory structure
        $publicPath = $basePath . '/' . ContentBlockPathUtility::getPublicFolderPath();
        GeneralUtility::mkdir_deep($publicPath);
        GeneralUtility::mkdir_deep($basePath . '/' . ContentBlockPathUtility::getLanguageFolderPath());

        // create files
        file_put_contents(
            $basePath . '/' . ContentBlockPathUtility::getEditorInterfacePath(),
            Yaml::dump($contentBlockConfiguration->getYamlConfig(), 10, 2)
        );
        file_put_contents(
            $basePath . '/' . ContentBlockPathUtility::getBackendPreviewPath(),
            $this->htmlTemplateCodeGenerator->generateEditorPreviewTemplate($contentBlockConfiguration)
        );
        file_put_contents(
            $basePath . '/' . ContentBlockPathUtility::getFrontendTemplatePath(),
            $this->htmlTemplateCodeGenerator->generateFrontendTemplate($contentBlockConfiguration)
        );

        $languageContent = <<<HEREDOC
<?xml version="1.0"?>
<xliff version="1.0">
	<file datatype="plaintext" original="messages" source-language="en" product-name="example">
		<header/>
		<body>
			<trans-unit id="$vendor.$package.title" xml:space="preserve">
				<source>Content Block: $package</source>
			</trans-unit>
			<trans-unit id="$vendor.$package.description" xml:space="preserve">
				<source>This is your content block description</source>
			</trans-unit>
            <trans-unit id="header.label" xml:space="preserve">
				<source>Custom header title</source>
			</trans-unit>
        </body>
	</file>
</xliff>
HEREDOC;

        file_put_contents(
            $basePath . '/' . ContentBlockPathUtility::getLanguageFilePath(),
            $languageContent
        );
        file_put_contents(
            $publicPath . '/EditorPreview.css',
            '/* Created by Content Block skeleton builder */'
        );
        file_put_contents(
            $publicPath . '/Frontend.css',
            '/* Created by Content Block skeleton builder */'
        );
        file_put_contents(
            $publicPath . '/Frontend.js',
            '/* Created by Content Block skeleton builder */'
        );
        copy(
            GeneralUtility::getFileAbsFileName('EXT:content_blocks/Resources/Public/Icons/ContentBlockIcon.svg'),
            $basePath . '/' . ContentBlockPathUtility::getIconPath()
        );
    }
}
