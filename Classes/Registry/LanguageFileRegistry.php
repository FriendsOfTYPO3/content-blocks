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

use Symfony\Component\Translation\MessageCatalogue;
use TYPO3\CMS\ContentBlocks\Loader\LoadedContentBlock;
use TYPO3\CMS\ContentBlocks\Utility\LocalLangPathUtility;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * @internal Not part of TYPO3's public API.
 */
class LanguageFileRegistry implements SingletonInterface
{
    /**
     * @var array<string, MessageCatalogue>
     */
    protected array $parsedLanguageFiles = [];

    public function register(LoadedContentBlock $contentBlock, MessageCatalogue $messageCatalogue): void
    {
        $this->parsedLanguageFiles[$contentBlock->getName()] = $messageCatalogue;
    }

    public function isset(string $name, string $key): bool
    {
        $key = LocalLangPathUtility::extractKeyFromLLLPath($key);
        if ($this->hasLanguageFile($name) === false) {
            return false;
        }
        return $this->parsedLanguageFiles[$name]->has($key);
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
        return $this->parsedLanguageFiles[$name]->get($key);
    }

    /**
     * @return string[]
     */
    public function getAllRegisteredKeys(string $name): array
    {
        if (!$this->hasLanguageFile($name)) {
            return [];
        }
        $allTranslations = $this->getAllTranslations($name);
        $allKeys = array_keys($allTranslations);
        return $allKeys;
    }

    public function getAllLanguageFiles(): array
    {
        return $this->parsedLanguageFiles;
    }

    public function getAllTranslations(string $name): array
    {
        if ($this->hasLanguageFile($name) === false) {
            return [];
        }
        return $this->parsedLanguageFiles[$name]->all('messages');
    }

    public function hasLanguageFile(string $name): bool
    {
        return array_key_exists($name, $this->parsedLanguageFiles);
    }
}
