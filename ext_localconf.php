<?php

defined('TYPO3') or die();

use TYPO3\CMS\ContentBlocks\DataHandler\ClearBackendPreviewCaches;
use TYPO3\CMS\Core\Cache\Backend\FileBackend;
use TYPO3\CMS\Core\Cache\Backend\SimpleFileBackend;
use TYPO3\CMS\Core\Cache\Frontend\PhpFrontend;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

ExtensionManagementUtility::addTypoScriptSetup('
lib.contentBlock = FLUIDTEMPLATE
lib.contentBlock {
    dataProcessing {
        10 = content-blocks
    }
}
');

$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['content_blocks_yaml'] = [
    'frontend' => PhpFrontend::class,
    'backend' => SimpleFileBackend::class,
    'options' => [
        'defaultLifetime' => 0,
    ],
    'groups' => ['system'],
];

$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['content_blocks_object'] = [
    'frontend' => PhpFrontend::class,
    'backend' => SimpleFileBackend::class,
    'options' => [
        'defaultLifetime' => 0,
    ],
    'groups' => ['system'],
];

$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['content_blocks_code'] = [
    'frontend' => PhpFrontend::class,
    'backend' => SimpleFileBackend::class,
    'options' => [
        'defaultLifetime' => 0,
    ],
    'groups' => ['system'],
];

$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['content_blocks_preview'] = [
    'frontend' => PhpFrontend::class,
    'backend' => FileBackend::class,
    'options' => [
        'defaultLifetime' => 0,
    ],
    'groups' => ['system'],
];

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = ClearBackendPreviewCaches::class;

$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['cb'][] = 'TYPO3\\CMS\\ContentBlocks\\ViewHelpers';
