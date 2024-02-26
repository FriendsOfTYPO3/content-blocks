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

namespace TYPO3\CMS\ContentBlocks\Tests\Functional\Generator;

use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\ContentBlocks\Generator\LanguageFileGenerator;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class LanguageFileGeneratorTest extends FunctionalTestCase
{
    protected bool $initializeDatabase = false;

    protected array $coreExtensionsToLoad = [
//        'content_blocks',
    ];

    protected array $testExtensionsToLoad = [
        'typo3conf/ext/content_blocks/Tests/Functional/Generator/Fixtures/Extensions/language_test',
        'typo3conf/ext/content_blocks',
    ];

    #[Test]
    public function generateWithEmptyLanguageFile(): void
    {
        $languageFileGenerator = $this->get(LanguageFileGenerator::class);
        $contentBlockRegistry = $this->get(ContentBlockRegistry::class);
        $contentBlock = $contentBlockRegistry->getContentBlock('language/test1');
        $result = $languageFileGenerator->generate($contentBlock, '');
        $expected = file_get_contents(__DIR__ . '/Fixtures/Language/1_EmptyLanguageFile.xlf');
        self::assertSame($expected, $result);
    }

    #[Test]
    public function generateWithEmptyLanguageFileWithLabels(): void
    {
        $languageFileGenerator = $this->get(LanguageFileGenerator::class);
        $contentBlockRegistry = $this->get(ContentBlockRegistry::class);
        $contentBlock = $contentBlockRegistry->getContentBlock('language/test2');
        $result = $languageFileGenerator->generate($contentBlock, '');
        $expected = file_get_contents(__DIR__ . '/Fixtures/Language/2_EmptyLanguageFileWithLabels.xlf');
        self::assertSame($expected, $result);
    }

    #[Test]
    public function generateWithLanguageFileWithoutLabels(): void
    {
        $languageFileGenerator = $this->get(LanguageFileGenerator::class);
        $contentBlockRegistry = $this->get(ContentBlockRegistry::class);
        $contentBlock = $contentBlockRegistry->getContentBlock('language/test3');
        $result = $languageFileGenerator->generate($contentBlock, '');
        $expected = file_get_contents(__DIR__ . '/Fixtures/Language/3_LanguageFileWithoutLabels.xlf');
        self::assertSame($expected, $result);
    }

    #[Test]
    public function generateWithLanguageFileWitLabels(): void
    {
        $languageFileGenerator = $this->get(LanguageFileGenerator::class);
        $contentBlockRegistry = $this->get(ContentBlockRegistry::class);
        $contentBlock = $contentBlockRegistry->getContentBlock('language/test4');
        $result = $languageFileGenerator->generate($contentBlock, '');
        $expected = file_get_contents(__DIR__ . '/Fixtures/Language/4_LanguageFileWithLabels.xlf');
        self::assertSame($expected, $result);
    }

    #[Test]
    public function generateWithLanguageFileWitLabelsWithCustomTranslation(): void
    {
        $languageFileGenerator = $this->get(LanguageFileGenerator::class);
        $contentBlockRegistry = $this->get(ContentBlockRegistry::class);
        $contentBlock = $contentBlockRegistry->getContentBlock('language/test5');
        $result = $languageFileGenerator->generate($contentBlock, '');
        $expected = file_get_contents(__DIR__ . '/Fixtures/Language/5_LanguageFileWithLabelsWithCustomTranslations.xlf');
        self::assertSame($expected, $result);
    }

    #[Test]
    public function generateWithLanguageFileWitLabelsWithDescriptions(): void
    {
        $languageFileGenerator = $this->get(LanguageFileGenerator::class);
        $contentBlockRegistry = $this->get(ContentBlockRegistry::class);
        $contentBlock = $contentBlockRegistry->getContentBlock('language/test6');
        $result = $languageFileGenerator->generate($contentBlock, '');
        $expected = file_get_contents(__DIR__ . '/Fixtures/Language/6_LanguageFileWithLabelsWithDescriptions.xlf');
        self::assertSame($expected, $result);
    }

    #[Test]
    public function generateWithLanguageFileWithoutLabelsWithoutDescriptions(): void
    {
        $languageFileGenerator = $this->get(LanguageFileGenerator::class);
        $contentBlockRegistry = $this->get(ContentBlockRegistry::class);
        $contentBlock = $contentBlockRegistry->getContentBlock('language/test7');
        $result = $languageFileGenerator->generate($contentBlock, '');
        $expected = file_get_contents(__DIR__ . '/Fixtures/Language/7_LanguageFileWithoutLabelsWithoutDescriptions.xlf');
        self::assertSame($expected, $result);
    }
}
