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
 * CssViewHelper
 *
 * ONLY TO BE USED INSIDE CONTENT BLOCKS
 *
 * Examples
 * ========
 *
 * <cb:asset.css identifier="identifier123" file="Frontend.css" />
 * <cb:asset.css identifier="identifier123" file="SubDirectory/style.css" />
 */
final class CssViewHelper extends AbstractTagBasedViewHelper
{
    /**
     * This VH does not produce direct output, thus does not need to be wrapped in an escaping node
     *
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * Rendered children string is passed as CSS code,
     * there is no point in HTML encoding anything from that.
     *
     * @var bool
     */
    protected $escapeChildren = true;

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
        $this->registerTagAttribute('name', 'string', 'Define the name (vendor/package) of the content block.');
        $this->registerTagAttribute('as', 'string', 'Define the type of content being loaded (For rel="preload" or rel="prefetch" only).');
        $this->registerTagAttribute('crossorigin', 'string', 'Define how to handle crossorigin requests.');
        $this->registerTagAttribute('disabled', 'bool', 'Define whether or not the described stylesheet should be loaded and applied to the document.');
        $this->registerTagAttribute('hreflang', 'string', 'Define the language of the resource (Only to be used if \'href\' is set).');
        $this->registerTagAttribute('importance', 'string', 'Define the relative fetch priority of the resource.');
        $this->registerTagAttribute('integrity', 'string', 'Define base64-encoded cryptographic hash of the resource that allows browsers to verify what they fetch.');
        $this->registerTagAttribute('media', 'string', 'Define which media type the resources applies to.');
        $this->registerTagAttribute('referrerpolicy', 'string', 'Define which referrer is sent when fetching the resource.');
        $this->registerTagAttribute('rel', 'string', 'Define the relationship of the target object to the link object.');
        $this->registerTagAttribute('sizes', 'string', 'Define the icon size of the resource.');
        $this->registerTagAttribute('type', 'string', 'Define the MIME type (usually \'text/css\').');
        $this->registerTagAttribute('nonce', 'string', 'Define a cryptographic nonce (number used once) used to whitelist inline styles in a style-src Content-Security-Policy.');
        $this->registerArgument('useNonce', 'bool', 'Whether to use the global nonce value', false, false);
        $this->registerArgument(
            'identifier',
            'string',
            'Use this identifier within templates to only inject your CSS once, even though it is added multiple times.',
            true
        );
        $this->registerArgument(
            'priority',
            'boolean',
            'Define whether the CSS should be included before other CSS. CSS will always be output in the <head> tag.',
            false,
            false
        );
    }

    public function render(): string
    {
        $identifier = (string)$this->arguments['identifier'];
        $attributes = $this->tag->getAttributes();

        // boolean attributes shall output attr="attr" if set
        if ($attributes['disabled'] ?? false) {
            $attributes['disabled'] = 'disabled';
        }

        $name = $attributes['name'] ?? $this->templateVariableContainer->get('data._name');
        $file = $attributes['file'];
        unset(
            $attributes['name'],
            $attributes['file'],
        );
        if (Environment::isComposerMode()) {
            $file = ContentBlockPathUtility::getSymlinkedAssetsPath($name) . '/' . $file;
        } else {
            $file = $this->contentBlockRegistry->getContentBlockExtPath($name) . '/' . ContentBlockPathUtility::getPublicFolder() . '/' . $file;
        }
        $options = [
            'priority' => $this->arguments['priority'],
            'useNonce' => $this->arguments['useNonce'],
        ];
        $this->assetCollector->addStyleSheet($identifier, $file, $attributes, $options);
        return '';
    }
}
