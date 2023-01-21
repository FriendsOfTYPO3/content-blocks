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
use TYPO3\CMS\ContentBlocks\Utility\LanguagePathUtility;

class PageTsConfigGenerator
{
    public static function generate(ContentElementDefinition $contentElementDefinition): string
    {
        $partialLanguagePath = LanguagePathUtility::getPartialLanguageIdentifierPath($contentElementDefinition->getPackage(), $contentElementDefinition->getVendor(), $contentElementDefinition->getVendor());
        return <<<HEREDOC
mod.wizards.newContentElement.wizardItems.{$contentElementDefinition->getWizardGroup()} {
    elements {
        {$contentElementDefinition->getType()} {
            iconIdentifier = {$contentElementDefinition->getType()}
            title = LLL:$partialLanguagePath.{$contentElementDefinition->getPackage()}.title
            description = LLL:$partialLanguagePath.{$contentElementDefinition->getPackage()}.description
            tt_content_defValues {
                CType = {$contentElementDefinition->getType()}
            }
        }
    }
    show := addToList({$contentElementDefinition->getType()})
}
HEREDOC;
    }
}
