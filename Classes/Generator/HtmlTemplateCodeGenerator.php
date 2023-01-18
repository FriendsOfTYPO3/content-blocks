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

class HtmlTemplateCodeGenerator
{
    /**
     * Get HTML Template for EditorPreview in create method
     */
    public function getHtmlTemplateEditorPreview(ContentBlockConfiguration $contentBlockConfiguration): string
    {
        $package = $contentBlockConfiguration->getPackage();
        $editorPreviewTemplate = '<html xmlns:f="http://typo3.org/ns/TYPO3/CMS/Fluid/ViewHelpers" xmlns:be="http://typo3.org/ns/TYPO3/CMS/Backend/ViewHelpers" data-namespace-typo3-fluid="true">' . "\n";
        $editorPreviewTemplate .= '    <f:asset.css identifier="content-block-' . $package . '-be" href="CB:' . $package . '/dist/EditorPreview.css"/>' . "\n";
        $editorPreviewTemplate .= "\n";
        $editorPreviewTemplate .= '    <be:link.editRecord uid="{data.uid}" table="tt_content" id="element-tt_content-{data.uid}">' . "\n";
        $editorPreviewTemplate .= '        <div class="' . $package . '">' . "\n";
//        $editorPreviewTemplate .= $this->getFieldsHtmlTemplate($contentBlockConfiguration->fieldsConfig) . "\n";
        $editorPreviewTemplate .= '        </div>' . "\n";
        $editorPreviewTemplate .= '    </be:link.editRecord>' . "\n";
        $editorPreviewTemplate .= '</html>' . "\n";
        return $editorPreviewTemplate;
    }

    /**
     * Get HTML Template for Frontend in create method
     */
    public function getHtmlTemplateFrontend(ContentBlockConfiguration $contentBlockConfiguration): string
    {
        $package = $contentBlockConfiguration->getPackage();
        $frontendTemplate = '<html xmlns:f="http://typo3.org/ns/TYPO3/CMS/Fluid/ViewHelpers" data-namespace-typo3-fluid="true">' . "\n";
        $frontendTemplate .= "\n";
        $frontendTemplate .= '    <f:layout name="Default" />' . "\n";
        $frontendTemplate .= "\n";
        $frontendTemplate .= '    <f:section name="Main">' . "\n";
        $frontendTemplate .= "\n";
        $frontendTemplate .= '        <f:asset.css identifier="content-block-' . $package . '-be" href="CB:' . $package . '/dist/EditorPreview.css"/>' . "\n";
        $frontendTemplate .= '        <f:asset.css identifier="content-block-' . $package . '" href="CB:' . $package . '/dist/Frontend.css"/>' . "\n";
        $frontendTemplate .= '        <f:asset.script identifier="content-block-' . $package . '" src="CB:' . $package . '/dist/Frontend.js"/>' . "\n";
        $frontendTemplate .= "\n";
        $frontendTemplate .= '        <div class="' . $package . '">' . "\n";
//        $frontendTemplate .= $this->getFieldsHtmlTemplate($contentBlockConfiguration->fieldsConfig) . "\n";
        $frontendTemplate .= '        </div>' . "\n";
        $frontendTemplate .= "\n";
        $frontendTemplate .= '    </f:section>' . "\n";
        $frontendTemplate .= '</html>' . "\n";
        return $frontendTemplate;
    }

    /**
     * get HTML template from all fields.
     */
    protected function getFieldsHtmlTemplate(array $fieldsConfig): string
    {
        $fieldsForTemplate = '';
        $indentation = '            '; // indentation to get a well formated html template file

        if (count($fieldsConfig) > 0) {
            foreach ($fieldsConfig as $tempFieldsConfig) {
                $fieldsForTemplate .= $tempFieldsConfig->getTemplateHtml($indentation);
            }
        }
        return $fieldsForTemplate;
    }
}
