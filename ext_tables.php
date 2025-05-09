<?php

declare(strict_types=1);

use TYPO3\CMS\Core\DataHandling\PageDoktypeRegistry;
use TYPO3\CMS\Core\Utility\GeneralUtility;

defined('TYPO3') || die();

(static function () {
    GeneralUtility::makeInstance(PageDoktypeRegistry::class);
})();
