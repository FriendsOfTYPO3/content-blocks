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

namespace TYPO3\CMS\ContentBlocks\Service;

use TYPO3\CMS\ContentBlocks\Definition\ContentElementDefinition;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\ContentBlocks\Generator\PageTsConfigGenerator;
use TYPO3\CMS\ContentBlocks\Generator\TypoScriptGenerator;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class LocalConfRegistrationService
{
    public function __construct(
        protected TableDefinitionCollection $tableDefinitionCollection
    ) {
    }

    public function setup(): void
    {
        // @todo: Configure the caching

        // Icons
        /** @var IconRegistry $iconRegistry */
        $iconRegistry = GeneralUtility::makeInstance(IconRegistry::class);
        $iconRegistry->registerIcon(
            'ext-content_blocks',
            SvgIconProvider::class,
            ['source' => 'EXT:content_blocks/Resources/Public/Icons/Extension.svg']
        );

        // TypoScript
        // @todo: find a better way to add individual definitions?
        $importTypoScriptTemplate = (string)GeneralUtility::makeInstance(ExtensionConfiguration::class)
                ->get('content_blocks', 'contentBlockDefinition');
        if (strlen($importTypoScriptTemplate) > 2) {
            ExtensionManagementUtility::addTypoScriptSetup(
                "@import '$importTypoScriptTemplate'"
            );
        } else {
            ExtensionManagementUtility::addTypoScriptSetup(
                "@import 'EXT:content_blocks/Configuration/TypoScript/setup.typoscript'"
            );
        }

        foreach ($this->tableDefinitionCollection as $tableDefinition) {
            foreach ($tableDefinition->getTypeDefinitionCollection() as $typeDefinition) {
                if ($typeDefinition instanceof ContentElementDefinition) {
                    // add PageTsConfig
                    ExtensionManagementUtility::addPageTSConfig(
                        PageTsConfigGenerator::getStandardPageTsConfig($typeDefinition)
                    );

                    // add icon for ContentElement
                    $iconRegistry->registerIcon(
                        $typeDefinition->getCType(),
                        $typeDefinition->getIconProviderClassName(),
                        ['source' => $typeDefinition->getIcon()],
                    );

                    // add typoscript for ContentElement
                    ExtensionManagementUtility::addTypoScriptSetup(
                        TypoScriptGenerator::typoScriptForContentElementDefinition($typeDefinition)
                    );
                }
            }
        }
    }
}
