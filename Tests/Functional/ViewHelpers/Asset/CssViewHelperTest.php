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

namespace TYPO3\CMS\ContentBlocks\Tests\Functional\ViewHelpers\Asset;

use TYPO3\CMS\Core\Page\AssetCollector;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextFactory;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use TYPO3Fluid\Fluid\View\TemplateView;

final class CssViewHelperTest extends FunctionalTestCase
{
    protected bool $initializeDatabase = false;

    protected array $coreExtensionsToLoad = [
        'content_blocks',
    ];

    protected array $testExtensionsToLoad = [
        'typo3/sysext/content_blocks/Tests/Fixtures/Extensions/foo',
        'typo3/sysext/content_blocks/Tests/Fixtures/Extensions/bar',
    ];

    /**
     * @test
     */
    public function sourceStringIsNotHtmlEncodedBeforePassedToAssetCollector(): void
    {
        $context = $this->get(RenderingContextFactory::class)->create();
        $context->getTemplatePaths()->setTemplateSource('<cb:asset.css name="foo/bar" identifier="test" file="Frontend.css" priority="0"/>');

        (new TemplateView($context))->render();

        $collectedStyleSheets = $this->get(AssetCollector::class)->getStyleSheets();
        self::assertSame('EXT:foo/ContentBlocks/foo/Assets/Frontend.css', $collectedStyleSheets['test']['source']);
        self::assertSame([], $collectedStyleSheets['test']['attributes']);
    }

    /**
     * @test
     */
    public function booleanAttributesAreProperlyConverted(): void
    {
        $context = $this->get(RenderingContextFactory::class)->create();
        $context->getTemplatePaths()->setTemplateSource('<cb:asset.css name="bar/foo" identifier="test" file="my.css" disabled="1" priority="0"/>');

        (new TemplateView($context))->render();

        $collectedStyleSheets = $this->get(AssetCollector::class)->getStyleSheets();
        self::assertSame('EXT:bar/ContentBlocks/bar/Assets/my.css', $collectedStyleSheets['test']['source']);
        self::assertSame(['disabled' => 'disabled'], $collectedStyleSheets['test']['attributes']);
    }
}
