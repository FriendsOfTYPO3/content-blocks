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

namespace TYPO3\CMS\ContentBlocks\Definition\ContentType;

use TYPO3\CMS\ContentBlocks\Definition\TcaFieldDefinition;

/**
 * @internal Not part of TYPO3's public API.
 */
interface ContentTypeInterface
{
    public static function createFromArray(array $array, string $table): ContentTypeInterface;
    public function getName(): string;
    public function getVendor(): string;
    public function getPackage(): string;
    public function getTypeName(): string|int;
    public function getIdentifier(): string;
    public function getTitle(): string;
    public function getDescription(): string;
    public function getPriority(): int;
    public function getTable(): string;
    /** @return TcaFieldDefinition[] */
    public function getOverrideColumns(): array;
    public function getShowItems(): array;
    public function hasColumn(string $column): bool;
    public function getColumns(): array;
    public function getTypeIconIdentifier(): ?string;
    public function getIconProviderClassName(): string;
    public function getTypeIconPath(): string;
    public function getLanguagePathTitle(): string;
    public function getLanguagePathDescription(): string;
}
