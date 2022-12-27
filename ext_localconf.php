<?php

declare(strict_types=1);

use TYPO3\CMS\ContentBlocks\Service\LocalConfRegistrationService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

GeneralUtility::makeInstance(LocalConfRegistrationService::class)->setup();
