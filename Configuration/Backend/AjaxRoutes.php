<?php

use TYPO3\CMS\ContentBlocks\BackendController\ContentBlocksAjaxController;

return [
    ContentBlocksAjaxController::ROUTE_IDENTIFIER . '/contentBlocks/list/json' => [
        'path' => '/module/tools/content_blocks/list',
        'target' => ContentBlocksAjaxController::class . '::jsonContentBlocksListAction',
    ],
    ContentBlocksAjaxController::ROUTE_IDENTIFIER . '/contentBlock/get/json' => [
        'path' => '/module/tools/content_blocks/get',
        'target' => ContentBlocksAjaxController::class . '::jsonContentBlockGetAction',
    ],
];
