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

use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class IconGeneratorTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'typo3conf/ext/content_blocks/Tests/Fixtures/Extensions/simple',
        'typo3conf/ext/content_blocks',
    ];

    public static function contentBlockIconsAreRegisteredDataProvider(): iterable
    {
        yield ['tt_content-simple_simple-175ef6f'];
        yield ['tt_content-simple_simple2-175ef6f'];
        yield ['tt_content-simple_basics-175ef6f'];
    }

    /**
     * @test
     * @dataProvider contentBlockIconsAreRegisteredDataProvider
     */
    public function contentBlockIconsAreRegistered(string $identifier): void
    {
        $iconRegistry = $this->get(IconRegistry::class);

        self::assertTrue($iconRegistry->isRegistered($identifier));
    }

    public static function registeredContentBlockIconsHaveCorrectConfigurationDataProvider(): iterable
    {
        yield [
            'tt_content-simple_simple-175ef6f',
            [
                'provider' => SvgIconProvider::class,
                'options' => [
                    'source' => 'EXT:simple/ContentBlocks/ContentElements/simple/Assets/Icon.svg',
                ],
            ],
        ];
        yield [
            'tt_content-simple_simple2-175ef6f',
            [
                'provider' => SvgIconProvider::class,
                'options' => [
                    'source' => 'EXT:simple/ContentBlocks/ContentElements/simple2/Assets/Icon.svg',
                ],
            ],
        ];
        yield [
            'tt_content-simple_basics-175ef6f',
            [
                'provider' => SvgIconProvider::class,
                'options' => [
                    'source' => 'EXT:simple/ContentBlocks/ContentElements/simple_with_basics/Assets/Icon.svg',
                ],
            ],
        ];
    }

    /**
     * @test
     * @dataProvider registeredContentBlockIconsHaveCorrectConfigurationDataProvider
     */
    public function registeredContentBlockIconsHaveCorrectConfiguration(string $identifier, array $configuration): void
    {
        /** @var IconRegistry $iconRegistry */
        $iconRegistry = $this->get(IconRegistry::class);

        $result = $iconRegistry->getIconConfigurationByIdentifier($identifier);

        self::assertSame($configuration, $result);
    }
}
