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

class TypoScriptGenerator
{
    public static function typoScriptForContentElementDefinition(ContentElementDefinition $contentElementDefinition): string
    {
        return '
            tt_content.' . $contentElementDefinition->getCType() . ' < lib.contentBlock
            tt_content.' . $contentElementDefinition->getCType() . '{
                templateName = Frontend
                templateRootPaths {
                    20 = ' . $contentElementDefinition->getPrivatePath() . '
                }
                partialRootPaths {
                    20 = ' . $contentElementDefinition->getPrivatePath() . 'Partials/
                }
                layoutRootPaths {
                    20 = ' . $contentElementDefinition->getPrivatePath() . 'Layouts/
                }
            }
            ';
    }
}
