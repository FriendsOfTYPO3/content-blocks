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

final class ContentElementDefinition extends TypeDefinition
{
    private string $description = '';
    private string $contentElementIcon = '';
    private string $contentElementIconOverlay = '';
    private bool $saveAndClose = false;
    private string $wizardGroup = '';
    private string $wizardIconPath = '';

    public static function createFromArray(array $array, string $table = 'tt_content'): static
    {
        $self = parent::createFromArray($array, $table);
        return $self
            ->withDescription($array['description'] ?? '')
            ->withContentElementIcon($array['contentElementIcon'] ?? '')
            ->withContentElementIconOverlay($array['contentElementIconOverlay'] ?? '')
            ->withSaveAndClose(!empty($array['saveAndClose']))
            ->withWizardGroup($array['wizardGroup'] ?? 'common')
            ->withWizardIconPath($array['icon'] ?? '');
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getContentElementIcon(): string
    {
        return $this->contentElementIcon;
    }

    public function getContentElementIconOverlay(): string
    {
        return $this->contentElementIconOverlay;
    }

    public function getWizardGroup(): string
    {
        return $this->wizardGroup;
    }

    public function getWizardIconPath(): string
    {
        return $this->wizardIconPath;
    }

    public function getWizardIconIdentifier(): string
    {
        return $this->type . '-icon';
    }

    public function hasSaveAndClose(): bool
    {
        return $this->saveAndClose;
    }

    public function withDescription(string $description): self
    {
        $clone = clone $this;
        $clone->description = $description;
        return $clone;
    }

    public function withContentElementIcon(string $contentElementIcon): self
    {
        $clone = clone $this;
        $clone->contentElementIcon = $contentElementIcon;
        return $clone;
    }

    public function withContentElementIconOverlay(string $contentElementIconOverlay): self
    {
        $clone = clone $this;
        $clone->contentElementIconOverlay = $contentElementIconOverlay;
        return $clone;
    }

    public function withSaveAndClose(bool $saveAndClose): self
    {
        $clone = clone $this;
        $clone->saveAndClose = $saveAndClose;
        return $clone;
    }

    public function withWizardGroup(string $wizardGroup): self
    {
        $clone = clone $this;
        $clone->wizardGroup = $wizardGroup;
        return $clone;
    }

    public function withWizardIconPath(string $icon): self
    {
        $clone = clone $this;
        $clone->wizardIconPath = $icon;
        return $clone;
    }
}
