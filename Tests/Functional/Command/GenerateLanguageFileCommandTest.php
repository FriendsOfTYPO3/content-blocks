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
use TYPO3\CMS\ContentBlocks\Command\GenerateLanguageFileCommand;
use TYPO3\CMS\ContentBlocks\Generator\LanguageFileGenerator;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class GenerateLanguageFileCommandTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'typo3conf/ext/content_blocks/Tests/Functional/Command/Fixtures/Extensions/command_test',
        'typo3conf/ext/content_blocks',
    ];

    protected function tearDown(): void
    {
        $contentElementsPath = $this->instancePath . '/typo3conf/ext/command_test/ContentBlocks/ContentElements';
        GeneralUtility::rmdir($contentElementsPath . '/command-test-language-1/language', true);
        GeneralUtility::rmdir($contentElementsPath . '/command-test-language-2/language', true);
        GeneralUtility::rmdir($contentElementsPath . '/command-test-assets/language', true);
        parent::tearDown();
    }

    #[Test]
    public function generateWithContentBlock(): void
    {
        $labelPath = $this->instancePath . '/typo3conf/ext/command_test/ContentBlocks/ContentElements/command-test-language-1/language/labels.xlf';

        // Verify labels.xlf does not already exist
        self::assertFileDoesNotExist($labelPath, 'label.xlf already exists before running create command');

        // Generate label.xlf
        $commandTester = new CommandTester($this->get(GenerateLanguageFileCommand::class));
        $commandTester->execute(
            [
                'content-block' => 'typo3tests/command-test-language-1',
            ]
        );

        self::assertSame(0, $commandTester->getStatusCode());

        // Verify labels.xlf now exists
        self::assertFileExists($labelPath, 'label.xlf does not exist');

        // Verify label.xlf
        $languageFileGenerator = $this->get(LanguageFileGenerator::class);
        $contentBlockRegistry = $this->get(ContentBlockRegistry::class);
        $contentBlock = $contentBlockRegistry->getContentBlock('typo3tests/command-test-language-1');
        $result = $languageFileGenerator->generate($contentBlock, '');

        $expected = file_get_contents(__DIR__ . '/Fixtures/Language/1_GenerateLanguageFileGenerateWithContentBlock.xlf');

        self::assertSame($expected, $result);
    }

    #[Test]
    public function generateWithExtension(): void
    {
        $labelPath = $this->instancePath . '/typo3conf/ext/command_test/ContentBlocks/ContentElements/command-test-language-2/language/labels.xlf';

        // Verify labels.xlf does not already exist
        self::assertFileDoesNotExist($labelPath, 'label.xlf already exists before running create command');

        // Generate label.xlf
        $commandTester = new CommandTester($this->get(GenerateLanguageFileCommand::class));
        $commandTester->execute(
            [
                '--extension' => 'command_test',
            ]
        );

        self::assertSame(0, $commandTester->getStatusCode());

        // Verify labels.xlf now exists
        self::assertFileExists($labelPath, 'label.xlf does not exist');

        // Verify label.xlf
        $languageFileGenerator = $this->get(LanguageFileGenerator::class);
        $contentBlockRegistry = $this->get(ContentBlockRegistry::class);
        $contentBlock = $contentBlockRegistry->getContentBlock('typo3tests/command-test-language-2');
        $result = $languageFileGenerator->generate($contentBlock, '');

        $expected = file_get_contents(__DIR__ . '/Fixtures/Language/2_GenerateLanguageFileGenerateWithExtension.xlf');

        self::assertSame($expected, $result);
    }

    #[Test]
    public function printWithContentBlock(): void
    {
        // Print label.xlf
        $commandTester = new CommandTester($this->get(GenerateLanguageFileCommand::class));
        $commandTester->execute(
            [
                'content-block' => 'typo3tests/command-test-language-1',
                '--print' => true,
            ]
        );

        self::assertSame(0, $commandTester->getStatusCode());

        $result = $commandTester->getDisplay();

        // Remove date attribute value in output $result
        $resultForCompare = preg_replace('/date="[^"]*"/', 'date=""', $result);

        $expected = file_get_contents(__DIR__ . '/Fixtures/Language/3_GenerateLanguageFilePrintWithContentBlock.xlf');

        self::assertSame($expected, $resultForCompare);
    }
}
