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
 * Defines basic stuff for FieldConfigurations
 */
interface FieldConfigurationInterface
{
    public function getSql(string $uniqueColumnName): string;

    public function getTca(array $contentBlock): array;

    public function toArray(): array;

    public function getTemplateHtml(string $indentation): string;

    /** TODO:
     *  - getAllowedSettings: allowed properties and values for the backend module
     *  - (?) validateProperties: removes all not allowed properties from an array
     *  - (?) getXlfTemplate
     */
}
