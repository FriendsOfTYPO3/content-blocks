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
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use TYPO3\CMS\ContentBlocks\Command\LintContentBlocksCommand;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class LintContentBlocksCommandTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'typo3conf/ext/content_blocks/Tests/Fixtures/Extensions/test_content_blocks_e',
        'typo3conf/ext/content_blocks',
    ];

    #[Test]
    public function invalidTabFilesAreReported(): void
    {
        $commandTester = new CommandTester($this->get(LintContentBlocksCommand::class));
        $commandTester->execute([]);
        $display = $commandTester->getDisplay();

        self::assertSame(Command::FAILURE, $commandTester->getStatusCode());
        self::assertStringContainsString('typo3tests/invalid-tab tabs/settings.yaml | EXT:test_content_blocks_e', $display);
        self::assertStringContainsString('/fields/setting', $display);
        self::assertStringContainsString('typo3tests/invalid-tab-property tabs/settings.yaml | EXT:test_content_blocks_e', $display);
        self::assertStringContainsString('Additional object properties are not allowed: title', $display);
        self::assertStringNotContainsString('typo3tests/valid-tab', $display);
        self::assertStringContainsString('Found 2 errors', $display);
    }
}
