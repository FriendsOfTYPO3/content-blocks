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
use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\Core\Core\Event\BootCompletedEvent;
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @internal Not part of TYPO3's public API.
 */
class TypoScriptGenerator
{
    public function __construct(
        protected readonly TableDefinitionCollection $tableDefinitionCollection,
        protected readonly IconRegistry $iconRegistry,
    ) {
    }

    public function __invoke(BootCompletedEvent $event): void
    {
        foreach ($this->tableDefinitionCollection as $tableDefinition) {
            foreach ($tableDefinition->getTypeDefinitionCollection() ?? [] as $typeDefinition) {
                if (!$typeDefinition instanceof ContentElementDefinition) {
                    continue;
                }
                $this->iconRegistry->registerIcon(
                    identifier: $typeDefinition->getWizardIconIdentifier(),
                    iconProviderClassName: $typeDefinition->getIconProviderClassName(),
                    options: ['source' => $typeDefinition->getWizardIconPath()],
                );
                ExtensionManagementUtility::addTypoScriptSetup($this->generate($typeDefinition));
            }
        }
    }

    protected function generate(ContentElementDefinition $contentElementDefinition): string
    {
        $contentBlockRegistry = GeneralUtility::makeInstance(ContentBlockRegistry::class);
        $privatePath = $contentBlockRegistry->getContentBlockPath($contentElementDefinition->getName()) . '/' . ContentBlockPathUtility::getPrivateFolderPath();

        return <<<HEREDOC
tt_content.{$contentElementDefinition->getTypeName()} =< lib.contentBlock
tt_content.{$contentElementDefinition->getTypeName()} {
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
    settings.name = {$contentElementDefinition->getName()}
}
HEREDOC;
    }
}
