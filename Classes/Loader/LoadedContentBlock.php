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

namespace TYPO3\CMS\ContentBlocks\Loader;

use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentTypeIcon;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\PageIconSet;
use TYPO3\CMS\ContentBlocks\Definition\Factory\PrefixType;

/**
 * @internal Not part of TYPO3's public API.
 */
final class LoadedContentBlock
{
    public function __construct(
        private readonly string $name,
        private readonly array $yaml,
        private readonly ContentTypeIcon $icon,
        private readonly string $hostExtension,
        private readonly string $extPath,
        private readonly ContentType $contentType,
        private readonly ?PageIconSet $pageIconSet = null,
    ) {}

    public static function fromArray(array $array): LoadedContentBlock
    {
        $table = (string)($array['yaml']['table'] ?? '');
        if ($table === '') {
            throw new \InvalidArgumentException('Failed to load Content Block: Missing "table".', 1689198195);
        }
        $contentType = ContentType::getByTable($table);
        $pageIconSet = null;
        if ($contentType === ContentType::PAGE_TYPE) {
            $IconHideInMenu = ContentTypeIcon::fromArray($array['iconHideInMenu'] ?? []);
            $iconRoot = ContentTypeIcon::fromArray($array['iconRoot'] ?? []);
            $pageIconSet = new PageIconSet($IconHideInMenu, $iconRoot);
        }
        return new self(
            name: (string)($array['name'] ?? ''),
            yaml: (array)($array['yaml'] ?? []),
            icon: ContentTypeIcon::fromArray($array['icon'] ?? []),
            hostExtension: (string)($array['hostExtension'] ?? ''),
            extPath: (string)($array['extPath'] ?? ''),
            contentType: ContentType::getByTable($table),
            pageIconSet: $pageIconSet
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'yaml' => $this->yaml,
            'icon' => $this->icon->toArray(),
            'iconHideInMenu' => $this->pageIconSet?->iconHideInMenu->toArray(),
            'iconRoot' => $this->pageIconSet?->iconRoot->toArray(),
            'hostExtension' => $this->hostExtension,
            'extPath' => $this->extPath,
        ];
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getVendor(): string
    {
        return explode('/', $this->name)[0];
    }

    public function getPackage(): string
    {
        return explode('/', $this->name)[1];
    }

    public function getYaml(): array
    {
        return $this->yaml;
    }

    public function getIcon(): ContentTypeIcon
    {
        return $this->icon;
    }

    public function getPageIconSet(): ?PageIconSet
    {
        return $this->pageIconSet;
    }

    public function getHostExtension(): string
    {
        return $this->hostExtension;
    }

    public function getExtPath(): string
    {
        return $this->extPath;
    }

    public function prefixFields(): bool
    {
        return (bool)($this->yaml['prefixFields'] ?? true);
    }

    public function getPrefixType(): PrefixType
    {
        if (array_key_exists('prefixType', $this->yaml)) {
            return PrefixType::from($this->yaml['prefixType']);
        }
        return PrefixType::FULL;
    }

    public function getContentType(): ContentType
    {
        return $this->contentType;
    }
}
