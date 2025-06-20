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
use TYPO3\CMS\ContentBlocks\Command\ListContentBlocksCommand;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class ListContentBlocksCommandTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'typo3conf/ext/content_blocks/Tests/Fixtures/Extensions/test_content_blocks_a',
        'typo3conf/ext/content_blocks/Tests/Fixtures/Extensions/test_content_blocks_b',
        'typo3conf/ext/content_blocks/Tests/Fixtures/Extensions/test_content_blocks_c',
        'typo3conf/ext/content_blocks',
    ];

    #[Test]
    public function listWithDefaultOrder(): void
    {
        $commandTester = new CommandTester($this->get(ListContentBlocksCommand::class));
        $commandTester->execute([]);

        self::assertSame(0, $commandTester->getStatusCode());

        $expected = file_get_contents(__DIR__ . '/Fixtures/Table/1_ListWithDefaultOrder.txt');

        self::assertSame($expected, $commandTester->getDisplay());
    }

    #[Test]
    public function listWithVendorOrder(): void
    {
        $commandTester = new CommandTester($this->get(ListContentBlocksCommand::class));
        $commandTester->execute([
            '--order' => 'vendor',
        ]);

        self::assertSame(0, $commandTester->getStatusCode());

        $expected = file_get_contents(__DIR__ . '/Fixtures/Table/2_ListWithVendorOrder.txt');

        self::assertSame($expected, $commandTester->getDisplay());
    }

    #[Test]
    public function listWithNameOrder(): void
    {
        $commandTester = new CommandTester($this->get(ListContentBlocksCommand::class));
        $commandTester->execute([
            '--order' => 'name',
        ]);

        self::assertSame(0, $commandTester->getStatusCode());

        $expected = file_get_contents(__DIR__ . '/Fixtures/Table/3_ListWithNameOrder.txt');

        self::assertSame($expected, $commandTester->getDisplay());
    }

    #[Test]
    public function listWithTableOrder(): void
    {
        $commandTester = new CommandTester($this->get(ListContentBlocksCommand::class));
        $commandTester->execute([
            '--order' => 'table',
        ]);

        self::assertSame(0, $commandTester->getStatusCode());

        $expected = file_get_contents(__DIR__ . '/Fixtures/Table/4_ListWithTableOrder.txt');

        self::assertSame($expected, $commandTester->getDisplay());
    }

    #[Test]
    public function listWithTypeNameOrder(): void
    {
        $commandTester = new CommandTester($this->get(ListContentBlocksCommand::class));
        $commandTester->execute([
            '--order' => 'type-name',
        ]);

        self::assertSame(0, $commandTester->getStatusCode());

        $expected = file_get_contents(__DIR__ . '/Fixtures/Table/5_ListWithTypeNameOrder.txt');

        self::assertSame($expected, $commandTester->getDisplay());
    }

    #[Test]
    public function listWithContentTypeOrder(): void
    {
        $commandTester = new CommandTester($this->get(ListContentBlocksCommand::class));
        $commandTester->execute([
            '--order' => 'content-type',
        ]);

        self::assertSame(0, $commandTester->getStatusCode());

        $expected = file_get_contents(__DIR__ . '/Fixtures/Table/6_ListWithContentTypeOrder.txt');

        self::assertSame($expected, $commandTester->getDisplay());
    }

    #[Test]
    public function listWithExtensionOrder(): void
    {
        $commandTester = new CommandTester($this->get(ListContentBlocksCommand::class));
        $commandTester->execute([
            '--order' => 'extension',
        ]);

        self::assertSame(0, $commandTester->getStatusCode());

        $expected = file_get_contents(__DIR__ . '/Fixtures/Table/7_ListWithExtensionOrder.txt');

        self::assertSame($expected, $commandTester->getDisplay());
    }
}
