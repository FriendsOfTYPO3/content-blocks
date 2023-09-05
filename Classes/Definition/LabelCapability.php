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

final class LabelCapability
{
    /**
     * @var string|string[]
     */
    private string|array $useAsLabel = '';
    private array $fallbackLabelFields = [];

    public static function createFromArray(array $definition): LabelCapability
    {
        $self = new self();
        $useAsLabel = $definition['useAsLabel'] ?? null;
        if (
            isset($useAsLabel)
            && (
                is_array($useAsLabel) || is_string($useAsLabel)
            )
        ) {
            $self->useAsLabel = $useAsLabel;
        }
        $self->fallbackLabelFields = $definition['fallbackLabelFields'] ?? $self->fallbackLabelFields;
        return $self;
    }

    public function hasUseAsLabel(): bool
    {
        return $this->useAsLabel !== '';
    }

    public function getPrimaryLabelField(): string
    {
        if (is_array($this->useAsLabel)) {
            return $this->useAsLabel[0];
        }
        return $this->useAsLabel;
    }

    public function getLabelFieldsAsArray(): array
    {
        if (is_array($this->useAsLabel)) {
            return $this->useAsLabel;
        }
        $primaryField = $this->getPrimaryLabelField();
        return [$primaryField];
    }

    public function hasAdditionalLabelFields(): bool
    {
        return is_array($this->useAsLabel) && count($this->useAsLabel) > 1;
    }

    public function getAdditionalLabelFieldsAsString(): string
    {
        if (!$this->hasAdditionalLabelFields()) {
            return '';
        }
        $additionalLabelFields = array_slice($this->useAsLabel, 1);
        $additionalLabelFieldsAsString = implode(',', $additionalLabelFields);
        return $additionalLabelFieldsAsString;
    }

    public function hasFallbackLabelFields(): bool
    {
        if ($this->hasAdditionalLabelFields()) {
            return false;
        }
        return $this->fallbackLabelFields !== [];
    }

    public function getFallbackLabelFieldsAsString(): string
    {
        if (!$this->hasFallbackLabelFields()) {
            return '';
        }
        $fallbackLabelFieldsAsString = implode(',', $this->fallbackLabelFields);
        return $fallbackLabelFieldsAsString;
    }

    public function getFallbackLabelFields(): array
    {
        return $this->fallbackLabelFields;
    }
}
