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
class HtmlTemplateCodeGenerator
{
    public function generateEditorPreviewTemplate(LoadedContentBlock $contentBlockConfiguration): string
    {
        $defaultContent = [];

        if ($contentBlockConfiguration->getContentType() === ContentType::PAGE_TYPE) {
            $defaultContent[] = '<html xmlns:be="http://typo3.org/ns/TYPO3/CMS/Backend/ViewHelpers" data-namespace-typo3-fluid="true">';
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
            $package = $contentBlockConfiguration->getName();
            $vendor = $contentBlockConfiguration->getVendor();

            $defaultContent[] = '<f:asset.css identifier="content-block-' . $vendor . '-' . $package . '-be" href="{cb:assetPath()}/preview.css"/>';
            $defaultContent[] = '';
            $defaultContent[] = 'Preview for Content Block: ' . $contentBlockConfiguration->getName() . '<br>';
            $defaultContent[] = 'Header: {data.header}';
        }

        $defaultContentString = implode("\n", $defaultContent);

        return $defaultContentString;
    }

    public function generateFrontendTemplate(LoadedContentBlock $contentBlockConfiguration): string
    {
        $package = $contentBlockConfiguration->getName();
        $vendor = $contentBlockConfiguration->getVendor();
        $frontendTemplate[] = '<f:asset.css identifier="content-block-css-' . $vendor . '-' . $package . '" href="{cb:assetPath()}/frontend.css"/>';
        $frontendTemplate[] = '<f:asset.script identifier="content-block-js-' . $vendor . '-' . $package . '" src="{cb:assetPath()}/frontend.js"/>';
        $frontendTemplate[] = '';
        $frontendTemplate[] = 'Frontend template for Content Block: ' . $contentBlockConfiguration->getName() . '<br>';
        $frontendTemplate[] = 'Header: {data.header}';
        $frontendTemplateString = implode("\n", $frontendTemplate);

        return $frontendTemplateString;
    }
}
