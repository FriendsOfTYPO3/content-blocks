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

use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\ContentBlocks\ViewHelpers\Asset\CssViewHelper;
use TYPO3\CMS\Core\Page\AssetCollector;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class CssViewHelperTest extends FunctionalTestCase
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
        $assetCollector = new AssetCollector();
        $viewHelper = new CssViewHelper();
        $viewHelper->injectAssetCollector($assetCollector);
        $viewHelper->injectContentBlockRegistry($this->get(ContentBlockRegistry::class));
        $viewHelper->setArguments([
            'identifier' => 'test',
            'name' => 'foo/bar',
            'file' => 'Frontend.css',
            'priority' => false,
        ]);
        $viewHelper->initializeArgumentsAndRender();
        $collectedStyleSheets = $assetCollector->getStyleSheets();

        self::assertSame('EXT:foo/ContentBlocks/foo/Resources/Public/Frontend.css', $collectedStyleSheets['test']['source']);
        self::assertSame([], $collectedStyleSheets['test']['attributes']);
    }

    /**
     * @test
     */
    public function booleanAttributesAreProperlyConverted(): void
    {
        $assetCollector = new AssetCollector();
        $viewHelper = new CssViewHelper();
        $viewHelper->injectAssetCollector($assetCollector);
        $viewHelper->injectContentBlockRegistry($this->get(ContentBlockRegistry::class));
        $viewHelper->setArguments([
            'identifier' => 'test',
            'name' => 'bar/foo',
            'file' => 'my.css',
            'disabled' => true,
            'priority' => false,
        ]);
        $viewHelper->initializeArgumentsAndRender();
        $collectedStyleSheets = $assetCollector->getStyleSheets();

        self::assertSame('EXT:bar/ContentBlocks/bar/Resources/Public/my.css', $collectedStyleSheets['test']['source']);
        self::assertSame(['disabled' => 'disabled'], $collectedStyleSheets['test']['attributes']);
    }
}
