<?php

defined('TYPO3') or die();

use TYPO3\CMS\Backend\Form\FormDataProvider\TcaSelectItems;
use TYPO3\CMS\ContentBlocks\Form\FormDataProvider\AllowedRecordTypesInCollection;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

ExtensionManagementUtility::addTypoScriptSetup('
lib.contentBlock = FLUIDTEMPLATE
lib.contentBlock {
    dataProcessing {
        10 = content-blocks
    }
}
styles.content.get.select.where.postUserFunc = TYPO3\CMS\ContentBlocks\UserFunction\ContentWhere->extend
');

$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['cb'][] = 'TYPO3\\CMS\\ContentBlocks\\ViewHelpers';

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['tcaDatabaseRecord'][AllowedRecordTypesInCollection::class] = [
    'depends' => [
        TcaSelectItems::class,
    ],
];
