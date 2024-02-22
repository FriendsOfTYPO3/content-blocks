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
trait WithCustomProperties
{
    private array $allowedCustomProperties = ['itemsProcConfig'];
    private array $customProperties = [];

    protected function setCustomProperties(array $settings): void
    {
        foreach ($this->allowedCustomProperties as $customProperty) {
            if (array_key_exists($customProperty, $settings)) {
                $this->customProperties[$customProperty] = $settings[$customProperty];
            }
        }
    }

    protected function mergeCustomProperties(array $config = []): array
    {
        $config = array_merge($config, $this->customProperties);
        return $config;
    }
}
