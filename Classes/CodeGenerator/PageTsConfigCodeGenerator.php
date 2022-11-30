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

namespace TYPO3\CMS\ContentBlocks\CodeGenerator;

use TYPO3\CMS\ContentBlocks\Definition\ContentElementDefinition;

class PageTsConfigCodeGenerator
{
    public static function getStandardPageTsConfig(ContentElementDefinition $ceDefinition): string
    {
        return '
            mod.wizards.newContentElement.wizardItems.' . $ceDefinition->getWizardGroup() . '  {
                elements {
                    ' . $ceDefinition->getCType() . ' {
                        iconIdentifier = ' . $ceDefinition->getCType() . '
                        title = LLL:' . $ceDefinition->getPrivatePath()  . 'Language' . DIRECTORY_SEPARATOR . 'Labels.xlf:' . $ceDefinition->getVendor() . '.' . $ceDefinition->getPackage() . '.title
                        description = LLL:' . $ceDefinition->getPrivatePath()  . 'Language' . DIRECTORY_SEPARATOR . 'Labels.xlf:' . $ceDefinition->getVendor() . '.' . $ceDefinition->getPackage() . '.description
                        tt_content_defValues {
                            CType = ' . $ceDefinition->getCType() . '
                        }
                    }
                }
                show := addToList(' . $ceDefinition->getCType() . ')
            }
        ';
    }
}
