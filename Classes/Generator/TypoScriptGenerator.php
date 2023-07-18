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

use TYPO3\CMS\ContentBlocks\Definition\ContentType;
use TYPO3\CMS\ContentBlocks\Definition\ContentTypeInterface;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\Core\Core\Event\BootCompletedEvent;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * @internal Not part of TYPO3's public API.
 */
class TypoScriptGenerator
{
    public function __construct(
        protected readonly TableDefinitionCollection $tableDefinitionCollection,
        protected readonly ContentBlockRegistry $contentBlockRegistry,
    ) {
    }

    public function __invoke(BootCompletedEvent $event): void
    {
        foreach ($this->tableDefinitionCollection as $tableDefinition) {
            foreach ($tableDefinition->getTypeDefinitionCollection() ?? [] as $typeDefinition) {
                if ($typeDefinition->getContentType() === ContentType::CONTENT_ELEMENT) {
                    ExtensionManagementUtility::addTypoScriptSetup($this->generate($typeDefinition));
                }
            }
        }
    }

    protected function generate(ContentTypeInterface $typeDefinition): string
    {
        $privatePath = $this->contentBlockRegistry->getContentBlockPath($typeDefinition->getName()) . '/' . ContentBlockPathUtility::getPrivateFolder();
        $template = ContentBlockPathUtility::getFrontendTemplateFileNameWithoutExtension();

        return <<<HEREDOC
tt_content.{$typeDefinition->getTypeName()} =< lib.contentBlock
tt_content.{$typeDefinition->getTypeName()} {
    templateName = {$template}
    templateRootPaths {
        20 = $privatePath
    }
    partialRootPaths {
        20 = $privatePath/Partials
    }
    layoutRootPaths {
        20 = $privatePath/Layouts
    }
    settings.name = {$typeDefinition->getName()}
}
HEREDOC;
    }
}
