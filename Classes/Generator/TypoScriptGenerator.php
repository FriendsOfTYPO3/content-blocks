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

use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @internal Not part of TYPO3's public API.
 */
readonly class TypoScriptGenerator
{
    public function __construct(
        protected TableDefinitionCollection $tableDefinitionCollection,
        protected ContentBlockRegistry $contentBlockRegistry,
    ) {}

    public function generate(): string
    {
        $typoScriptArray = [];
        foreach ($this->tableDefinitionCollection as $tableDefinition) {
            if ($tableDefinition->contentType !== ContentType::CONTENT_ELEMENT) {
                continue;
            }
            foreach ($tableDefinition->contentTypeDefinitionCollection as $typeDefinition) {
                $extPath = $this->contentBlockRegistry->getContentBlockExtPath($typeDefinition->getName());
                $extPrivatePath = $extPath . '/' . ContentBlockPathUtility::getTemplatesFolder();
                $templateFileName = ContentBlockPathUtility::getFrontendTemplateFileName();
                $extFilePath = $extPrivatePath . '/' . $templateFileName;
                $absolutePath = GeneralUtility::getFileAbsFileName($extFilePath);
                if (!file_exists($absolutePath)) {
                    continue;
                }
                $typoScriptArray[] = $this->generateSingle(
                    $typeDefinition->getName(),
                    $typeDefinition->getTypeName(),
                    $extFilePath,
                    $extPrivatePath
                );
            }
        }
        $concatenatedTypoScript = implode(LF, $typoScriptArray);
        return $concatenatedTypoScript;
    }

    protected function generateSingle(
        string $name,
        string $typeName,
        string $extFilePath,
        string $extPrivatePath,
    ): string {
        $typoScript = <<<HEREDOC
tt_content.$typeName =< lib.contentBlock
tt_content.$typeName {
    file = $extFilePath
    partialRootPaths {
        20 = $extPrivatePath/partials/
    }
    layoutRootPaths {
        20 = $extPrivatePath/layouts/
    }
    settings._content_block_name = $name
}
HEREDOC;
        return $typoScript;
    }
}
