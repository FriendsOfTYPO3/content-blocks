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
use TYPO3\CMS\ContentBlocks\Registry\LanguageFileRegistryInterface;
use TYPO3\CMS\ContentBlocks\Service\TypeDefinitionLabelService;
use TYPO3\CMS\Core\TypoScript\IncludeTree\Event\ModifyLoadedPageTsConfigEvent;

/**
 * @internal Not part of TYPO3's public API.
 */
class PageTsConfigGenerator
{
    public function __construct(
        protected readonly TableDefinitionCollection $tableDefinitionCollection,
        protected readonly TypeDefinitionLabelService $typeDefinitionLabelService,
        protected readonly LanguageFileRegistryInterface $languageFileRegistry,
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
        $title = $this->typeDefinitionLabelService->getLLLPathForTitle($contentElementDefinition);
        $key = $this->typeDefinitionLabelService->buildTitleKey($contentElementDefinition);
        if (!$this->languageFileRegistry->isset($contentElementDefinition->getName(), $key)) {
            $title = $contentElementDefinition->getName();
        }
        $description = $this->typeDefinitionLabelService->getLLLPathForDescription($contentElementDefinition);
        return <<<HEREDOC
mod.wizards.newContentElement.wizardItems.{$contentElementDefinition->getWizardGroup()} {
    elements {
        {$contentElementDefinition->getTypeName()} {
            iconIdentifier = {$contentElementDefinition->getWizardIconIdentifier()}
            title = $title
            description = $description
            tt_content_defValues {
                CType = {$contentElementDefinition->getTypeName()}
            }
        }
    }
    show := addToList({$contentElementDefinition->getTypeName()})
}
HEREDOC;
    }
}
