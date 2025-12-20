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
use TYPO3\CMS\ContentBlocks\Generator\TypoScriptGenerator;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Cache\Frontend\PhpFrontend;
use TYPO3\CMS\Core\Core\Event\BootCompletedEvent;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * @internal Not part of TYPO3's public API.
 */
readonly class AddDefaultTypoScript
{
    public function __construct(
        /** @var PhpFrontend */
        #[Autowire(service: 'cache.core')]
        protected FrontendInterface $cache,
        protected TypoScriptGenerator $typoScriptGenerator,
    ) {}

    #[AsEventListener(identifier: 'content-blocks-typoscript')]
    public function __invoke(BootCompletedEvent $event): void
    {
        $typoScript = $this->create();
        ExtensionManagementUtility::addTypoScriptSetup($typoScript);
    }

    protected function create(): string
    {
        $typoScriptFromCache = $this->cache->require('ContentBlocks_Typoscript');
        if ($typoScriptFromCache !== false) {
            return $typoScriptFromCache;
        }
        $typoScript = $this->typoScriptGenerator->generate();
        $this->cache->set('ContentBlocks_Typoscript', 'return ' . var_export($typoScript, true) . ';');
        return $typoScript;
    }
}
