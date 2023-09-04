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

namespace TYPO3\CMS\ContentBlocks\Definition;

/**
 * @internal Not part of TYPO3's public API.
 */
final class TableDefinitionCapability
{
    private bool $languageAware = true;
    private bool $workspaceAware = true;
    private bool $ancestorReferenceField = true;

    public static function createFromArray(array $definition): TableDefinitionCapability
    {
        $capability = new TableDefinitionCapability();
        $capability->languageAware = $definition['languageAware'] ?? $capability->languageAware;
        $capability->workspaceAware = $definition['workspaceAware'] ?? $capability->workspaceAware;
        $capability->ancestorReferenceField = $definition['ancestorReferenceField'] ?? $capability->ancestorReferenceField;
        return $capability;
    }

    public function isLanguageAware(): bool
    {
        return $this->languageAware;
    }

    public function isWorkspaceAware(): bool
    {
        return $this->workspaceAware;
    }

    public function hasAncestorReferenceField(): bool
    {
        return $this->ancestorReferenceField;
    }
}
