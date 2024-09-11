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
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Exception;

/**
 * LanguagePathViewHelper
 *
 * ONLY TO BE USED INSIDE CONTENT BLOCKS
 *
 * Examples
 * ========
 *
 * <f:translate key="{cb:languagePath()}:header" />
 */
class LanguagePathViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        $this->registerArgument('name', 'string', 'The vendor/package of the Content Block.');
    }

    public function render(): string
    {
        $name = (string)($this->arguments['name'] ?? $this->renderingContext->getVariableProvider()->get('data._name'));
        if ($name === '') {
            throw new Exception(__CLASS__ . ' seemingly called outside Content Blocks context.', 1699271759);
        }
        $contentBlockRegistry = GeneralUtility::makeInstance(ContentBlockRegistry::class);
        if (!$contentBlockRegistry->hasContentBlock($name)) {
            throw new Exception('Content block with the name "' . $name . '" is not registered.', 1699272189);
        }
        $languagePath = 'LLL:' . $contentBlockRegistry->getContentBlockExtPath($name) . '/' . ContentBlockPathUtility::getLanguageFilePath();
        return $languagePath;
    }
}
