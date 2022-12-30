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
        // @todo make this more generic and remove instanceof
        // @todo add own group and or the possibility to define groups.
        if ($typeDefinition instanceof ContentElementDefinition) {
            ExtensionManagementUtility::addTcaSelectItemGroup(
                table: $typeDefinition->getTable(),
                field: $typeDefinition->getTypeField(),
                groupId: 'content_blocks',
                groupLabel: 'Content Blocks',
                position: 'after:default',
            );
            ExtensionManagementUtility::addTcaSelectItem(
                table: $typeDefinition->getTable(),
                field: $typeDefinition->getTypeField(),
                item: [
                    'LLL:' . LanguagePathUtility::getFullLanguageIdentifierPath($typeDefinition->getPackage(), $typeDefinition->getVendor(), $typeDefinition->getPackage() . '.title'),
                    $typeDefinition->getCType(),
                    // @todo not sure about icon name = Ctype
                    $typeDefinition->getCType(),
                    'content_blocks'
                ]
            );
        }
    }
}
