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

/**
 * @internal Not part of TYPO3's public API.
 */
final class ParsedContentBlock
{
    public function __construct(
        private readonly array $composerJson,
        private readonly array $yaml,
        private readonly string $icon,
        private readonly string $iconProvider,
        private readonly string $packagePath,
    ) {
    }

    public static function fromArray(array $array): ParsedContentBlock
    {
        return new self(
            composerJson: (array)($array['composerJson'] ?? []),
            yaml: (array)($array['yaml'] ?? []),
            icon: (string)($array['icon'] ?? ''),
            iconProvider: (string)($array['iconProvider'] ?? ''),
            packagePath: (string)($array['packagePath'] ?? '')
        );
    }

    public function toArray(): array
    {
        return [
            'composerJson' => $this->composerJson,
            'yaml' => $this->yaml,
            'icon' => $this->icon,
            'iconProvider' => $this->iconProvider,
            'packagePath' => $this->packagePath,
        ];
    }

    public function getComposerJson(): array
    {
        return $this->composerJson;
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

    public function getPackagePath(): string
    {
        return $this->packagePath;
    }
}
