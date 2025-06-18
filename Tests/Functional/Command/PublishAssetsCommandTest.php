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
use TYPO3\CMS\ContentBlocks\Command\PublishAssetsCommand;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class PublishAssetsCommandTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'typo3conf/ext/content_blocks/Tests/Functional/Command/Fixtures/Extensions/command_test',
        'typo3conf/ext/content_blocks',
    ];

    #[Test]
    public function publishAssets(): void
    {
        // Delete all published assets
        if (is_dir($this->instancePath . '/typo3conf/ext/command_test/Resources/Public/ContentBlocks/typo3tests')) {
            GeneralUtility::rmdir($this->instancePath . '/typo3conf/ext/command_test/Resources/Public/ContentBlocks/typo3tests', true);
        }

        $iconPath = $this->instancePath . '/typo3conf/ext/command_test/Resources/Public/ContentBlocks/typo3tests/command-test-assets/icon.svg';

        // Verify icon.svg does not already exists
        self::assertFileDoesNotExist($iconPath, 'icon.svg already exists before running publish assets command');

        // Publish assets
        $commandTester = new CommandTester($this->get(PublishAssetsCommand::class));
        $commandTester->execute([]);

        self::assertEquals(0, $commandTester->getStatusCode());

        // Verify icon.svg now exists
        self::assertFileExists($iconPath, 'icon.svg does not exists');
    }

    protected function tearDown(): void
    {
        // Delete all published assets
        if (is_dir($this->instancePath . '/typo3conf/ext/command_test/Resources/Public/ContentBlocks/typo3tests')) {
            GeneralUtility::rmdir($this->instancePath . '/typo3conf/ext/command_test/Resources/Public/ContentBlocks/typo3tests', true);
        }
        parent::tearDown();
    }
}
