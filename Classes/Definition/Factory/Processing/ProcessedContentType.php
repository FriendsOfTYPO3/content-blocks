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
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentTypeIcon;
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
        $vendor = $this->contentBlock->getVendor();
        $package = $this->contentBlock->getPackage();
        $contentType = [
            'identifier' => $this->contentBlock->getName(),
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
            $contentTypeIcon = new ContentTypeIcon();
            $contentTypeIcon->iconPath = $this->contentBlock->getIcon();
            $contentTypeIcon->iconProvider = $this->contentBlock->getIconProvider();
            $contentType['priority'] = (int)($this->contentBlock->getYaml()['priority'] ?? 0);
        } else {
            $absolutePath = GeneralUtility::getFileAbsFileName($this->contentBlock->getExtPath());
            $contentTypeIcon = ContentTypeIconResolver::resolve(
                $this->contentBlock->getName(),
                $absolutePath,
                $this->contentBlock->getExtPath(),
                $identifier,
                $this->contentBlock->getContentType(),
            );
        }
        $contentType['typeIconPath'] = $contentTypeIcon->iconPath;
        $contentType['iconProvider'] = $contentTypeIcon->iconProvider;
        $contentType['typeIconIdentifier'] = $this->buildTypeIconIdentifier($contentTypeIcon);
        if ($this->contentBlock->getContentType() === ContentType::CONTENT_ELEMENT) {
            $contentType['group'] = $this->contentBlock->getYaml()['group'] ?? $this->contentBlock->getContentType()->getDefaultGroup();
        }
        return $contentType;
    }

    /**
     * We add a part of the md5 hash here in order to mitigate browser caching issues when changing the Content Block
     * Icon. Otherwise, the icon identifier would always be the same and stored in the local storage.
     */
    private function buildTypeIconIdentifier(ContentTypeIcon $contentTypeIcon): string
    {
        $typeIconIdentifier = $this->table . '-' . $this->typeName;
        $absolutePath = GeneralUtility::getFileAbsFileName($contentTypeIcon->iconPath);
        if ($absolutePath !== '') {
            $contents = @file_get_contents($absolutePath);
            if ($contents === false) {
                throw new \RuntimeException(
                    'Unable to load resources of Content Block "' . $this->contentBlock->getName() . '".'
                    . ' If you have deleted this Content Block, please flush system caches and reload the page.',
                    1698430544,
                );
            }
            $hash = md5($contents);
            $hasSubString = substr($hash, 0, 7);
            $typeIconIdentifier .= '-' . $hasSubString;
        }
        return $typeIconIdentifier;
    }
}
