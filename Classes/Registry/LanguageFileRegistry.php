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

use TYPO3\CMS\ContentBlocks\Definition\TypeDefinition;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\Core\Localization\Parser\XliffParser;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class LanguageFileRegistry implements LanguageFileRegistryInterface
{
    protected array $parsedLanguageFiles = [];

    public function __construct(
        protected readonly XliffParser $xliffParser,
        protected readonly ContentBlockRegistry $contentBlockRegistry,
    ) {
    }

    public function register(TypeDefinition $typeDefinition): void
    {
        if (!array_key_exists($typeDefinition->getName(), $this->parsedLanguageFiles)) {
            $languagePath = $this->contentBlockRegistry->getContentBlockPath($typeDefinition->getName()) . '/' . ContentBlockPathUtility::getLanguageFilePath();
            $absoluteLanguagePath = GeneralUtility::getFileAbsFileName($languagePath);
            if (file_exists($absoluteLanguagePath)) {
                $this->parsedLanguageFiles[$typeDefinition->getName()] = $this->xliffParser->getParsedData($absoluteLanguagePath, 'default');
            }
        }
    }

    public function isset(TypeDefinition $typeDefinition, string $key): bool
    {
        return isset($this->parsedLanguageFiles[$typeDefinition->getName()]['default'][$key]);
    }
}
