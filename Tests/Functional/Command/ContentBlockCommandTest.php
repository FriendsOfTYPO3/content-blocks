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

namespace TYPO3\CMS\ContentBlocks\Tests\Functional\Command;

use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Tester\CommandTester;
use TYPO3\CMS\Core\Authentication\CommandLineUserAuthentication;
use TYPO3\CMS\Core\Command\SetupExtensionsCommand;
use TYPO3\CMS\Core\Core\Bootstrap;
use TYPO3\CMS\Core\Tests\Functional\SiteHandling\SiteBasedTestTrait;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class ContentBlockCommandTest extends FunctionalTestCase
{
    use SiteBasedTestTrait;

    protected array $testExtensionsToLoad = [
        'typo3conf/ext/content_blocks/Tests/Fixtures/Extensions/test_content_blocks_b',
        'typo3conf/ext/content_blocks/Tests/Fixtures/Extensions/test_content_blocks_c',
        'typo3conf/ext/content_blocks',
    ];

    protected const LANGUAGE_PRESETS = [
        'EN' => ['id' => 0, 'title' => 'English', 'locale' => 'en_US.UTF8'],
    ];
    protected const ROOT_PAGE_ID = 1;

    public function setUp(): void
    {
        parent::setUp();
    }

    #[Test]
    public function extensionSetupCacheTest(): void
    {
        // Warmup cache with internal call
        $this->importCSVDataSet(__DIR__ . '/../Frontend/Fixtures/DataSet/base.csv');
        $this->writeSiteConfiguration(
            'fluid_template',
            $this->buildSiteConfiguration(self::ROOT_PAGE_ID, '/'),
        );

        $this->importCSVDataSet(__DIR__ . '/../Frontend/Fixtures/DataSet/frontend_simple_element.csv');
        $this->setUpFrontendRootPage(
            self::ROOT_PAGE_ID,
            [
                'EXT:content_blocks/Tests/Functional/Frontend/Fixtures/frontend.typoscript',
            ]
        );
        $response = $this->executeFrontendSubRequest((new InternalRequest())->withPageId(self::ROOT_PAGE_ID));
        $html = (string)$response->getBody();
        self::assertStringContainsString('HeaderSimple', $html);

        // Run extension:setup
        Bootstrap::initializeBackendUser(CommandLineUserAuthentication::class);

        // Use CommandTester to execute the command
        $commandTester = new CommandTester($this->get(SetupExtensionsCommand::class));
        $commandTester->execute([]);

        print_r($commandTester->getDisplay());

        self::assertEquals(0, $commandTester->getStatusCode());
    }
}
