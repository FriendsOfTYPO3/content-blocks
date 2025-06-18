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
        $basePath = $this->instancePath . '/typo3conf/ext/command_test/ContentBlocks/ContentElements/command-test-content-element';

        // Verify content element direcotry does not already exists
        self::assertFileDoesNotExist($basePath, 'Content element directory already exists before running create command');

        // Create content element
        $commandTester = new CommandTester($this->get(CreateContentBlockCommand::class));
        $commandTester->execute(
            [
                '--content-type' => 'content-element',
                '--vendor' => 'typo3tests',
                '--name' => 'command-test-content-element',
                '--title' => 'Test Content Element',
                '--extension' => 'command_test',
            ],
            [
                'interactive' => false,
            ]
        );

        self::assertEquals(0, $commandTester->getStatusCode());

        // Verify all files exists now
        self::assertFileExists($basePath, 'Content element directory does not exists');
        self::assertFileExists($basePath . '/config.yaml', 'config.yaml does not exists');
        self::assertFileExists($basePath . '/assets/icon.svg', 'Assets icon.svg does not exists');
        self::assertFileExists($basePath . '/language/labels.xlf', 'Language labels.xlf does not exists');
        self::assertFileExists($basePath . '/templates/backend-preview.html', 'Templates backend-preview.html does not exists');
        self::assertFileExists($basePath . '/templates/frontend.html', 'Templates frontend.html does not exists');
    }

    #[Test]
    public function createPageType(): void
    {
        $basePath = $this->instancePath . '/typo3conf/ext/command_test/ContentBlocks/PageTypes/command-test-page-type';

        // Verify content element direcotry does not already exists
        self::assertFileDoesNotExist($basePath, 'Page type directory already exists before running create command');

        // Create content element
        $commandTester = new CommandTester($this->get(CreateContentBlockCommand::class));
        $commandTester->execute(
            [
                '--content-type' => 'page-type',
                '--vendor' => 'typo3tests',
                '--name' => 'command-test-page-type',
                '--title' => 'Test Page Type',
                '--extension' => 'command_test',
            ],
            [
                'interactive' => false,
            ]
        );

        self::assertEquals(0, $commandTester->getStatusCode());

        // Verify all files exists now
        self::assertFileExists($basePath, 'Page type directory does not exists');
        self::assertFileExists($basePath . '/config.yaml', 'config.yaml does not exists');
        self::assertFileExists($basePath . '/assets/icon-hide-in-menu.svg', 'Assets icon-hide-in-menu.svg does not exists');
        self::assertFileExists($basePath . '/assets/icon.svg', 'Assets icon.svg does not exists');
        self::assertFileExists($basePath . '/language/labels.xlf', 'Language labels.xlf does not exists');
        self::assertFileExists($basePath . '/templates/backend-preview.html', 'Templates backend-preview.html does not exists');
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
                '--name' => 'command-test-invalid-characters-are-escaped',
                '--title' => 'Test Invalid Characters & Description',
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
        $contentBlock = $contentBlockRegistry->getContentBlock('typo3tests/command-test-invalid-characters-are-escaped');
        $result = $languageFileGenerator->generate($contentBlock, '');

        $expected = file_get_contents(__DIR__ . '/Fixtures/Language/InvalidXmlCharactersAreEscaped.xlf');

        self::assertSame($expected, $result);
    }

    protected function tearDown(): void
    {
        if (is_dir($this->instancePath . '/typo3conf/ext/command_test/ContentBlocks/ContentElements/command-test-content-element')) {
            GeneralUtility::rmdir($this->instancePath . '/typo3conf/ext/command_test/ContentBlocks/ContentElements/command-test-content-element', true);
        }
        if (is_dir($this->instancePath . '/typo3conf/ext/command_test/ContentBlocks/ContentElements/command-test-invalid-characters-are-escaped')) {
            GeneralUtility::rmdir($this->instancePath . '/typo3conf/ext/command_test/ContentBlocks/ContentElements/command-test-invalid-characters-are-escaped', true);
        }
        if (is_dir($this->instancePath . '/typo3conf/ext/command_test/ContentBlocks/PageTypes/command-test-page-type')) {
            GeneralUtility::rmdir($this->instancePath . '/typo3conf/ext/command_test/ContentBlocks/PageTypes/command-test-page-type', true);
        }
        // Delete all published assets
        if (is_dir($this->instancePath . '/typo3conf/ext/command_test/Resources/Public/ContentBlocks/typo3tests')) {
            GeneralUtility::rmdir($this->instancePath . '/typo3conf/ext/command_test/Resources/Public/ContentBlocks/typo3tests', true);
        }
        parent::tearDown();
    }
}
