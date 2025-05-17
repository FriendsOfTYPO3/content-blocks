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

use TYPO3\CMS\ContentBlocks\Definition\PaletteDefinition;
use TYPO3\CMS\ContentBlocks\Definition\TCA\TabDefinition;
use TYPO3\CMS\ContentBlocks\Definition\TcaFieldDefinition;

/**
 * @internal Not part of TYPO3's public API.
 */
final readonly class ContentElementDefinition implements ContentTypeInterface
{
    use ContentTypeDefinition;

    public function __construct(
        public string $table,
        public string $identifier,
        public string $title,
        public string $description,
        public string|int $typeName,
        /** @var string[] */
        public array $columns,
        /** @var array<string|PaletteDefinition|TabDefinition> */
        public array $showItems,
        /** @var TcaFieldDefinition[] */
        public array $overrideColumns,
        public string $vendor,
        public string $package,
        public int $priority,
        public ContentTypeIcon $typeIcon,
        public string $languagePathTitle,
        public string $languagePathDescription,
        public ?string $group,
        public bool $saveAndClose,
    ) {}

    public function hasSaveAndClose(): bool
    {
        return $this->saveAndClose;
    }
}
