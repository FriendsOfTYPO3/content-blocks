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
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;
use TYPO3\CMS\ContentBlocks\Generator\HtmlTemplateCodeGenerator;
use TYPO3\CMS\ContentBlocks\Service\ContentTypeIconResolver;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @internal Not part of TYPO3's public API.
 */
class ContentBlockSkeletonBuilder
{
    public function __construct(
        protected readonly HtmlTemplateCodeGenerator $htmlTemplateCodeGenerator,
    ) {}

    /**
     * Writes a Content Block to file system.
     */
    public function create(ContentBlockConfiguration $contentBlockConfiguration): void
    {
        $vendor = $contentBlockConfiguration->getVendor();
        $name = $contentBlockConfiguration->getName();
        $basePath = $contentBlockConfiguration->getBasePath();
        $contentType = $contentBlockConfiguration->getContentType();
        if ($basePath === '') {
            throw new \RuntimeException('Path to package "' . $name . '" cannot be empty.', 1674225339);
        }
        $basePath .= '/' . $name;
        if (file_exists($basePath)) {
            throw new \RuntimeException('A content block with the name "' . $name . '" already exists in target extension.', 1674225340);
        }

        // create directory structure
        $publicPath = $basePath . '/' . ContentBlockPathUtility::getPublicFolder();
        GeneralUtility::mkdir_deep($publicPath);
        GeneralUtility::mkdir_deep($basePath . '/' . ContentBlockPathUtility::getLanguageFolderPath());

        // create files
        file_put_contents(
            $basePath . '/' . ContentBlockPathUtility::getContentBlockDefinitionFileName(),
            Yaml::dump($contentBlockConfiguration->getYamlConfig(), 10, 2)
        );

        $utc = new \DateTimeZone('UTC');
        $date = (new \DateTime())->setTimezone($utc)->format('c');
        $xliffContent = match ($contentType) {
            ContentType::CONTENT_ELEMENT => $this->getXliffMarkupForContentElement($vendor, $name, $date),
            ContentType::PAGE_TYPE => $this->getXliffMarkupForPageType($vendor, $name, $date),
            ContentType::RECORD_TYPE => $this->getXliffMarkupForRecordType($vendor, $name, $date),
        };
        file_put_contents(
            $basePath . '/' . ContentBlockPathUtility::getLanguageFilePath(),
            $xliffContent
        );
        if ($contentType === ContentType::CONTENT_ELEMENT) {
            file_put_contents(
                $basePath . '/' . ContentBlockPathUtility::getBackendPreviewPath(),
                $this->htmlTemplateCodeGenerator->generateEditorPreviewTemplate($contentBlockConfiguration)
            );
            file_put_contents(
                $basePath . '/' . ContentBlockPathUtility::getFrontendTemplatePath(),
                $this->htmlTemplateCodeGenerator->generateFrontendTemplate($contentBlockConfiguration)
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
        }
        $defaultIcon = ContentTypeIconResolver::getDefaultContentTypeIcon($contentType);
        $absoluteDefaultIconPath = GeneralUtility::getFileAbsFileName($defaultIcon);
        $contentBlockIconPath = $basePath . '/' . ContentBlockPathUtility::getIconPathWithoutFileExtension() . '.svg';
        copy($absoluteDefaultIconPath, $contentBlockIconPath);
    }

    protected function getXliffMarkupForContentElement(string $vendor, string $name, string $date): string
    {
        $xliffContent = <<<HEREDOC
<?xml version="1.0"?>
<xliff version="1.2" xmlns="urn:oasis:names:tc:xliff:document:1.2">
	<file datatype="plaintext" original="Labels.xlf" source-language="en" date="$date" product-name="$vendor/$name">
		<header/>
		<body>
			<trans-unit id="title" resname="title">
				<source>Content Element: $vendor/$name</source>
			</trans-unit>
			<trans-unit id="description" resname="description">
				<source>This is your Content Element description</source>
			</trans-unit>
			<trans-unit id="header.label" resname="header.label">
				<source>Custom header title</source>
			</trans-unit>
		</body>
	</file>
</xliff>

HEREDOC;
        return $xliffContent;
    }

    protected function getXliffMarkupForPageType(string $vendor, string $name, string $date): string
    {
        $xliffContent = <<<HEREDOC
<?xml version="1.0"?>
<xliff version="1.2" xmlns="urn:oasis:names:tc:xliff:document:1.2">
	<file datatype="plaintext" original="Labels.xlf" source-language="en" date="$date" product-name="$vendor/$name">
		<header/>
		<body>
			<trans-unit id="title" resname="title">
				<source>Page Type: $vendor/$name</source>
			</trans-unit>
			<trans-unit id="description" resname="description">
				<source>This is your Page Type description</source>
			</trans-unit>
		</body>
	</file>
</xliff>

HEREDOC;
        return $xliffContent;
    }

    protected function getXliffMarkupForRecordType(string $vendor, string $name, string $date): string
    {
        $xliffContent = <<<HEREDOC
<?xml version="1.0"?>
<xliff version="1.2" xmlns="urn:oasis:names:tc:xliff:document:1.2">
	<file datatype="plaintext" original="Labels.xlf" source-language="en" date="$date" product-name="$vendor/$name">
		<header/>
		<body>
			<trans-unit id="title" resname="title">
				<source>Record Type: $vendor/$name</source>
			</trans-unit>
			<trans-unit id="description" resname="description">
				<source>This is your Record Type description</source>
			</trans-unit>
			<trans-unit id="title.label" resname="title.label">
				<source>Custom title</source>
			</trans-unit>
		</body>
	</file>
</xliff>

HEREDOC;
        return $xliffContent;
    }
}
