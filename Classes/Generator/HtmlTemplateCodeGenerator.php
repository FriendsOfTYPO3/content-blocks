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

namespace TYPO3\CMS\ContentBlocks\Generator;

use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;
use TYPO3\CMS\ContentBlocks\Loader\LoadedContentBlock;

/**
 * @internal Not part of TYPO3's public API.
 */
readonly class HtmlTemplateCodeGenerator
{
    public function generateEditorPreviewTemplate(LoadedContentBlock $contentBlockConfiguration): string
    {
        $defaultContent = [];

        if ($contentBlockConfiguration->getContentType() === ContentType::PAGE_TYPE) {
            $defaultContent[] = '<html xmlns:be="http://typo3.org/ns/TYPO3/CMS/Backend/ViewHelpers" xmlns:f="http://typo3.org/ns/TYPO3/CMS/Fluid/ViewHelpers" data-namespace-typo3-fluid="true">';
            $defaultContent[] = '    <div class="card card-size-medium">';
            $defaultContent[] = '        <div class="card-body">';
            $defaultContent[] = '            <dl class="row">';
            $defaultContent[] = '                <dt class="col-sm-3">Title:</dt>';
            $defaultContent[] = '                <dd class="col-sm-9">';
            $defaultContent[] = '                   {data.title}';
            $defaultContent[] = '                </dd>';
            $defaultContent[] = '            </dl>';
            $defaultContent[] = '            <be:link.editRecord class="btn btn-default" uid="{data.uid}" table="{data.mainType}" fields="title">';
            $defaultContent[] = '               Edit page properties';
            $defaultContent[] = '            </be:link.editRecord>';
            $defaultContent[] = '        </div>';
            $defaultContent[] = '    </div>';
            $defaultContent[] = '</html>';
        } else {
            $defaultContent[] = '<html';
            $defaultContent[] = '    xmlns:f="http://typo3.org/ns/TYPO3/CMS/Fluid/ViewHelpers"';
            $defaultContent[] = '    xmlns:cb="http://typo3.org/ns/TYPO3/CMS/ContentBlocks/ViewHelpers"';
            $defaultContent[] = '    data-namespace-typo3-fluid="true"';
            $defaultContent[] = '>';
            $defaultContent[] = '';
            $defaultContent[] = '<f:layout name="Preview"/>';
            $defaultContent[] = '';
            $defaultContent[] = '<f:section name="Header">';
            $defaultContent[] = '    <cb:link.editRecord uid="{data.uid}" table="{data.mainType}">{data.header}</cb:link.editRecord>';
            $defaultContent[] = '</f:section>';
            $defaultContent[] = '';
            $defaultContent[] = '<f:section name="Content">';
            $defaultContent[] = '    Preview for Content Block: ' . $contentBlockConfiguration->getName();
            $defaultContent[] = '</f:section>';
            $defaultContent[] = '';
            $defaultContent[] = '<f:comment>';
            $defaultContent[] = '<!-- Uncomment to override preview footer -->';
            $defaultContent[] = '<f:section name="Footer">';
            $defaultContent[] = '    My custom Footer';
            $defaultContent[] = '</f:section>';
            $defaultContent[] = '</f:comment>';
            $defaultContent[] = '</html>';
        }

        $defaultContentString = implode("\n", $defaultContent);

        return $defaultContentString;
    }

    public function generateFrontendTemplate(LoadedContentBlock $contentBlockConfiguration): string
    {
        $frontendTemplate[] = '<html';
        $frontendTemplate[] = '    xmlns:f="http://typo3.org/ns/TYPO3/CMS/Fluid/ViewHelpers"';
        $frontendTemplate[] = '    xmlns:cb="http://typo3.org/ns/TYPO3/CMS/ContentBlocks/ViewHelpers"';
        $frontendTemplate[] = '    data-namespace-typo3-fluid="true"';
        $frontendTemplate[] = '>';
        $frontendTemplate[] = '';
        $frontendTemplate[] = 'Frontend template for Content Block: ' . $contentBlockConfiguration->getName() . '<br>';
        $frontendTemplate[] = 'Header: {data.header}';
        $frontendTemplate[] = '</html>';
        $frontendTemplateString = implode("\n", $frontendTemplate);

        return $frontendTemplateString;
    }
}
