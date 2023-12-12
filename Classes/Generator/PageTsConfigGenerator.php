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

use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentElementDefinition;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\ContentBlocks\Registry\LanguageFileRegistry;
use TYPO3\CMS\Core\TypoScript\IncludeTree\Event\ModifyLoadedPageTsConfigEvent;

/**
 * @internal Not part of TYPO3's public API.
 */
class PageTsConfigGenerator
{
    public function __construct(
        protected readonly TableDefinitionCollection $tableDefinitionCollection,
        protected readonly LanguageFileRegistry $languageFileRegistry,
    ) {}

    public function __invoke(ModifyLoadedPageTsConfigEvent $event): void
    {
        foreach ($this->tableDefinitionCollection as $tableDefinition) {
            foreach ($tableDefinition->getContentTypeDefinitionCollection() ?? [] as $typeDefinition) {
                if ($typeDefinition instanceof ContentElementDefinition) {
                    $event->addTsConfig($this->generate($typeDefinition));
                }
            }
        }
    }

    protected function generate(ContentElementDefinition $contentElementDefinition): string
    {
        $languagePathTitle = $contentElementDefinition->getLanguagePathTitle();
        if ($this->languageFileRegistry->isset($contentElementDefinition->getName(), $languagePathTitle)) {
            $title = $languagePathTitle;
        } else {
            $title = $contentElementDefinition->getTitle();
        }
        $languagePathDescription = $contentElementDefinition->getLanguagePathDescription();
        if ($this->languageFileRegistry->isset($contentElementDefinition->getName(), $languagePathDescription)) {
            $description = $languagePathDescription;
        } else {
            $description = $contentElementDefinition->getDescription();
        }
        $group = $contentElementDefinition->getGroup();
        $typeName = $contentElementDefinition->getTypeName();
        $iconIdentifier = $contentElementDefinition->getTypeIconIdentifier();
        $saveAndClose = $contentElementDefinition->hasSaveAndClose() ? '1' : '0';
        return <<<HEREDOC
mod.wizards.newContentElement.wizardItems.$group {
    elements {
        $typeName {
            iconIdentifier = $iconIdentifier
            title = $title
            description = $description
            saveAndClose = $saveAndClose
            tt_content_defValues {
                CType = $typeName
            }
        }
    }
    show := addToList($typeName)
}
HEREDOC;
    }
}
