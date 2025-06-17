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
use TYPO3\CMS\ContentBlocks\Command\CreateContentBlockCommand;
use TYPO3\CMS\ContentBlocks\Generator\LanguageFileGenerator;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\Core\Authentication\CommandLineUserAuthentication;
use TYPO3\CMS\Core\Command\CacheFlushCommand;
use TYPO3\CMS\Core\Command\CacheWarmupCommand;
use TYPO3\CMS\Core\Command\SetupExtensionsCommand;
use TYPO3\CMS\Core\Core\Bootstrap;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class ContentBlockCommandTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'typo3conf/ext/content_blocks/Tests/Fixtures/Extensions/test_content_blocks_b',
        'typo3conf/ext/content_blocks/Tests/Fixtures/Extensions/test_content_blocks_c',
        'typo3conf/ext/content_blocks/Tests/Functional/Command/Fixtures/Extensions/command_test',
        'typo3conf/ext/content_blocks',
    ];

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

    #[Test]
    public function createContentBlockTitleSpecialCharsTest(): void
    {
        // Create content block with '&' in title
        $commandTesterCreateContentBlock = new CommandTester($this->get(CreateContentBlockCommand::class));
        $commandTesterCreateContentBlock->execute(
            [
                '--content-type' => 'content-element',
                '--vendor' => 'typo3tests',
                '--name' => 'command-test-1',
                '--title' => 'Test 1 & Description',
                '--extension' => 'command_test',
            ],
            [
                'interactive' => false,
            ]
        );

        self::assertEquals(0, $commandTesterCreateContentBlock->getStatusCode());

        // Flush cache
        $commandTesterCacheWarmup = new CommandTester($this->get(CacheFlushCommand::class));
        $commandTesterCacheWarmup->execute([]);

        // Verify labels.xlf
        $languageFileGenerator = $this->get(LanguageFileGenerator::class);
        $contentBlockRegistry = $this->get(ContentBlockRegistry::class);
        $contentBlock = $contentBlockRegistry->getContentBlock('typo3tests/command-test-1');
        $result = $languageFileGenerator->generate($contentBlock, '');

        $expected = file_get_contents(__DIR__ . '/Fixtures/Language/1_InvalidXmlCharactersAreEscaped.xlf');

        self::assertSame($expected, $result);
    }

    protected function tearDown(): void
    {
        if (is_dir($this->instancePath . '/typo3conf/ext/command_test/ContentBlocks/ContentElements/command-test-1')) {
            GeneralUtility::rmdir($this->instancePath . '/typo3conf/ext/command_test/ContentBlocks/ContentElements/command-test-1', true);
        }
        if (is_dir($this->instancePath . '/typo3conf/ext/command_test/Resources/Public/ContentBlocks/typo3tests/command-test-1')) {
            GeneralUtility::rmdir($this->instancePath . '/typo3conf/ext/command_test/Resources/Public/ContentBlocks/typo3tests/command-test-1', true);
        }
        parent::tearDown();
    }
}
