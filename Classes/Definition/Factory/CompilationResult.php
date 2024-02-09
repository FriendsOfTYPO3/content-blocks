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

namespace TYPO3\CMS\ContentBlocks\Definition\Factory;

use TYPO3\CMS\ContentBlocks\Registry\AutomaticLanguageKeysRegistry;

final class CompilationResult
{
    public function __construct(
        private readonly AutomaticLanguageKeysRegistry $automaticLanguageKeys,
        /**
         * @var array<string, array>
         */
        private readonly array $parentReferences,
        private readonly array $mergedTableDefinitions,
    ) {}

    public function getAutomaticLanguageKeys(): AutomaticLanguageKeysRegistry
    {
        return $this->automaticLanguageKeys;
    }

    public function getParentReferences(): array
    {
        return $this->parentReferences;
    }

    public function getMergedTableDefinitions(): array
    {
        return $this->mergedTableDefinitions;
    }
}
