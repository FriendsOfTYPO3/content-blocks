<?php

use TYPO3\CMS\ContentBlocks\BackendController\ContentBlocksController;

return [
    ContentBlocksController::ROUTE_IDENTIFIER . '/contentBlock/new' => [
        'path' => '/module/tools/content_blocks/new',
        'methods' => ['GET'],
        'target' => ContentBlocksController::class . '::newAction',
    ],
    ContentBlocksController::ROUTE_IDENTIFIER . '/contentBlock/edit' => [
        'path' => '/module/tools/content_blocks/edit',
        'methods' => ['GET'],
        'target' => ContentBlocksController::class . '::editAction',
    ],
    ContentBlocksController::ROUTE_IDENTIFIER . '/contentBlock/update' => [
        'path' => '/module/tools/content_blocks/update',
        'methods' => ['POST'],
        'target' => ContentBlocksController::class . '::updateAction',
    ],
];
