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

use TYPO3\CMS\ContentBlocks\Backend\Preview\PreviewRenderer;

final class ContentElementDefinition extends TypeDefinition
{
    private string $description = '';
    private string $contentElementIcon = '';
    private string $contentElementIconOverlay = '';
    private bool $saveAndClose = false;
    private string $composerName = '';
    private string $vendor = '';
    private string $package = '';
    private string $publicPath = '';
    private string $privatePath = '';

    public static function createFromArray(array $array, string $table = 'tt_content'): ContentElementDefinition
    {

        // 'vendor' => $vendor,
        // 'package' => $package,
        // 'publicPath' => $path . $configurationService->getContentBlocksPublicPath() . DIRECTORY_SEPARATOR,
        // 'privatePath' => $path . $configurationService->getContentBlocksPublicPath() . DIRECTORY_SEPARATOR,
        $array['typeField'] = 'CType';
        $self = self::fromArray($array, $table);
        return $self
            ->withDescription($array['description'] ?? '')
            ->withContentElementIcon($array['contentElementIcon'] ?? '')
            ->withContentElementIconOverlay($array['contentElementIconOverlay'] ?? '')
            ->withSaveAndClose(!empty($array['saveAndClose']))
            ->withComposerName($array['composerName'] ?? '')
            ->withVendor($array['vendor'] ?? '')
            ->withPackage($array['package'] ?? '')
            ->withPublicPath($array['publicPath'] ?? '')
            ->withPrivatePath($array['privatePath'] ?? '');
    }

    public function toArray(): array
    {
        $array = parent::toArray();
        $array += [
            'description' => $this->description,
            'contentElementIcon' => $this->contentElementIcon,
            'contentElementIconOverlay' => $this->contentElementIconOverlay,
            'saveAndClose' => $this->saveAndClose,
            'vendor' => $this->vendor,
            'package' => $this->package,
            'publicPath' => $this->publicPath,
            'privatePath' => $this->privatePath,
        ];
        return $array;
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

    public function getCType(): string
    {
        return 'cb_' . str_replace('/', '-', $this->composerName);
    }

    public function getVendor(): string
    {
        return $this->vendor;
    }

    public function getPackage(): string
    {
        return $this->package;
    }

    public function getPublicPath(): string
    {
        return $this->publicPath;
    }

    public function getPrivatePath(): string
    {
        return $this->privatePath;
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

    public function withComposerName(string $composerName): self
    {
        $clone = clone $this;
        $clone->composerName = $composerName;
        return $clone;
    }

    public function withVendor(string $vendor): self
    {
        $clone = clone $this;
        $clone->vendor = $vendor;
        return $clone;
    }

    public function withPackage(string $package): self
    {
        $clone = clone $this;
        $clone->package = $package;
        return $clone;
    }

    public function withPublicPath(string $publicPath): self
    {
        $clone = clone $this;
        $clone->publicPath = $publicPath;
        return $clone;
    }

    public function withPrivatePath(string $privatePath): self
    {
        $clone = clone $this;
        $clone->privatePath = $privatePath;
        return $clone;
    }
}
