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

namespace TYPO3\CMS\ContentBlocks\FieldConfiguration;

/**
 * @internal Not part of TYPO3's public API.
 */
trait WithCommonProperties
{
    private ?string $label = null;
    private ?string $description = null;
    private null|string|array $displayCond = null;
    private string $l10n_display = '';
    private string $l10n_mode = '';
    private string $onChange = '';
    private bool $exclude = true;
    private array $fieldWizard = [];
    private array $fieldControl = [];
    private array $fieldInformation = [];
    private string $renderType = '';

    protected function setCommonProperties(array $settings): void
    {
        if (isset($settings['label'])) {
            $this->label = (string)$settings['label'];
        }
        if (isset($settings['description'])) {
            $this->description = (string)$settings['description'];
        }
        $displayCond = $settings['displayCond'] ?? null;
        if (isset($displayCond) && (is_string($displayCond) || is_array($displayCond))) {
            $this->displayCond = $displayCond;
        }
        $this->l10n_display = (string)($settings['l10n_display'] ?? $this->l10n_display);
        $this->l10n_mode = (string)($settings['l10n_mode'] ?? $this->l10n_mode);
        $this->onChange = (string)($settings['onChange'] ?? $this->onChange);
        $this->exclude = (bool)($settings['exclude'] ?? $this->exclude);
        $this->fieldWizard = (array)($settings['fieldWizard'] ?? $this->fieldWizard);
        $this->fieldControl = (array)($settings['fieldControl'] ?? $this->fieldControl);
        $this->fieldInformation = (array)($settings['fieldInformation'] ?? $this->fieldInformation);
        $this->renderType = (string)($settings['renderType'] ?? $this->renderType);
    }

    protected function toTca(array $tca = []): array
    {
        if ($this->label !== null && $this->label !== '') {
            $tca['label'] = $this->label;
        }
        if ($this->description !== null) {
            $tca['description'] = $this->description;
        }
        if ($this->displayCond !== null && $this->displayCond !== '' && $this->displayCond !== []) {
            $tca['displayCond'] = $this->displayCond;
        }
        if ($this->l10n_display !== '') {
            $tca['l10n_display'] = $this->l10n_display;
        }
        if ($this->l10n_mode !== '') {
            $tca['l10n_mode'] = $this->l10n_mode;
        }
        if ($this->onChange !== '') {
            $tca['onChange'] = $this->onChange;
        }
        if ($this->exclude) {
            $tca['exclude'] = $this->exclude;
        }
        if ($this->fieldWizard !== []) {
            $tca['config']['fieldWizard'] = $this->fieldWizard;
        }
        if ($this->fieldControl !== []) {
            $tca['config']['fieldControl'] = $this->fieldControl;
        }
        if ($this->fieldInformation !== []) {
            $tca['config']['fieldInformation'] = $this->fieldInformation;
        }
        if ($this->renderType !== '') {
            $tca['config']['renderType'] = $this->renderType;
        }
        return $tca;
    }
}
