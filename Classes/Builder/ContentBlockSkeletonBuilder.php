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
        $basePath = ContentBlockPathUtility::getAbsoluteContentBlockPath($package, $vendor);
        if (file_exists($basePath)) {
            throw new \RuntimeException('A content block with the identifier "' . $package . '" already exists.', 1674225339);
        }

        // create directory structure
        $privatePath = ContentBlockPathUtility::getAbsoluteContentBlockPrivatePath($package);
        $publicPath = ContentBlockPathUtility::getAbsoluteContentBlockPublicPath($package);
        GeneralUtility::mkdir_deep($publicPath);
        GeneralUtility::mkdir_deep($privatePath . '/Language');

        // create files
        file_put_contents(
            $basePath . '/composer.json',
            json_encode($contentBlockConfiguration->getComposerJson(), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
        );
        file_put_contents(
            $privatePath . '/EditorInterface.yaml',
            Yaml::dump($contentBlockConfiguration->getYamlConfig(), 10)
        );
        file_put_contents(
            $privatePath . '/EditorPreview.html',
            $this->htmlTemplateCodeGenerator->generateEditorPreviewTemplate($contentBlockConfiguration)
        );
        file_put_contents(
            $privatePath . '/Frontend.html',
            $this->htmlTemplateCodeGenerator->generateFrontendTemplate($contentBlockConfiguration)
        );

        $languageContent = <<<HEREDOC
<?xml version="1.0"?>
<xliff version="1.0">
	<file datatype="plaintext" original="messages" source-language="en" product-name="example">
		<header/>
		<body>
			<trans-unit id="$vendor.$package.title" xml:space="preserve">
				<source>Content Block title</source>
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
            $privatePath . '/Language/Labels.xlf',
            $languageContent
        );
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
        copy(
            GeneralUtility::getFileAbsFileName('EXT:content_blocks/Resources/Public/Icons/ContentBlockIcon.svg'),
            $publicPath . '/ContentBlockIcon.svg'
        );
    }
}
