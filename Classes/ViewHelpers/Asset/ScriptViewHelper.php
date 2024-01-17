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

namespace TYPO3\CMS\ContentBlocks\ViewHelpers\Asset;

use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Page\AssetCollector;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;

/**
 * ScriptViewHelper
 *
 * ONLY TO BE USED INSIDE CONTENT BLOCKS
 *
 * Examples
 * ========
 *
 *  <cb:asset.script identifier="identifier123" file="Frontend.js" />
 *  <cb:asset.script identifier="identifier123" file="SubDirectory/script.js" />
 */
final class ScriptViewHelper extends AbstractTagBasedViewHelper
{
    /**
     * This VH does not produce direct output, thus does not need to be wrapped in an escaping node
     *
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * Rendered children string is passed as JavaScript code,
     * there is no point in HTML encoding anything from that.
     *
     * @var bool
     */
    protected $escapeChildren = false;

    protected AssetCollector $assetCollector;
    protected ContentBlockRegistry $contentBlockRegistry;

    public function injectAssetCollector(AssetCollector $assetCollector): void
    {
        $this->assetCollector = $assetCollector;
    }

    public function injectContentBlockRegistry(ContentBlockRegistry $contentBlockRegistry): void
    {
        $this->contentBlockRegistry = $contentBlockRegistry;
    }

    public function initialize(): void
    {
        // Add a tag builder, that does not html encode values, because rendering with encoding happens in AssetRenderer
        $this->setTagBuilder(
            new class () extends TagBuilder {
                public function addAttribute($attributeName, $attributeValue, $escapeSpecialCharacters = false): void
                {
                    parent::addAttribute($attributeName, $attributeValue, false);
                }
            }
        );
        parent::initialize();
    }

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerUniversalTagAttributes();
        $this->registerTagAttribute('file', 'string', 'Define which file should be delivered from within the "Assets" directory.', true);
        $this->registerTagAttribute('name', 'string', 'Define the name (vendor/dir) of the content block.');
        $this->registerTagAttribute('async', 'bool', 'Define that the script will be fetched in parallel to parsing and evaluation.');
        $this->registerTagAttribute('crossorigin', 'string', 'Define how to handle crossorigin requests.');
        $this->registerTagAttribute('defer', 'bool', 'Define that the script is meant to be executed after the document has been parsed.');
        $this->registerTagAttribute('integrity', 'string', 'Define base64-encoded cryptographic hash of the resource that allows browsers to verify what they fetch.');
        $this->registerTagAttribute('nomodule', 'bool', 'Define that the script should not be executed in browsers that support ES2015 modules.');
        $this->registerTagAttribute('nonce', 'string', 'Define a cryptographic nonce (number used once) used to whitelist inline styles in a style-src Content-Security-Policy.');
        $this->registerTagAttribute('referrerpolicy', 'string', 'Define which referrer is sent when fetching the resource.');
        $this->registerTagAttribute('type', 'string', 'Define the MIME type (usually \'text/javascript\').');
        $this->registerArgument('useNonce', 'bool', 'Whether to use the global nonce value', false, false);
        $this->registerArgument(
            'identifier',
            'string',
            'Use this identifier within templates to only inject your JS once, even though it is added multiple times.',
            true
        );
        $this->registerArgument(
            'priority',
            'boolean',
            'Define whether the JavaScript should be put in the <head> tag above-the-fold or somewhere in the body part.',
            false,
            false
        );
    }

    public function render(): string
    {
        $identifier = (string)$this->arguments['identifier'];
        $attributes = $this->tag->getAttributes();

        // boolean attributes shall output attr="attr" if set
        foreach (['async', 'defer', 'nomodule'] as $_attr) {
            if ($attributes[$_attr] ?? false) {
                $attributes[$_attr] = $_attr;
            }
        }

        $name = $attributes['name'] ?? $this->templateVariableContainer->get('data._name');
        $file = $attributes['file'];
        unset(
            $attributes['name'],
            $attributes['file']
        );
        if (Environment::isComposerMode()) {
            $src = ContentBlockPathUtility::getSymlinkedAssetsPath($name) . '/' . $file;
        } else {
            $src = $this->contentBlockRegistry->getContentBlockExtPath($name) . '/' . ContentBlockPathUtility::getPublicFolder() . '/' . $file;
        }

        $options = [
            'priority' => $this->arguments['priority'],
            'useNonce' => $this->arguments['useNonce'],
        ];
        $this->assetCollector->addJavaScript($identifier, $src, $attributes, $options);
        return '';
    }
}
