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

use TYPO3\CMS\ContentBlocks\Definition\ContentType\PageTypeDefinition;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;

/**
 * @internal Not part of TYPO3's public API.
 */
readonly class UserTsConfigGenerator
{
    public function __construct(
        protected TableDefinitionCollection $tableDefinitionCollection,
    ) {}

    public function generate(): string
    {
        $userTsConfig = [];
        foreach ($this->tableDefinitionCollection as $tableDefinition) {
            foreach ($tableDefinition->contentTypeDefinitionCollection as $typeDefinition) {
                if ($typeDefinition instanceof PageTypeDefinition) {
                    $options = 'options.pageTree.doktypesToShowInNewPageDragArea := addToList(' . $typeDefinition->getTypeName() . ')';
                    $userTsConfig[] = $options;
                }
            }
        }
        $concatenatedTypoScript = implode(LF, $userTsConfig);
        return $concatenatedTypoScript;
    }
}
