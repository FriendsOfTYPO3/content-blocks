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
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;

class HtmlTemplateCodeGenerator
{
    public function generateEditorPreviewTemplate(ContentBlockConfiguration $contentBlockConfiguration): string
    {
        $package = $contentBlockConfiguration->getPackage();
        $vendor = $contentBlockConfiguration->getVendor();
        return '<f:asset.css identifier="content-block-' . $vendor . '-' . $package . '-be" href="' . ContentBlockPathUtility::getRelativeContentBlocksPublicPath($package, $vendor) . '/EditorPreview.css"/>' . "\n";
    }

    public function generateFrontendTemplate(ContentBlockConfiguration $contentBlockConfiguration): string
    {
        $package = $contentBlockConfiguration->getPackage();
        $vendor = $contentBlockConfiguration->getVendor();
        $frontendTemplate[] = '<f:asset.css identifier="content-block-css-' . $vendor . '-' . $package . '" href="' . ContentBlockPathUtility::getRelativeContentBlocksPublicPath($package, $vendor) . '/Frontend.css"/>';
        $frontendTemplate[] = '<f:asset.script identifier="content-block-js-' . $vendor . '-' . $package . '" src="' . ContentBlockPathUtility::getRelativeContentBlocksPublicPath($package, $vendor) . '/Frontend.js"/>';
        $frontendTemplate[] = '';
        return implode("\n", $frontendTemplate);
    }
}
