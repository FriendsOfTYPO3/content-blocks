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

/**
 * @internal Not part of TYPO3's public API.
 */
final class LoadedContentBlock
{
    public function __construct(
        private readonly string $name,
        private readonly array $yaml,
        private readonly string $icon,
        private readonly string $iconProvider,
        private readonly string $path,
        private readonly ContentType $contentType,
    ) {}

    public static function fromArray(array $array): LoadedContentBlock
    {
        $table = (string)($array['yaml']['table'] ?? '');
        if ($table === '') {
            throw new \InvalidArgumentException('Failed to load Content Block: Missing "table".', 1689198195);
        }
        return new self(
            name: (string)($array['name'] ?? ''),
            yaml: (array)($array['yaml'] ?? []),
            icon: (string)($array['icon'] ?? ''),
            iconProvider: (string)($array['iconProvider'] ?? ''),
            path: (string)($array['path'] ?? ''),
            contentType: ContentType::getByTable($table)
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'yaml' => $this->yaml,
            'icon' => $this->icon,
            'iconProvider' => $this->iconProvider,
            'path' => $this->path,
        ];
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getYaml(): array
    {
        return $this->yaml;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function getIconProvider(): string
    {
        return $this->iconProvider;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function prefixFields(): bool
    {
        return (bool)($this->yaml['prefixFields'] ?? true);
    }

    public function getContentType(): ContentType
    {
        return $this->contentType;
    }
}
