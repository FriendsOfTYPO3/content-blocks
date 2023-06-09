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

use TYPO3\CMS\ContentBlocks\Definition\TypeDefinition;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;

/**
 * @internal Not part of TYPO3's public API.
 */
class TypeDefinitionLabelService
{
    public function __construct(protected readonly ContentBlockRegistry $contentBlockRegistry)
    {
    }

    public function getLLLPathForTitle(TypeDefinition $typeDefinition): string
    {
        $label = $this->getBasePath($typeDefinition) . '.title';
        return $label;
    }

    public function getLLLPathForDescription(TypeDefinition $typeDefinition): string
    {
        $label = $this->getBasePath($typeDefinition) . '.description';
        return $label;
    }

    protected function getBasePath(TypeDefinition $typeDefinition): string
    {
        $basePath = 'LLL:' . $this->contentBlockRegistry->getContentBlockPath($typeDefinition->getName()) . '/'
            . ContentBlockPathUtility::getLanguageFilePath()
            . ':' . $typeDefinition->getVendor() . '.' . $typeDefinition->getPackage();
        return $basePath;
    }
}
