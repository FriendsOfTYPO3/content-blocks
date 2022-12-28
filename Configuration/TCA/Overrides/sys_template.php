<?php

defined('TYPO3') or die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    extKey: 'content_blocks',
    path: 'Configuration/TypoScript',
    title: 'Content Blocks',
);
