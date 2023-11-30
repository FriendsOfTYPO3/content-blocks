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

namespace TYPO3\CMS\ContentBlocks\Definition\Factory\Processing;

use TYPO3\CMS\ContentBlocks\Definition\PaletteDefinition;
use TYPO3\CMS\ContentBlocks\Definition\TCA\TabDefinition;
use TYPO3\CMS\ContentBlocks\Loader\LoadedContentBlock;

/**
 * @internal Not part of TYPO3's public API.
 */
final class ProcessedContentType
{
    public string $table = '';
    public LoadedContentBlock $contentBlock;
    public array $columns = [];
    /** @var array<string|PaletteDefinition|TabDefinition> */
    public array $showItems = [];
    public array $overrideColumns = [];
    public string|int $typeName = '';
    public string $languagePathTitle = '';
    public string $languagePathDescription = '';
}
