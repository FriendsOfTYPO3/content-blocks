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

namespace TYPO3\CMS\ContentBlocks\Definition\ContentType;

/**
 * @internal Not part of TYPO3's public API.
 */
final class ContentElementDefinition extends ContentTypeDefinition implements ContentTypeInterface
{
    // @todo implement saveAndClose.
    private bool $saveAndClose = false;
    private string $group = '';

    public static function createFromArray(array $array, string $table): ContentElementDefinition
    {
        $self = new self();
        return $self
            ->withTable($table)
            ->withIdentifier($array['identifier'])
            ->withTypeName($array['typeName'])
            ->withColumns($array['columns'] ?? [])
            ->withShowItems($array['showItems'] ?? [])
            ->withOverrideColumns($array['overrideColumns'] ?? [])
            ->withVendor($array['vendor'] ?? '')
            ->withPackage($array['package'] ?? '')
            ->withPriority($array['priority'] ?? 0)
            ->withSaveAndClose(!empty($array['saveAndClose']))
            ->withGroup($array['group'])
            ->withTypeIconPath($array['typeIconPath'] ?? null)
            ->withIconProviderClassName($array['iconProvider'] ?? null)
            ->withTypeIconIdentifier($array['typeIconIdentifier'] ?? null)
            ->withLanguagePathTitle($array['languagePathTitle'] ?? null)
            ->withLanguagePathDescription($array['languagePathDescription'] ?? null);
    }

    public function getGroup(): string
    {
        return $this->group;
    }

    public function hasSaveAndClose(): bool
    {
        return $this->saveAndClose;
    }

    public function withSaveAndClose(bool $saveAndClose): self
    {
        $clone = clone $this;
        $clone->saveAndClose = $saveAndClose;
        return $clone;
    }

    public function withGroup(string $group): self
    {
        $clone = clone $this;
        $clone->group = $group;
        return $clone;
    }
}
