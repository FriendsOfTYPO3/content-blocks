<?php

declare(strict_types=1);

use TYPO3\CMS\ContentBlocks\Service\LocalConfRegistrationService;

defined('TYPO3') or die();

(static function () {
    LocalConfRegistrationService::setup();
})();
