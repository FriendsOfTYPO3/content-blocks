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

use TYPO3\CMS\ContentBlocks\Domain\Repository\ContentBlockConfigurationRepository;
use TYPO3\CMS\ContentBlocks\Enumeration\FieldType;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class TcaGenerator
{
    /**
     * Manages to set up the TCA for each ContentBlock
     *
     * $contentBlocksConfig as method param mainly for unti test purposes.
     *
     * @param array $contentBlocksConfig    configuration of all ContentBlocks
     */
    public function setTca(array $contentBlocksConfig = null): array
    {
        if ($contentBlocksConfig === null) {
            /** @param array */
            $contentBlocksConfig = GeneralUtility::makeInstance(ContentBlockConfigurationRepository::class)->findAll();
        }
        // result only for unit test purposes.
        $result = [];

        foreach ($contentBlocksConfig as $contentBlock) {
            /***************
             * Add Content Element
             */
            if (
                !isset($GLOBALS['TCA']['tt_content']['types'][$contentBlock['CType']]) ||
                !is_array($GLOBALS['TCA']['tt_content']['types'][$contentBlock['CType']])
            ) {
                $GLOBALS['TCA']['tt_content']['types'][$contentBlock['CType']] = [];
            }
        }
        // return result only for unit test purposes.
        return $result;
    }
}
