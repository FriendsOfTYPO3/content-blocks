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
use TYPO3\CMS\ContentBlocks\Utility\LocalLangPathUtility;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * @internal Not part of TYPO3's public API.
 */
class LanguageFileRegistry implements SingletonInterface
{
    protected array $parsedLanguageFiles = [];

    public function register(LoadedContentBlock $contentBlock, array $defaultData): void
    {
        $this->parsedLanguageFiles[$contentBlock->getName()] = $defaultData;
    }

    public function isset(string $name, string $key): bool
    {
        $key = LocalLangPathUtility::extractKeyFromLLLPath($key);
        return isset($this->parsedLanguageFiles[$name][$key][0]['source']);
    }

    public function get(string $name, string $key): string
    {
        $key = LocalLangPathUtility::extractKeyFromLLLPath($key);
        if (!$this->isset($name, $key)) {
            throw new \InvalidArgumentException(
                'Language key ' . $key . ' does not exist for Content Block "' . $name . '".',
                1701533837,
            );
        }
        return $this->parsedLanguageFiles[$name][$key][0]['source'];
    }

    /**
     * @return string[]
     */
    public function getAllRegisteredKeys(string $name): array
    {
        if (!$this->hasLanguageFile($name)) {
            return [];
        }
        $languageFile = $this->getLanguageFile($name);
        $allKeys = array_keys($languageFile);
        return $allKeys;
    }

    public function getAllLanguageFiles(): array
    {
        return $this->parsedLanguageFiles;
    }

    public function getLanguageFile(string $name): array
    {
        return $this->parsedLanguageFiles[$name];
    }

    public function hasLanguageFile(string $name): bool
    {
        return isset($this->parsedLanguageFiles[$name]);
    }
}
