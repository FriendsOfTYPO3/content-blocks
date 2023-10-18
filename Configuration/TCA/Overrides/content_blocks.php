<?php

declare(strict_types=1);

use TYPO3\CMS\ContentBlocks\Generator\TcaGenerator;
use TYPO3\CMS\Core\Utility\GeneralUtility;

// @todo remove in v13
$tcaGenerator = GeneralUtility::makeInstance(TcaGenerator::class);
$GLOBALS['TCA'] = $tcaGenerator->generateTcaOverrides();
