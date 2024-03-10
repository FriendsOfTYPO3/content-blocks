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

namespace TYPO3\CMS\ContentBlocks\FieldType;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * @internal Not part of TYPO3's public API.
 */
#[AutoconfigureTag('content_blocks.field_type')]
interface FieldTypeInterface
{
    public static function createFromArray(array $settings): FieldTypeInterface;
    public function getTca(): array;
    public function getSql(string $uniqueColumnName): string;
    public static function getName(): string;
    public static function getTcaType(): string;
    public static function isSearchable(): bool;
    public static function isRenderable(): bool;
    public static function isRelation(): bool;
    public static function hasItems(): bool;
}
