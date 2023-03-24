<?php

use TYPO3\CMS\ContentBlocks\Definition\ContentElementDefinition;
use TYPO3\CMS\ContentBlocks\Loader\LoaderFactory;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

defined('TYPO3') or die();

$contentBlockRegistry = GeneralUtility::makeInstance(ContentBlockRegistry::class);
$tableDefinitionCollection = GeneralUtility::makeInstance(LoaderFactory::class)->load();
foreach ($tableDefinitionCollection as $tableName => $tableDefinition) {
    foreach ($tableDefinition->getTypeDefinitionCollection() ?? [] as $typeDefinition) {
        if ($typeDefinition instanceof ContentElementDefinition) {
            ExtensionManagementUtility::addTcaSelectItemGroup(
                table: $typeDefinition->getTable(),
                field: $typeDefinition->getTypeField(),
                groupId: 'content_blocks',
                groupLabel: 'Content Blocks',
                position: 'after:default',
            );
        }
        ExtensionManagementUtility::addTcaSelectItem(
            table: $typeDefinition->getTable(),
            field: $typeDefinition->getTypeField(),
            item: [
                'label' => 'LLL:' . $contentBlockRegistry->getContentBlockPath($typeDefinition->getName()) . '/' . ContentBlockPathUtility::getLanguageFilePath() . ':' . $typeDefinition->getVendor() . '.' . $typeDefinition->getPackage() . '.title',
                'value' => $typeDefinition->getTypeName(),
                'icon' => $typeDefinition instanceof ContentElementDefinition ? $typeDefinition->getWizardIconIdentifier() : '',
                'group' => $typeDefinition instanceof ContentElementDefinition ? 'content_blocks' : '',
            ]
        );
    }
}
