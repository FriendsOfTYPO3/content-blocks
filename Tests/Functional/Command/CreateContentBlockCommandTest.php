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

    protected function tearDown(): void
    {
        $contentBlocksPath = $this->instancePath . '/typo3conf/ext/command_test/ContentBlocks';
        GeneralUtility::rmdir($contentBlocksPath . '/ContentElements/command-test-content-element', true);
        GeneralUtility::rmdir($contentBlocksPath . '/ContentElements/command-test-invalid-characters-are-escaped', true);
        GeneralUtility::rmdir($contentBlocksPath . '/PageTypes/command-test-page-type', true);
        GeneralUtility::rmdir($contentBlocksPath . '/RecordTypes/command-test-record-type', true);
        // Delete all published assets
        GeneralUtility::rmdir($this->instancePath . '/typo3conf/ext/command_test/Resources/Public/ContentBlocks/typo3tests', true);
        parent::tearDown();
    }

    #[Test]
    public function createContentElement(): void
    {
        $basePath = $this->instancePath . '/typo3conf/ext/command_test/ContentBlocks/ContentElements/command-test-content-element';

        // Verify content element directory does not already exist
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

        self::assertSame(0, $commandTester->getStatusCode());

        // Verify all files exists now
        self::assertFileExists($basePath, 'Content element directory does not exist');
        self::assertFileExists($basePath . '/config.yaml', 'config.yaml does not exist');
        self::assertFileExists($basePath . '/assets/icon.svg', 'Assets icon.svg does not exist');
        self::assertFileExists($basePath . '/language/labels.xlf', 'Language labels.xlf does not exist');
        self::assertFileExists($basePath . '/templates/backend-preview.fluid.html', 'Templates backend-preview.fluid.html does not exist');
        self::assertFileExists($basePath . '/templates/frontend.fluid.html', 'Templates frontend.fluid.html does not exist');
    }

    #[Test]
    public function createPageType(): void
    {
        $basePath = $this->instancePath . '/typo3conf/ext/command_test/ContentBlocks/PageTypes/command-test-page-type';

        // Verify page type directory does not already exist
        self::assertFileDoesNotExist($basePath, 'Page type directory already exists before running create command');

        // Create page type
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

        self::assertSame(0, $commandTester->getStatusCode());

        // Verify all files exists now
        self::assertFileExists($basePath, 'Page type directory does not exist');
        self::assertFileExists($basePath . '/config.yaml', 'config.yaml does not exist');
        self::assertFileExists($basePath . '/assets/icon-hide-in-menu.svg', 'Assets icon-hide-in-menu.svg does not exist');
        self::assertFileExists($basePath . '/assets/icon.svg', 'Assets icon.svg does not exist');
        self::assertFileExists($basePath . '/language/labels.xlf', 'Language labels.xlf does not exist');
        self::assertFileExists($basePath . '/templates/backend-preview.fluid.html', 'Templates backend-preview.fluid.html does not exist');
    }

    #[Test]
    public function createRecordType(): void
    {
        $basePath = $this->instancePath . '/typo3conf/ext/command_test/ContentBlocks/RecordTypes/command-test-record-type';

        // Verify record type directory does not already exist
        self::assertFileDoesNotExist($basePath, 'Record type directory already exists before running create command');

        // Create record type
        $commandTester = new CommandTester($this->get(CreateContentBlockCommand::class));
        $commandTester->execute(
            [
                '--content-type' => 'record-type',
                '--vendor' => 'typo3tests',
                '--name' => 'command-test-record-type',
                '--title' => 'Test Record Type',
                '--extension' => 'command_test',
            ],
            [
                'interactive' => false,
            ]
        );

        self::assertSame(0, $commandTester->getStatusCode());

        // Verify all files exists now
        self::assertFileExists($basePath, 'Page type directory does not exist');
        self::assertFileExists($basePath . '/config.yaml', 'config.yaml does not exist');
        self::assertFileExists($basePath . '/assets/icon.svg', 'Assets icon.svg does not exist');
        self::assertFileExists($basePath . '/language/labels.xlf', 'Language labels.xlf does not exist');
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

        self::assertSame(0, $commandTester->getStatusCode());

        // Verify labels.xlf
        $languageFileGenerator = $this->get(LanguageFileGenerator::class);
        $contentBlockRegistry = $this->get(ContentBlockRegistry::class);
        $contentBlock = $contentBlockRegistry->getContentBlock('typo3tests/command-test-invalid-characters-are-escaped');
        $result = $languageFileGenerator->generate($contentBlock, '');

        $expected = file_get_contents(__DIR__ . '/Fixtures/Language/InvalidXmlCharactersAreEscaped.xlf');

        self::assertSame($expected, $result);
    }
}
