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
final class PageTypeDefinition extends ContentTypeDefinition implements ContentTypeInterface
{
    private PageIconSet $pageIconSet;

    public static function createFromArray(array $array, string $table): PageTypeDefinition
    {
        $self = new self();
        $iconHideInMenu = ContentTypeIcon::fromArray($array['typeIconHideInMenu'] ?? []);
        $iconRoot = ContentTypeIcon::fromArray($array['typeIconRoot'] ?? []);
        $pageIconSet = new PageIconSet($iconHideInMenu, $iconRoot);
        return $self
            ->withTable($table)
            ->withIdentifier($array['identifier'])
            ->withTitle($array['title'])
            ->withDescription($array['description'])
            ->withTypeName($array['typeName'])
            ->withColumns($array['columns'] ?? [])
            ->withShowItems($array['showItems'] ?? [])
            ->withOverrideColumns($array['overrideColumns'] ?? [])
            ->withVendor($array['vendor'] ?? '')
            ->withPackage($array['package'] ?? '')
            ->withTypeIcon(ContentTypeIcon::fromArray($array['typeIcon'] ?? []))
            ->withPageIconSet($pageIconSet)
            ->withPriority($array['priority'] ?? 0)
            ->withLanguagePathTitle($array['languagePathTitle'] ?? null)
            ->withLanguagePathDescription($array['languagePathDescription'] ?? null)
            ->withGroup($array['group']);
    }

    public function withPageIconSet(PageIconSet $pageIconSet): self
    {
        $clone = clone $this;
        $clone->pageIconSet = $pageIconSet;
        return $clone;
    }

    public function getPageIconSet(): PageIconSet
    {
        return $this->pageIconSet;
    }
}
