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

namespace TYPO3\CMS\ContentBlocks\Tests\Unit\Command;

use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Tester\CommandTester;
use TYPO3\CMS\ContentBlocks\Basics\BasicsLoader;
use TYPO3\CMS\ContentBlocks\Basics\BasicsRegistry;
use TYPO3\CMS\ContentBlocks\Command\LintContentBlocksCommand;
use TYPO3\CMS\ContentBlocks\CropVariants\CropVariantsLoader;
use TYPO3\CMS\ContentBlocks\CropVariants\CropVariantsRegistry;
use TYPO3\CMS\ContentBlocks\CropVariants\Exception\CropVariantReferenceException;
use TYPO3\CMS\ContentBlocks\JsonSchemaValidation\ContentBlockValidator;
use TYPO3\CMS\ContentBlocks\JsonSchemaValidation\JsonSchemaErrorFormatter;
use TYPO3\CMS\ContentBlocks\JsonSchemaValidation\JsonSchemaValidator;
use TYPO3\CMS\ContentBlocks\Loader\ContentBlockLoader;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class LintContentBlocksCommandTest extends UnitTestCase
{
    #[Test]
    public function rootErrorIsGatheredAlongsideNestedError(): void
    {
        $yaml = [
            'identifier' => 'Vendor/Basic',
            'unknownRootOption' => true,
            'fields' => [
                [
                    'identifier' => 'text',
                    'type' => 'Text',
                    'unknownOption' => true,
                ],
            ],
        ];
        $command = new LintContentBlocksCommand(
            $this->createMock(ContentBlockLoader::class),
            new ContentBlockValidator(new JsonSchemaValidator()),
            new JsonSchemaErrorFormatter(),
            $this->createMock(BasicsLoader::class),
            $this->createMock(CropVariantsLoader::class),
        );
        $validationResult = (new JsonSchemaValidator())->validateBasic(json_decode(json_encode($yaml)));

        $errors = (new \ReflectionMethod($command, 'gatherErrors'))->invoke($command, $validationResult, $yaml);

        $expected = [
            ['/fields/text', 'Additional object properties are not allowed: unknownOption'],
            ['/', 'Additional object properties are not allowed: unknownRootOption'],
        ];
        self::assertSame($expected, $errors);
    }

    #[Test]
    public function invalidCropVariantFilesAreReportedAsLintError(): void
    {
        $basicsLoader = $this->createMock(BasicsLoader::class);
        $basicsLoader->method('loadUncached')->willReturn(new BasicsRegistry());
        $cropVariantsLoader = $this->createMock(CropVariantsLoader::class);
        $cropVariantsLoader->method('loadUncached')->willThrowException(
            new \RuntimeException('Invalid Crop Variants file in "EXT:foo/ContentBlocks/CropVariants/Broken.yaml": Cannot find an identifier.', 1794501605)
        );
        $contentBlockLoader = $this->createMock(ContentBlockLoader::class);
        $contentBlockLoader->expects($this->never())->method('loadUncached');
        $command = new LintContentBlocksCommand(
            $contentBlockLoader,
            new ContentBlockValidator(new JsonSchemaValidator()),
            new JsonSchemaErrorFormatter(),
            $basicsLoader,
            $cropVariantsLoader,
        );
        $commandTester = new CommandTester($command);

        $exitCode = $commandTester->execute([]);

        self::assertSame(1, $exitCode);
        self::assertStringContainsString('Cannot find an identifier', $commandTester->getDisplay());
        self::assertStringContainsString('Found 1 errors', $commandTester->getDisplay());
    }

    #[Test]
    public function unknownCropVariantReferenceIsReportedAsLintError(): void
    {
        $basicsLoader = $this->createMock(BasicsLoader::class);
        $basicsLoader->method('loadUncached')->willReturn(new BasicsRegistry());
        $cropVariantsLoader = $this->createMock(CropVariantsLoader::class);
        $cropVariantsLoader->method('loadUncached')->willReturn(new CropVariantsRegistry());
        $contentBlockLoader = $this->createMock(ContentBlockLoader::class);
        $contentBlockLoader->method('loadUncached')->willThrowException(
            new CropVariantReferenceException('vendor/unknown', 'image', 'desktop', 'vendor/teaser')
        );
        $command = new LintContentBlocksCommand(
            $contentBlockLoader,
            new ContentBlockValidator(new JsonSchemaValidator()),
            new JsonSchemaErrorFormatter(),
            $basicsLoader,
            $cropVariantsLoader,
        );
        $commandTester = new CommandTester($command);

        $exitCode = $commandTester->execute([]);

        $display = $commandTester->getDisplay();
        self::assertSame(1, $exitCode);
        self::assertStringContainsString('vendor/teaser', $display);
        self::assertStringContainsString('/fields/image/cropVariants/desktop', $display);
        self::assertStringContainsString('Found 1 errors', $display);
    }
}
