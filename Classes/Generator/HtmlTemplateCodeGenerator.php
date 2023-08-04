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

use TYPO3\CMS\ContentBlocks\Builder\ContentBlockConfiguration;

/**
 * @internal Not part of TYPO3's public API.
 */
class HtmlTemplateCodeGenerator
{
    public function generateEditorPreviewTemplate(ContentBlockConfiguration $contentBlockConfiguration): string
    {
        $package = $contentBlockConfiguration->getName();
        $vendor = $contentBlockConfiguration->getVendor();
        return '<cb:asset.css identifier="content-block-' . $vendor . '-' . $package . '-be" file="EditorPreview.css"/>' . "\n";
    }

    public function generateFrontendTemplate(ContentBlockConfiguration $contentBlockConfiguration): string
    {
        $package = $contentBlockConfiguration->getName();
        $vendor = $contentBlockConfiguration->getVendor();
        $frontendTemplate[] = '<cb:asset.css identifier="content-block-css-' . $vendor . '-' . $package . '" file="Frontend.css"/>';
        $frontendTemplate[] = '<cb:asset.script identifier="content-block-js-' . $vendor . '-' . $package . '" file="Frontend.js"/>';
        $frontendTemplate[] = '';
        return implode("\n", $frontendTemplate);
    }
}
