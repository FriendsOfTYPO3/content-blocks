<?php

$config = \TYPO3\CodingStandards\CsFixerConfig::create();
$config->setFinder(
    (new PhpCsFixer\Finder())
        ->ignoreVCSIgnored(true)
        ->in(realpath(__DIR__ . '/../../'))
);
return $config;
