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
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;

class TypoScriptGenerator
{
    public static function generate(ContentElementDefinition $contentElementDefinition): string
    {
        $privatePath = ContentBlockPathUtility::getRelativeContentBlocksPrivatePath($contentElementDefinition->getPackage(), $contentElementDefinition->getVendor());
        return <<<HEREDOC
tt_content.{$contentElementDefinition->getType()} =< lib.contentBlock
tt_content.{$contentElementDefinition->getType()} {
    templateName = Frontend
    templateRootPaths {
        20 = $privatePath
    }
    partialRootPaths {
        20 = $privatePath/Partials
    }
    layoutRootPaths {
        20 = $privatePath/Layouts
    }
}
HEREDOC;
    }
}
