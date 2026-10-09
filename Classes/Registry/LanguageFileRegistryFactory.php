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

namespace TYPO3\CMS\ContentBlocks\Registry;

use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;
use TYPO3\CMS\ContentBlocks\Loader\LoadedContentBlock;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\Core\Localization\Exception\FileNotFoundException;
use TYPO3\CMS\Core\Localization\LabelFileResolver;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @internal Not part of TYPO3's public API.
 */
readonly class LanguageFileRegistryFactory
{
    public function __construct(
        protected ContentBlockRegistry $contentBlockRegistry,
        protected LabelFileResolver $labelFileResolver,
    ) {}

    public function create(): LanguageFileRegistry
    {
        $languageFileRegistry = new LanguageFileRegistry();
        foreach ($this->contentBlockRegistry->getAll() as $contentBlock) {
            $messageCatalogue = $this->parseDefaultLanguageFile($contentBlock);
            if ($messageCatalogue !== null) {
                $languageFileRegistry->register($contentBlock, $messageCatalogue);
            }
        }
        return $languageFileRegistry;
    }

    protected function parseDefaultLanguageFile(LoadedContentBlock $contentBlock): ?MessageCatalogue
    {
        $languagePath = $contentBlock->getExtPath() . '/' . ContentBlockPathUtility::getLanguageFilePath();
        try {
            $absoluteLanguagePath = $this->labelFileResolver->resolveFileReference($languagePath, 'default');
        } catch (FileNotFoundException) {
            return null;
        }
        if ($absoluteLanguagePath === null) {
            return null;
        }
        $extension = pathinfo($absoluteLanguagePath, PATHINFO_EXTENSION);
        $loader = $this->getLoader($extension);
        return $loader?->load($absoluteLanguagePath, 'en');
    }

    protected function getLoader(string $extension): ?LoaderInterface
    {
        $loaderClass = $GLOBALS['TYPO3_CONF_VARS']['LANG']['loader'][$extension] ?? null;
        if (!is_string($loaderClass) || !is_a($loaderClass, LoaderInterface::class, true)) {
            return null;
        }
        $loader = GeneralUtility::makeInstance($loaderClass);
        return $loader instanceof LoaderInterface ? $loader : null;
    }
}
