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
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * TranslateViewHelper
 *
 * ONLY TO BE USED INSIDE CONTENT BLOCKS
 *
 * Examples
 * ========
 *
 * <cb:translate key="my.contentblock.header" />
 */
class TranslateViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('key', 'string', 'The key of the label to use.', true);
        $this->registerArgument('name', 'string', 'The vendor/package of the Content Block.');
        $this->registerArgument('default', 'string', 'If the given locallang key could not be found, this value is used. If this argument is not set, child nodes will be used to render the default');
        $this->registerArgument('arguments', 'array', 'Arguments to be replaced in the resulting string');
    }

    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext): string
    {
        $name = (string)($arguments['name'] ?? $renderingContext->getVariableProvider()->get('data._name'));
        $key = (string)$arguments['key'];
        $default = (string)($arguments['default'] ?? $renderChildrenClosure() ?? '');
        $translateArguments = $arguments['arguments'];

        $contentBlockRegistry = GeneralUtility::makeInstance(ContentBlockRegistry::class);
        $languagePath = '';
        if ($contentBlockRegistry->hasContentBlock($name)) {
            $languagePath = 'LLL:' . $contentBlockRegistry->getContentBlockPath($name) . '/' . ContentBlockPathUtility::getLanguageFilePath() . ':' . $key;
        }

        $value = self::getLanguageService()->sL($languagePath);
        if (empty($value)) {
            // In case $value is empty (LLL: could not be resolved) fall back to the default.
            $value = $default;
        }
        if (!empty($translateArguments)) {
            $value = vsprintf($value, $translateArguments);
        }
        return $value;
    }

    protected static function getLanguageService(): LanguageService
    {
        $languageServiceFactory = GeneralUtility::makeInstance(LanguageServiceFactory::class);
        if ($GLOBALS['TYPO3_REQUEST'] !== null && ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isFrontend()) {
            return $languageServiceFactory->createFromSiteLanguage($GLOBALS['TYPO3_REQUEST']->getAttribute('language')
                ?? $GLOBALS['TYPO3_REQUEST']->getAttribute('site')->getDefaultLanguage());
        }
        
        return $languageServiceFactory->createFromUserPreferences($GLOBALS['BE_USER'] ?? null);
    }
}
