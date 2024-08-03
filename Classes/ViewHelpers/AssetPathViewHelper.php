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

namespace TYPO3\CMS\ContentBlocks\ViewHelpers;

use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use TYPO3Fluid\Fluid\Exception;

class AssetPathViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    public function initializeArguments(): void
    {
        $this->registerArgument('name', 'string', 'Target Content Block name. If not set, the current Content Block will be used');
    }

    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): string {
        $name = (string)($arguments['name'] ?? $renderingContext->getVariableProvider()->get('data._name'));
        if ($name === '') {
            throw new Exception(__CLASS__ . ' seemingly called outside Content Blocks context.', 1701198923);
        }
        $contentBlockRegistry = GeneralUtility::makeInstance(ContentBlockRegistry::class);
        $contentBlock = $contentBlockRegistry->getContentBlock($name);
        $assetExtPath = ContentBlockPathUtility::getHostExtPublicContentBlockPath(
            $contentBlock->getHostExtension(),
            $name
        );
        return $assetExtPath;
    }
}
