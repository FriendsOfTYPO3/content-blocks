<?php

use TYPO3\CMS\ContentBlocks\Definition\ContentElementDefinition;
use TYPO3\CMS\ContentBlocks\Loader\LoaderFactory;
use TYPO3\CMS\ContentBlocks\Utility\LanguagePathUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

defined('TYPO3') or die();

$tableDefinitionCollection = GeneralUtility::makeInstance(LoaderFactory::class)->load();
foreach ($tableDefinitionCollection as $tableName => $tableDefinition) {
    foreach ($tableDefinition->getTypeDefinitionCollection() ?? [] as $typeDefinition) {
        $title = LanguagePathUtility::getFullLanguageIdentifierPath(
            package: $typeDefinition->getPackage(),
            vendor: $typeDefinition->getVendor(),
            identifier: $typeDefinition->getVendor(),
            suffix: $typeDefinition->getPackage() . '.title'
        );
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
                $title,
                $typeDefinition->getType(),
                $typeDefinition instanceof ContentElementDefinition ? $typeDefinition->getWizardIconIdentifier() : '',
                $typeDefinition instanceof ContentElementDefinition ? 'content_blocks' : '',
            ]
        );
    }
}
