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

namespace TYPO3\CMS\ContentBlocks\Generator;

use TYPO3\CMS\ContentBlocks\Definition\ContentElementDefinition;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @internal Not part of TYPO3's public API.
 */
class PageTsConfigGenerator
{
    public static function generate(ContentElementDefinition $contentElementDefinition): string
    {
        $contentBlockRegistry = GeneralUtility::makeInstance(ContentBlockRegistry::class);
        $partialLanguagePath = 'LLL:' . $contentBlockRegistry->getContentBlockPath($contentElementDefinition->getName()) . '/' . ContentBlockPathUtility::getPathToDefaultLanguageFile() . ':' . $contentElementDefinition->getVendor() . '.' . $contentElementDefinition->getPackage();
        return <<<HEREDOC
mod.wizards.newContentElement.wizardItems.{$contentElementDefinition->getWizardGroup()} {
elements {
{$contentElementDefinition->getTypeName()} {
    iconIdentifier = {$contentElementDefinition->getWizardIconIdentifier()}
    title = $partialLanguagePath.title
    description = $partialLanguagePath.description
    tt_content_defValues {
        CType = {$contentElementDefinition->getTypeName()}
    }
}
}
show := addToList({$contentElementDefinition->getTypeName()})
}
HEREDOC;
    }
}
