<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace TYPO3\CMS\ContentBlocks\EventListener;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use TYPO3\CMS\ContentBlocks\Generator\UserTsConfigGenerator;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Cache\Frontend\PhpFrontend;
use TYPO3\CMS\Core\TypoScript\IncludeTree\Event\BeforeLoadedUserTsConfigEvent;

readonly class AddUserTsConfig
{
    public function __construct(
        /** @var PhpFrontend */
        #[Autowire(service: 'cache.core')]
        protected FrontendInterface $cache,
        protected UserTsConfigGenerator $userTsConfigGenerator,
    ) {}

    #[AsEventListener(identifier: 'content-blocks-user-ts-config')]
    public function __invoke(BeforeLoadedUserTsConfigEvent $event): void
    {
        $userTsConfig = $this->create();
        $event->addTsConfig($userTsConfig);
    }

    protected function create(): string
    {
        $typoScriptFromCache = $this->cache->require('ContentBlocks_UserTsConfig');
        if ($typoScriptFromCache !== false) {
            return $typoScriptFromCache;
        }
        $userTsConfig = $this->userTsConfigGenerator->generate();
        $this->cache->set('ContentBlocks_UserTsConfig', 'return ' . var_export($userTsConfig, true) . ';');
        return $userTsConfig;
    }
}
