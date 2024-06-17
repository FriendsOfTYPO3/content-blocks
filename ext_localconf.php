<?php

defined('TYPO3') or die();

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

ExtensionManagementUtility::addTypoScriptSetup('
lib.contentBlock = FLUIDTEMPLATE
lib.contentBlock {
    dataProcessing {
        10 = content-blocks
    }
}
');

$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['cb'][] = 'TYPO3\\CMS\\ContentBlocks\\ViewHelpers';
