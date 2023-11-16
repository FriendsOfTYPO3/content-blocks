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

namespace TYPO3\CMS\ContentBlocks\Service;

use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentTypeInterface;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;

/**
 * @internal Not part of TYPO3's public API.
 */
class TypeDefinitionLabelService
{
    public function __construct(protected readonly ContentBlockRegistry $contentBlockRegistry) {}

    public function getLLLPathForTitle(ContentTypeInterface $typeDefinition): string
    {
        $label = $this->getBasePath($typeDefinition) . ':' . $this->buildTitleKey($typeDefinition);
        return $label;
    }

    public function getLLLPathForDescription(ContentTypeInterface $typeDefinition): string
    {
        $label = $this->getBasePath($typeDefinition) . ':' . $this->buildDescriptionKey($typeDefinition);
        return $label;
    }

    public function buildTitleKey(ContentTypeInterface $typeDefinition): string
    {
        $vendor = $typeDefinition->getVendor();
        $package = $typeDefinition->getPackage();
        $key = $vendor . '.' . $package . '.title';
        return $key;
    }

    public function buildDescriptionKey(ContentTypeInterface $typeDefinition): string
    {
        $vendor = $typeDefinition->getVendor();
        $package = $typeDefinition->getPackage();
        $key = $vendor . '.' . $package . '.description';
        return $key;
    }

    public function getBasePath(ContentTypeInterface $typeDefinition): string
    {
        $contentBlockPath = $this->contentBlockRegistry->getContentBlockPath($typeDefinition->getName());
        $languageFilePath = ContentBlockPathUtility::getLanguageFilePath();
        $basePath = 'LLL:' . $contentBlockPath . '/' . $languageFilePath;
        return $basePath;
    }
}
