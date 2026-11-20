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

namespace TYPO3\CMS\ContentBlocks\Tests\Unit\Tabs;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\ContentBlocks\Tabs\TabsService;
use TYPO3\CMS\ContentBlocks\Yaml\DefaultContentBlocksYamlParser;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class TabsServiceTest extends UnitTestCase
{
    private const CONTENT_BLOCK_PATH = __DIR__ . '/Fixtures/local-tabs';

    public static function tabsAreAppliedDataProvider(): iterable
    {
        yield 'Yaml without tabs is not changed' => [
            'yaml' => [
                'name' => 'vendor/local-tabs',
                'fields' => [
                    [
                        'identifier' => 'header',
                        'useExistingField' => true,
                    ],
                ],
            ],
            'expected' => [
                'name' => 'vendor/local-tabs',
                'fields' => [
                    [
                        'identifier' => 'header',
                        'useExistingField' => true,
                    ],
                ],
            ],
        ];

        yield 'Tabs are appended after fields in the order of the tabs list' => [
            'yaml' => [
                'name' => 'vendor/local-tabs',
                'tabs' => [
                    'settings',
                    'media',
                ],
                'fields' => [
                    [
                        'identifier' => 'header',
                        'useExistingField' => true,
                    ],
                ],
            ],
            'expected' => [
                'name' => 'vendor/local-tabs',
                'tabs' => [
                    'settings',
                    'media',
                ],
                'fields' => [
                    [
                        'identifier' => 'header',
                        'useExistingField' => true,
                    ],
                    [
                        'identifier' => 'options',
                        'type' => 'Tab',
                        'label' => 'Options',
                    ],
                    [
                        'identifier' => 'layout',
                        'type' => 'Select',
                        'renderType' => 'selectSingle',
                        'items' => [
                            [
                                'label' => 'Default',
                                'value' => 'default',
                            ],
                        ],
                    ],
                    [
                        'identifier' => 'Vendor/Spacing',
                        'type' => 'Basic',
                    ],
                    [
                        'identifier' => 'media',
                        'type' => 'Tab',
                    ],
                    [
                        'identifier' => 'image',
                        'type' => 'File',
                    ],
                ],
            ],
        ];

        yield 'Tabs without root fields start with the first Tab' => [
            'yaml' => [
                'name' => 'vendor/local-tabs',
                'tabs' => [
                    'media',
                ],
            ],
            'expected' => [
                'name' => 'vendor/local-tabs',
                'tabs' => [
                    'media',
                ],
                'fields' => [
                    [
                        'identifier' => 'media',
                        'type' => 'Tab',
                    ],
                    [
                        'identifier' => 'image',
                        'type' => 'File',
                    ],
                ],
            ],
        ];
    }

    #[DataProvider('tabsAreAppliedDataProvider')]
    #[Test]
    public function tabsAreApplied(array $yaml, array $expected): void
    {
        $tabsService = new TabsService(new DefaultContentBlocksYamlParser());

        $result = $tabsService->applyTabs(self::CONTENT_BLOCK_PATH, $yaml);

        self::assertSame($expected, $result);
    }

    #[Test]
    public function loadTabReturnsTabFileAsIs(): void
    {
        $tabsService = new TabsService(new DefaultContentBlocksYamlParser());
        $expected = [
            'identifier' => 'options',
            'label' => 'Options',
            'fields' => [
                [
                    'identifier' => 'layout',
                    'type' => 'Select',
                    'renderType' => 'selectSingle',
                    'items' => [
                        [
                            'label' => 'Default',
                            'value' => 'default',
                        ],
                    ],
                ],
                [
                    'identifier' => 'Vendor/Spacing',
                    'type' => 'Basic',
                ],
            ],
        ];

        $result = $tabsService->loadTab(self::CONTENT_BLOCK_PATH, 'settings', 'vendor/local-tabs');

        self::assertSame($expected, $result);
    }

    public static function invalidTabsThrowExceptionDataProvider(): iterable
    {
        yield 'tabs is not a list' => [
            'tabs' => 'media',
            'expectedCode' => 1794566428,
        ];

        yield 'Tab name is not a string' => [
            'tabs' => [
                ['media'],
            ],
            'expectedCode' => 1794566429,
        ];

        yield 'Tab name is empty' => [
            'tabs' => [
                '',
            ],
            'expectedCode' => 1794566429,
        ];

        yield 'Tab file does not exist' => [
            'tabs' => [
                'missing',
            ],
            'expectedCode' => 1794566430,
        ];

        yield 'Tab file has no fields' => [
            'tabs' => [
                'without-fields',
            ],
            'expectedCode' => 1794566431,
        ];
    }

    #[DataProvider('invalidTabsThrowExceptionDataProvider')]
    #[Test]
    public function invalidTabsThrowException(mixed $tabs, int $expectedCode): void
    {
        $tabsService = new TabsService(new DefaultContentBlocksYamlParser());
        $yaml = [
            'name' => 'vendor/local-tabs',
            'tabs' => $tabs,
        ];

        $this->expectExceptionCode($expectedCode);

        $tabsService->applyTabs(self::CONTENT_BLOCK_PATH, $yaml);
    }
}
