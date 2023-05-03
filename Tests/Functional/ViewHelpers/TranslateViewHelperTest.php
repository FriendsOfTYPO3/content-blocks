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

use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextFactory;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use TYPO3Fluid\Fluid\View\TemplateView;

class TranslateViewHelperTest extends FunctionalTestCase
{
    protected bool $initializeDatabase = false;

    protected array $coreExtensionsToLoad = [
        'content_blocks',
    ];

    protected array $testExtensionsToLoad = [
        'typo3/sysext/content_blocks/Tests/Fixtures/Extensions/foo',
    ];

    public static function renderReturnsStringDataProvider(): array
    {
        return [
            'fallback to default attribute for not existing label' => [
                '<cb:translate name="foo/bar" key="iDoNotExist" default="myDefault" />',
                'myDefault',
            ],
            'fallback to default attribute for static label' => [
                '<cb:translate name="foo/bar" key="static label" default="myDefault" />',
                'myDefault',
            ],
            'fallback to child for not existing label' => [
                '<cb:translate name="foo/bar" key="iDoNotExist">myDefault</cb:translate>',
                'myDefault',
            ],
            'fallback to child for static label' => [
                '<cb:translate name="foo/bar" key="static label">myDefault</cb:translate>',
                'myDefault',
            ],
            'key and name given' => [
                '<cb:translate key="foo.bar.title" name="foo/bar" />',
                'Content Block title',
            ],
            'empty string on invalid content block' => [
                '<cb:translate key="dummy" name="fizz/buzz" />',
                '',
            ],
        ];
    }

    /**
     * @test
     * @dataProvider renderReturnsStringDataProvider
     */
    public function renderReturnsString(string $template, string $expected): void
    {
        $GLOBALS['LANG'] = GeneralUtility::makeInstance(LanguageServiceFactory::class)->create('default');
        $context = $this->get(RenderingContextFactory::class)->create();
        $context->getTemplatePaths()->setTemplateSource($template);
        self::assertSame($expected, (new TemplateView($context))->render());
    }
}
