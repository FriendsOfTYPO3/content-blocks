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

namespace TYPO3\CMS\ContentBlocks\Tests\Functional\ViewHelpers;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextFactory;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use TYPO3Fluid\Fluid\Exception;
use TYPO3Fluid\Fluid\View\TemplateView;

final class LanguagePathViewHelperTest extends FunctionalTestCase
{
    protected bool $initializeDatabase = false;

    protected array $testExtensionsToLoad = [
        'typo3conf/ext/content_blocks/Tests/Fixtures/Extensions/test_content_blocks_b',
        'typo3conf/ext/content_blocks',
    ];

    public static function renderReturnsStringDataProvider(): array
    {
        return [
            'name explicitly set' => [
                '<cb:languagePath name="typo3tests/content-element-b" />',
                'LLL:EXT:test_content_blocks_b/ContentBlocks/ContentElements/content-element-b/language/labels.xlf',
            ],
            'fallback to name from context' => [
                '<cb:languagePath />',
                'LLL:EXT:test_content_blocks_b/ContentBlocks/ContentElements/content-element-b/language/labels.xlf',
            ],
        ];
    }

    #[DataProvider('renderReturnsStringDataProvider')]
    #[Test]
    public function renderReturnsString(string $template, string $expected): void
    {
        $GLOBALS['LANG'] = GeneralUtility::makeInstance(LanguageServiceFactory::class)->create('default');
        $context = $this->get(RenderingContextFactory::class)->create();
        $context->getTemplatePaths()->setTemplateSource($template);
        $view = new TemplateView($context);
        $view->assign('settings', ['_content_block_name' => 'typo3tests/content-element-b']);
        self::assertSame($expected, $view->render());
    }

    #[Test]
    public function invalidContentBlockThrowsFluidException(): void
    {
        $GLOBALS['LANG'] = GeneralUtility::makeInstance(LanguageServiceFactory::class)->create('default');
        $context = $this->get(RenderingContextFactory::class)->create();
        $context->getTemplatePaths()->setTemplateSource('<cb:languagePath name="fizz/buzz" />');

        $this->expectException(Exception::class);
        $this->expectExceptionCode(1699272189);

        (new TemplateView($context))->render();
    }

    #[Test]
    public function missingNameThrowsFluidException(): void
    {
        $GLOBALS['LANG'] = GeneralUtility::makeInstance(LanguageServiceFactory::class)->create('default');
        $context = $this->get(RenderingContextFactory::class)->create();
        $context->getTemplatePaths()->setTemplateSource('<cb:languagePath />');

        $this->expectException(Exception::class);
        $this->expectExceptionCode(1699271759);

        (new TemplateView($context))->render();
    }
}
