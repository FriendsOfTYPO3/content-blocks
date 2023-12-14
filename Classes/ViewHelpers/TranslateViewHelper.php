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

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Localization\Locale;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use TYPO3Fluid\Fluid\Exception;

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
        $this->registerArgument('languageKey', 'string', 'Language key ("da" for example) or "default" to use. If empty, use current language.');
    }

    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext): string
    {
        $name = (string)($arguments['name'] ?? $renderingContext->getVariableProvider()->get('data._name'));
        if ($name === '') {
            throw new Exception(__CLASS__ . ' seemingly called outside Content Blocks context.', 1699271759);
        }

        $key = (string)$arguments['key'];
        if ($key === '') {
            throw new Exception('Value for "key" must not be empty.', 1699271873);
        }

        $default = (string)($arguments['default'] ?? $renderChildrenClosure() ?? '');
        $translateArguments = $arguments['arguments'];

        $contentBlockRegistry = GeneralUtility::makeInstance(ContentBlockRegistry::class);
        if (!$contentBlockRegistry->hasContentBlock($name)) {
            throw new Exception('Content block with the name "' . $name . '" is not registered.', 1699272189);
        }
        $languagePath = 'LLL:' . $contentBlockRegistry->getContentBlockExtPath($name) . '/' . ContentBlockPathUtility::getLanguageFilePath() . ':' . $key;

        $request = null;
        if ($renderingContext instanceof RenderingContext) {
            $request = $renderingContext->getRequest();
        }
        $value = self::getLanguageService($request, $arguments['languageKey'])->sL($languagePath);
        if (empty($value)) {
            // In case $value is empty (LLL: could not be resolved) fall back to the default.
            $value = $default;
        }
        if (!empty($translateArguments)) {
            $value = vsprintf($value, $translateArguments);
        }
        return $value;
    }

    /**
     * @todo Adapt latest changes in Core TranslateViewHelper
     */
    protected static function getLanguageService(ServerRequestInterface $request = null, string|Locale $languageKey = null): LanguageService
    {
        $languageServiceFactory = GeneralUtility::makeInstance(LanguageServiceFactory::class);
        if ($languageKey) {
            return $languageServiceFactory->create($languageKey);
        }
        if ($request !== null && ApplicationType::fromRequest($request)->isFrontend()) {
            return $languageServiceFactory->createFromSiteLanguage($request->getAttribute('language')
                ?? $request->getAttribute('site')->getDefaultLanguage());
        }
        return $languageServiceFactory->createFromUserPreferences($GLOBALS['BE_USER'] ?? null);
    }
}
