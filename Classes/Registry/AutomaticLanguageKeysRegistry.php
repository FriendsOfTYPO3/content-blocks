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

/**
 * @internal Not part of TYPO3's public API.
 */
final class AutomaticLanguageKeysRegistry
{
    /**
     * @var array<string, AutomaticLanguageSource[]>
     */
    protected array $flatLanguageKeyListByContentBlock = [];

    public function addKey(LoadedContentBlock $contentBlock, AutomaticLanguageSource $automaticLanguageSource): void
    {
        // Value is already a translation, skip.
        if (str_starts_with($automaticLanguageSource->value, 'LLL:EXT:')) {
            return;
        }
        $extractedKey = LocalLangPathUtility::extractKeyFromLLLPath($automaticLanguageSource->key);
        $newAutomaticLanguageSource = new AutomaticLanguageSource($extractedKey, $automaticLanguageSource->value);
        $this->flatLanguageKeyListByContentBlock[$contentBlock->getName()][] = $newAutomaticLanguageSource;
    }

    /**
     * @return AutomaticLanguageSource[]
     */
    public function getByContentBlock(LoadedContentBlock $contentBlock): array
    {
        return $this->flatLanguageKeyListByContentBlock[$contentBlock->getName()];
    }
}
