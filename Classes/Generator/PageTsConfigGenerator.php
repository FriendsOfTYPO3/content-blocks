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

class PageTsConfigGenerator
{
    public static function getStandardPageTsConfig(ContentElementDefinition $contentElementDefinition): string
    {
        return '
            mod.wizards.newContentElement.wizardItems.' . $contentElementDefinition->getWizardGroup() . '  {
                elements {
                    ' . $contentElementDefinition->getCType() . ' {
                        iconIdentifier = ' . $contentElementDefinition->getCType() . '
                        title = LLL:' . $contentElementDefinition->getPrivatePath()  . 'Language' . '/' . 'Labels.xlf:' . $contentElementDefinition->getVendor() . '.' . $contentElementDefinition->getPackage() . '.title
                        description = LLL:' . $contentElementDefinition->getPrivatePath()  . 'Language' . '/' . 'Labels.xlf:' . $contentElementDefinition->getVendor() . '.' . $contentElementDefinition->getPackage() . '.description
                        tt_content_defValues {
                            CType = ' . $contentElementDefinition->getCType() . '
                        }
                    }
                }
                show := addToList(' . $contentElementDefinition->getCType() . ')
            }
        ';
    }
}
