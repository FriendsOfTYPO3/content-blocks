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

use TYPO3\CMS\ContentBlocks\Loader\LoadedContentBlock;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\Core\Localization\Parser\XliffParser;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @internal Not part of TYPO3's public API.
 */
class LanguageFileRegistry implements LanguageFileRegistryInterface
{
    protected array $parsedLanguageFiles = [];

    public function __construct(
        protected readonly XliffParser $xliffParser,
    ) {
    }

    public function register(LoadedContentBlock $contentBlock): void
    {
        if (!array_key_exists($contentBlock->getName(), $this->parsedLanguageFiles)) {
            $languagePath = $contentBlock->getPath() . '/' . ContentBlockPathUtility::getLanguageFilePath();
            $absoluteLanguagePath = GeneralUtility::getFileAbsFileName($languagePath);
            if (file_exists($absoluteLanguagePath)) {
                $this->parsedLanguageFiles[$contentBlock->getName()] = $this->xliffParser->getParsedData($absoluteLanguagePath, 'default');
            }
        }
    }

    public function isset(string $name, string $key): bool
    {
        return isset($this->parsedLanguageFiles[$name]['default'][$key]);
    }
}
