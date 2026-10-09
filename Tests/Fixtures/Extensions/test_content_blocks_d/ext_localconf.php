<?php

$GLOBALS['TYPO3_CONF_VARS']['LANG']['loader']['yaml'] ??=
	Symfony\Component\Translation\Loader\YamlFileLoader::class;
$GLOBALS['TYPO3_CONF_VARS']['LANG']['format']['priority'] = 'xlf,yaml';
