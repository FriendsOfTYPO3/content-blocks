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

use TYPO3\CMS\ContentBlocks\Loader\LoadedContentBlock;

/**
 * @internal Not part of TYPO3's public API.
 */
class HtmlTemplateCodeGenerator
{
    public function generateEditorPreviewTemplate(LoadedContentBlock $contentBlockConfiguration): string
    {
        $package = $contentBlockConfiguration->getName();
        $vendor = $contentBlockConfiguration->getVendor();
        $defaultContent[] = '<cb:asset.css identifier="content-block-' . $vendor . '-' . $package . '-be" file="EditorPreview.css"/>';
        $defaultContent[] = '';
        $defaultContent[] = 'Preview for Content Block: ' . $contentBlockConfiguration->getName() . '<br>';
        $defaultContent[] = 'Header: {data.header}';
        $defaultContentString = implode("\n", $defaultContent);

        return $defaultContentString;
    }

    public function generateFrontendTemplate(LoadedContentBlock $contentBlockConfiguration): string
    {
        $package = $contentBlockConfiguration->getName();
        $vendor = $contentBlockConfiguration->getVendor();
        $frontendTemplate[] = '<cb:asset.css identifier="content-block-css-' . $vendor . '-' . $package . '" file="Frontend.css"/>';
        $frontendTemplate[] = '<cb:asset.script identifier="content-block-js-' . $vendor . '-' . $package . '" file="Frontend.js"/>';
        $frontendTemplate[] = '';
        $frontendTemplate[] = 'Frontend template for Content Block: ' . $contentBlockConfiguration->getName() . '<br>';
        $frontendTemplate[] = 'Header: {data.header}';
        $frontendTemplateString = implode("\n", $frontendTemplate);

        return $frontendTemplateString;
    }
}
