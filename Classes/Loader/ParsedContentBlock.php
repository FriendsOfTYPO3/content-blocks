<?php

declare(strict_types=1);

namespace TYPO3\CMS\ContentBlocks\Loader;

final class ParsedContentBlock
{
    public function __construct(
        private readonly array $composerJson,
        private readonly array $yaml,
        private readonly string $icon,
        private readonly string $iconProvider,
    ) {
    }

    public static function fromArray(array $array): ParsedContentBlock
    {
        return new self(
            composerJson: (array)($array['composerJson'] ?? []),
            yaml: (array)($array['yaml'] ?? []),
            icon: (string)($array['icon'] ?? ''),
            iconProvider: (string)($array['iconProvider'] ?? ''),
        );
    }

    public function toArray(): array
    {
        return [
            'composerJson' => $this->composerJson,
            'yaml' => $this->yaml,
            'icon' => $this->icon,
            'iconProvider' => $this->iconProvider,
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
}
