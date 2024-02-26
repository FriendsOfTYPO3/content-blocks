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

namespace TYPO3\CMS\ContentBlocks\ViewHelpers\Uri;

use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use TYPO3Fluid\Fluid\Exception;

class ResourceViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    public function initializeArguments(): void
    {
        $this->registerArgument('path', 'string', 'The file path of the resource (relative to Assets directory of the Content Block).', true);
        $this->registerArgument('name', 'string', 'Target Content Block name. If not set, the current Content Block will be used');
        $this->registerArgument('absolute', 'bool', 'If set, an absolute URI is rendered', false, false);
    }

    /**
     * Render the URI to the resource. The filename is used from child content.
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext): string
    {
        $name = (string)($arguments['name'] ?? $renderingContext->getVariableProvider()->get('data._name'));
        $filePath = $arguments['path'];
        if ($name === '') {
            throw new Exception(__CLASS__ . ' seemingly called outside Content Blocks context.', 1701198923);
        }
        if (Environment::isComposerMode()) {
            $uri = ContentBlockPathUtility::getSymlinkedAssetsPath($name) . '/' . $filePath;
            $uri = GeneralUtility::getIndpEnv('TYPO3_SITE_PATH') . $uri;
        } else {
            $contentBlockRegistry = GeneralUtility::makeInstance(ContentBlockRegistry::class);
            $extPath = $contentBlockRegistry->getContentBlockExtPath($name) . '/' . ContentBlockPathUtility::getPublicFolder() . '/' . $filePath;
            $absoluteFilePath = GeneralUtility::getFileAbsFileName($extPath);
            $uri = PathUtility::getAbsoluteWebPath($absoluteFilePath);
        }
        if ($arguments['absolute']) {
            $uri = GeneralUtility::locationHeaderUrl($uri);
        }
        return $uri;
    }
}
