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

use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;
use TYPO3\CMS\ContentBlocks\Definition\PaletteDefinition;
use TYPO3\CMS\ContentBlocks\Definition\TCA\TabDefinition;
use TYPO3\CMS\ContentBlocks\Loader\LoadedContentBlock;
use TYPO3\CMS\ContentBlocks\Service\ContentTypeIconResolver;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @see ContentTypeDefinition
 * @internal Not part of TYPO3's public API.
 */
final class ProcessedContentType
{
    public string $title = '';
    public string $description = '';
    public string $table = '';
    public LoadedContentBlock $contentBlock;
    public array $columns = [];
    /** @var array<string|PaletteDefinition|TabDefinition> */
    public array $showItems = [];
    public array $overrideColumns = [];
    public string|int $typeName = '';
    public string $languagePathTitle = '';
    public string $languagePathDescription = '';

    public function toArray(bool $isRootTable, string $identifier): array
    {
        $yaml = $this->contentBlock->getYaml();
        $vendor = $this->contentBlock->getVendor();
        $package = $this->contentBlock->getPackage();
        $contentType = [
            'identifier' => $this->contentBlock->getName(),
            'title' => $this->title,
            'description' => $this->description,
            'columns' => $this->columns,
            'showItems' => $this->showItems,
            'overrideColumns' => $this->overrideColumns,
            'vendor' => $vendor,
            'package' => $package,
            'typeName' => $this->typeName,
            'languagePathTitle' => $this->languagePathTitle,
            'languagePathDescription' => $this->languagePathDescription,
        ];
        if ($isRootTable) {
            $contentTypeIcon = $this->contentBlock->getIcon();
            $contentType['priority'] = (int)($yaml['priority'] ?? 0);
        } else {
            $absolutePath = GeneralUtility::getFileAbsFileName($this->contentBlock->getExtPath());
            $contentTypeIcon = ContentTypeIconResolver::resolve(
                $this->contentBlock->getName(),
                $absolutePath,
                $this->contentBlock->getHostExtension(),
                $identifier,
                $this->contentBlock->getContentType(),
                $this->table,
                $this->typeName,
            );
        }
        $contentType['typeIcon'] = $contentTypeIcon->toArray();
        if ($this->contentBlock->getContentType() === ContentType::PAGE_TYPE) {
            $contentType['typeIconHideInMenu'] = $this->contentBlock->getIconHideInMenu()->toArray();
        }
        if ($this->contentBlock->getContentType() === ContentType::CONTENT_ELEMENT) {
            $contentType['group'] = $yaml['group'] ?? $this->contentBlock->getContentType()->getDefaultGroup();
            $contentType['saveAndClose'] = (bool)($yaml['saveAndClose'] ?? false);
        }
        return $contentType;
    }
}
