<?php

return [
    'dependencies' => [
        'core',
        'backend',
    ],
    'imports' => [
        '@typo3/content-blocks/' => [
            'path' => 'EXT:content_blocks/Resources/Public/JavaScript/',
        ]
//        // @todo
//        'lit' => 'EXT:core/Resources/Public/JavaScript/Contrib/lit@2.0.0/index.js',
//        'lit/' => 'EXT:core/Resources/Public/JavaScript/Contrib/lit@2.0.0/',
//        'lit-element' => 'EXT:core/Resources/Public/JavaScript/Contrib/lit-element@3.0.0/index.js',
//        'lit-element/' => 'EXT:core/Resources/Public/JavaScript/Contrib/lit-element@3.0.0/',
//        'lit-html' => 'EXT:core/Resources/Public/JavaScript/Contrib/lit-html@2.0.0/lit-html.js',
//        'lit-html/' => 'EXT:core/Resources/Public/JavaScript/Contrib/lit-html@2.0.0/',
    ],
];
