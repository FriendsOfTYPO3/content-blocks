<?php

declare(strict_types=1);

namespace ContentBlocks\Examples\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class StandardController extends ActionController
{
    public function defaultAction(): ResponseInterface
    {
        $this->view->assign('var', 'from plugin');
        return $this->htmlResponse();
    }
}
