<?php

defined('TYPO3') or die();

use TYPO3\CMS\ContentBlocks\Definition\ContentElementDefinition;
use TYPO3\CMS\ContentBlocks\Generator\PageTsConfigGenerator;
use TYPO3\CMS\ContentBlocks\Generator\TypoScriptGenerator;
use TYPO3\CMS\ContentBlocks\Loader\LoaderFactory;
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

$iconRegistry = GeneralUtility::makeInstance(IconRegistry::class);
foreach (GeneralUtility::makeInstance(LoaderFactory::class)->load() as $tableDefinition) {
    foreach ($tableDefinition->getTypeDefinitionCollection() ?? [] as $typeDefinition) {
        if (!$typeDefinition instanceof ContentElementDefinition) {
            continue;
        }
        $iconRegistry->registerIcon(
            identifier: $typeDefinition->getCType(),
            iconProviderClassName: $typeDefinition->getIconProviderClassName(),
            options: ['source' => $typeDefinition->getIcon()],
        );
        ExtensionManagementUtility::addPageTSConfig(PageTsConfigGenerator::generate($typeDefinition));
        ExtensionManagementUtility::addTypoScriptSetup(TypoScriptGenerator::generate($typeDefinition));
    }
}
