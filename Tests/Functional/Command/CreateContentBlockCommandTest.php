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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class CreateContentBlockCommandTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'typo3conf/ext/content_blocks/Tests/Functional/Command/Fixtures/Extensions/command_test',
        'typo3conf/ext/content_blocks',
    ];

    #[Test]
    public function createContentElement(): void
    {
        $basePath = $this->instancePath . '/typo3conf/ext/command_test/ContentBlocks/ContentElements/command-test-1';

        // Verify content element direcotry does not already exists
        self::assertFileDoesNotExist($basePath, 'Content element directory already exists before running create command');

        // Create content element
        $commandTester = new CommandTester($this->get(CreateContentBlockCommand::class));
        $commandTester->execute(
            [
                '--content-type' => 'content-element',
                '--vendor' => 'typo3tests',
                '--name' => 'command-test-1',
                '--title' => 'Test 1',
                '--extension' => 'command_test',
            ],
            [
                'interactive' => false,
            ]
        );

        self::assertEquals(0, $commandTester->getStatusCode());

        // Verify all files exists now
        self::assertFileExists($basePath, 'Content element directory does not exists');
        self::assertFileExists($basePath . '/assets/icon.svg', 'Assets icon.svg does not exists');
        self::assertFileExists($basePath . '/language/labels.xlf', 'Language labels.xlf does not exists');
        self::assertFileExists($basePath . '/templates/backend-preview.html', 'Templates backend-preview.html does not exists');
        self::assertFileExists($basePath . '/templates/frontend.html', 'Templates frontend.html does not exists');
    }

    #[Test]
    public function invalidXmlCharactersAreEscaped(): void
    {
        // Create content block with invalid xml character '&' in title
        $commandTester = new CommandTester($this->get(CreateContentBlockCommand::class));
        $commandTester->execute(
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

        self::assertEquals(0, $commandTester->getStatusCode());

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
