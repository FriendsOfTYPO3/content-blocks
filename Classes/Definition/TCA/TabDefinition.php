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

namespace TYPO3\CMS\ContentBlocks\Definition\TCA;

/**
 * @internal Not part of TYPO3's public API.
 */
final class TabDefinition
{
    private string $identifier = '';
    private string $contentBlockName = '';
    private string $label = '';
    private string $languagePathLabel = '';

    public static function createFromArray(array $array): TabDefinition
    {
        if (($array['identifier'] ?? '') === '') {
            throw new \InvalidArgumentException('Tab identifier must not be empty.', 1700344278);
        }
        $self = new self();
        $self->identifier = (string)$array['identifier'];
        $self->contentBlockName = (string)($array['contentBlockName'] ?? '');
        $self->label = (string)($array['label'] ?? '');
        $self->languagePathLabel = (string)($array['languagePathLabel'] ?? '');
        return $self;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getContentBlockName(): string
    {
        return $this->contentBlockName;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function hasLabel(): bool
    {
        return $this->label !== '';
    }

    public function getLanguagePathLabel(): string
    {
        return $this->languagePathLabel;
    }
}
