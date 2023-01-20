<?php

use TYPO3\CMS\ContentBlocks\BackendController\ContentBlocksController;

return [
    'tools_contentblocks' => [
        'parent' => 'tools',
        'access' => 'admin',
        'path' => '/module/tools/content_blocks',
        'methods' => ['GET'],
        'iconIdentifier' => 'module-generic',
        'labels' => 'LLL:EXT:content_blocks/Resources/Private/Language/locallang_module.xlf',
        'routes' => [
            '_default' => [
                'target' => ContentBlocksController::class . '::overviewAction',
            ],
        ],
    ],
];
