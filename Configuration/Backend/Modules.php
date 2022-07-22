<?php

/**
 * Definitions for modules provided by EXT:content_blocks
 */

use TYPO3\CMS\ContentBlocks\BackendController\ContentBlocksController;

return [
    ContentBlocksController::ROUTE_IDENTIFIER => [
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
            // Hey, this would be cool
//            'new' => [
//                'path' => '/module/tools/content_blocks/new',
//                'target' => ContentBlocksController::class . '::editAction',
//            ],
//            'edit' => [
//                'path' => '/module/tools/content_blocks/edit/%\d+%',
//                'target' => ContentBlocksController::class . '::editAction',
//            ],
        ],
    ],
];
