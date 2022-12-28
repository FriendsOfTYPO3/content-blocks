<?php

defined('TYPO3') or die();

use TYPO3\CMS\ContentBlocks\Generator\TcaGenerator;
use TYPO3\CMS\Core\Utility\GeneralUtility;

$tcaGenerator = GeneralUtility::makeInstance(TcaGenerator::class);
$tcaGenerator->setTca();
//$GLOBALS['TCA'] = array_merge_recursive($GLOBALS['TCA'], $tcaGenerator->setTca());
