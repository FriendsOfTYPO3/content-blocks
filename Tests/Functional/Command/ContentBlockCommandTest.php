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

namespace TYPO3\CMS\ContentBlocks\Tests\Functional\Frontend;

use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Tester\CommandTester;
use TYPO3\CMS\Core\Authentication\CommandLineUserAuthentication;
use TYPO3\CMS\Core\Command\CacheWarmupCommand;
use TYPO3\CMS\Core\Command\SetupExtensionsCommand;
use TYPO3\CMS\Core\Core\Bootstrap;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class ContentBlockCommandTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'typo3conf/ext/content_blocks/Tests/Fixtures/Extensions/test_content_blocks_b',
        'typo3conf/ext/content_blocks/Tests/Fixtures/Extensions/test_content_blocks_c',
        'typo3conf/ext/content_blocks',
    ];

    public function setUp(): void
    {
        parent::setUp();
    }

    #[Test]
    public function extensionSetupCacheTest(): void
    {
        // Warmup the cache to reproduce cache issue
        $commandTesterCacheWarmup = new CommandTester($this->get(CacheWarmupCommand::class));
        $commandTesterCacheWarmup->execute([]);

        self::assertEquals(0, $commandTesterCacheWarmup->getStatusCode());

        $this->setUp();

        // Run extension:setup
        Bootstrap::initializeBackendUser(CommandLineUserAuthentication::class);
        $commandTesterSetupExtension = new CommandTester($this->get(SetupExtensionsCommand::class));
        $commandTesterSetupExtension->execute([]);

        self::assertEquals(0, $commandTesterSetupExtension->getStatusCode());
    }
}
