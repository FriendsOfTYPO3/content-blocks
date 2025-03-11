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

namespace TYPO3\CMS\ContentBlocks\Tests\Unit\Basics;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\ContentBlocks\Basics\BasicsRegistry;
use TYPO3\CMS\ContentBlocks\Basics\BasicsService;
use TYPO3\CMS\ContentBlocks\Basics\LoadedBasic;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class BasicsServiceTest extends UnitTestCase
{
    public static function basicsAreAppliedByBasicsTypeDataProvider(): iterable
    {
        yield 'Simple Basic is replaced on first level' => [
            'yaml' => [
                'fields' => [
                    [
                        'identifier' => 'standard',
                        'type' => 'link',
                    ],
                    [
                        'identifier' => 'SimpleBasic',
                        'type' => 'Basic',
                    ],
                ],
            ],
            'basics' => [
                new LoadedBasic(
                    'foo',
                    'SimpleBasic',
                    [
                        [
                            'identifier' => 'foo',
                            'type' => 'Text',
                        ],
                        [
                            'identifier' => 'bar',
                            'type' => 'Textarea',
                        ],
                    ],
                ),
            ],
            'expected' => [
                'fields' => [
                    [
                        'identifier' => 'standard',
                        'type' => 'link',
                    ],
                    [
                        'identifier' => 'foo',
                        'type' => 'Text',
                    ],
                    [
                        'identifier' => 'bar',
                        'type' => 'Textarea',
                    ],
                ],
            ],
        ];

        yield 'Palette Basic is replaced on first level' => [
            'yaml' => [
                'fields' => [
                    [
                        'identifier' => 'standard',
                        'type' => 'link',
                    ],
                    [
                        'identifier' => 'PaletteBasic',
                        'type' => 'Basic',
                    ],
                ],
            ],
            'basics' => [
                new LoadedBasic(
                    'foo',
                    'PaletteBasic',
                    [
                        [
                            'identifier' => 'a_palette',
                            'type' => 'Palette',
                            'fields' => [
                                [
                                    'identifier' => 'foo',
                                    'type' => 'Text',
                                ],
                                [
                                    'identifier' => 'bar',
                                    'type' => 'Textarea',
                                ],
                            ],
                        ],
                    ],
                ),
            ],
            'expected' => [
                'fields' => [
                    [
                        'identifier' => 'standard',
                        'type' => 'link',
                    ],
                    [
                        'identifier' => 'a_palette',
                        'type' => 'Palette',
                        'fields' => [
                            [
                                'identifier' => 'foo',
                                'type' => 'Text',
                            ],
                            [
                                'identifier' => 'bar',
                                'type' => 'Textarea',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        yield 'Tab Basic is replaced on first level' => [
            'yaml' => [
                'fields' => [
                    [
                        'identifier' => 'standard',
                        'type' => 'link',
                    ],
                    [
                        'identifier' => 'TabBasic',
                        'type' => 'Basic',
                    ],
                ],
            ],
            'basics' => [
                new LoadedBasic(
                    'foo',
                    'TabBasic',
                    [
                        [
                            'identifier' => 'a_tab',
                            'type' => 'Tab',
                        ],
                        [
                            'identifier' => 'foo',
                            'type' => 'Text',
                        ],
                        [
                            'identifier' => 'bar',
                            'type' => 'Textarea',
                        ],
                    ],
                ),
            ],
            'expected' => [
                'fields' => [
                    [
                        'identifier' => 'standard',
                        'type' => 'link',
                    ],
                    [
                        'identifier' => 'a_tab',
                        'type' => 'Tab',
                    ],
                    [
                        'identifier' => 'foo',
                        'type' => 'Text',
                    ],
                    [
                        'identifier' => 'bar',
                        'type' => 'Textarea',
                    ],
                ],
            ],
        ];

        yield 'Nested Basics' => [
            'yaml' => [
                'fields' => [
                    [
                        'identifier' => 'standard',
                        'type' => 'link',
                    ],
                    [
                        'identifier' => 'TabBasic',
                        'type' => 'Basic',
                    ],
                ],
            ],
            'basics' => [
                new LoadedBasic(
                    'foo',
                    'TabBasic',
                    [
                        [
                            'identifier' => 'a_tab',
                            'type' => 'Tab',
                        ],
                        [
                            'identifier' => 'FieldBasic',
                            'type' => 'Basic',
                        ],
                    ],
                ),
                new LoadedBasic(
                    'bar',
                    'FieldBasic',
                    [
                        [
                            'identifier' => 'foo',
                            'type' => 'Text',
                        ],
                        [
                            'identifier' => 'bar',
                            'type' => 'Textarea',
                        ],
                    ],
                ),
            ],
            'expected' => [
                'fields' => [
                    [
                        'identifier' => 'standard',
                        'type' => 'link',
                    ],
                    [
                        'identifier' => 'a_tab',
                        'type' => 'Tab',
                    ],
                    [
                        'identifier' => 'foo',
                        'type' => 'Text',
                    ],
                    [
                        'identifier' => 'bar',
                        'type' => 'Textarea',
                    ],
                ],
            ],
        ];

        yield 'Simple Basic is replaced inside Palette' => [
            'yaml' => [
                'fields' => [
                    [
                        'identifier' => 'standard',
                        'type' => 'link',
                    ],
                    [
                        'identifier' => 'a_palette',
                        'type' => 'Palette',
                        'fields' => [
                            [
                                'identifier' => 'SimpleBasic',
                                'type' => 'Basic',
                            ],
                        ],
                    ],
                ],
            ],
            'basics' => [
                new LoadedBasic(
                    'foo',
                    'SimpleBasic',
                    [
                        [
                            'identifier' => 'foo',
                            'type' => 'Text',
                        ],
                        [
                            'identifier' => 'bar',
                            'type' => 'Textarea',
                        ],
                    ],
                ),
            ],
            'expected' => [
                'fields' => [
                    [
                        'identifier' => 'standard',
                        'type' => 'link',
                    ],
                    [
                        'identifier' => 'a_palette',
                        'type' => 'Palette',
                        'fields' => [
                            [
                                'identifier' => 'foo',
                                'type' => 'Text',
                            ],
                            [
                                'identifier' => 'bar',
                                'type' => 'Textarea',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    #[DataProvider('basicsAreAppliedByBasicsTypeDataProvider')]
    #[Test]
    public function basicsAreAppliedByBasicsType(array $yaml, array $basics, array $expected): void
    {
        $basicsRegistry = new BasicsRegistry();
        foreach ($basics as $basic) {
            $basicsRegistry->register($basic);
        }

        $basicsService = new BasicsService();

        self::assertSame($expected, $basicsService->applyBasics($basicsRegistry, $yaml));
    }

    public static function basicsAreAppendedByTopLevelBasicsArrayDataProvider(): iterable
    {
        yield 'Simple Basic is appended' => [
            'yaml' => [
                'basics' => [
                    'SimpleBasic',
                ],
                'fields' => [
                    [
                        'identifier' => 'standard',
                        'type' => 'link',
                    ],
                ],
            ],
            'basics' => [
                new LoadedBasic(
                    'foo',
                    'SimpleBasic',
                    [
                        [
                            'identifier' => 'foo',
                            'type' => 'Text',
                        ],
                        [
                            'identifier' => 'bar',
                            'type' => 'Textarea',
                        ],
                    ],
                ),
            ],
            'expected' => [
                'basics' => [
                    'SimpleBasic',
                ],
                'fields' => [
                    [
                        'identifier' => 'standard',
                        'type' => 'link',
                    ],
                    [
                        'identifier' => 'foo',
                        'type' => 'Text',
                    ],
                    [
                        'identifier' => 'bar',
                        'type' => 'Textarea',
                    ],
                ],
            ],
        ];

        yield 'Palette Basic is appended' => [
            'yaml' => [
                'basics' => [
                    'PaletteBasic',
                ],
                'fields' => [
                    [
                        'identifier' => 'standard',
                        'type' => 'link',
                    ],
                ],
            ],
            'basics' => [
                new LoadedBasic(
                    'foo',
                    'PaletteBasic',
                    [
                        [
                            'identifier' => 'a_palette',
                            'type' => 'Palette',
                            'fields' => [
                                [
                                    'identifier' => 'foo',
                                    'type' => 'Text',
                                ],
                                [
                                    'identifier' => 'bar',
                                    'type' => 'Textarea',
                                ],
                            ],
                        ],
                    ],
                ),
            ],
            'expected' => [
                'basics' => [
                    'PaletteBasic',
                ],
                'fields' => [
                    [
                        'identifier' => 'standard',
                        'type' => 'link',
                    ],
                    [
                        'identifier' => 'a_palette',
                        'type' => 'Palette',
                        'fields' => [
                            [
                                'identifier' => 'foo',
                                'type' => 'Text',
                            ],
                            [
                                'identifier' => 'bar',
                                'type' => 'Textarea',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        yield 'Tab Basic is appended' => [
            'yaml' => [
                'basics' => [
                    'TabBasic',
                ],
                'fields' => [
                    [
                        'identifier' => 'standard',
                        'type' => 'link',
                    ],
                ],
            ],
            'basics' => [
                new LoadedBasic(
                    'foo',
                    'TabBasic',
                    [
                        [
                            'identifier' => 'a_tab',
                            'type' => 'Tab',
                        ],
                        [
                            'identifier' => 'foo',
                            'type' => 'Text',
                        ],
                        [
                            'identifier' => 'bar',
                            'type' => 'Textarea',
                        ],
                    ],
                ),
            ],
            'expected' => [
                'basics' => [
                    'TabBasic',
                ],
                'fields' => [
                    [
                        'identifier' => 'standard',
                        'type' => 'link',
                    ],
                    [
                        'identifier' => 'a_tab',
                        'type' => 'Tab',
                    ],
                    [
                        'identifier' => 'foo',
                        'type' => 'Text',
                    ],
                    [
                        'identifier' => 'bar',
                        'type' => 'Textarea',
                    ],
                ],
            ],
        ];

        yield 'Nested basics' => [
            'yaml' => [
                'basics' => [
                    'TabBasic',
                ],
                'fields' => [
                    [
                        'identifier' => 'standard',
                        'type' => 'link',
                    ],
                ],
            ],
            'basics' => [
                new LoadedBasic(
                    'foo',
                    'TabBasic',
                    [
                        [
                            'identifier' => 'a_tab',
                            'type' => 'Tab',
                        ],
                        [
                            'identifier' => 'FieldBasic',
                            'type' => 'Basic',
                        ],
                    ],
                ),
                new LoadedBasic(
                    'bar',
                    'FieldBasic',
                    [
                        [
                            'identifier' => 'foo',
                            'type' => 'Text',
                        ],
                        [
                            'identifier' => 'bar',
                            'type' => 'Textarea',
                        ],
                    ],
                ),
            ],
            'expected' => [
                'basics' => [
                    'TabBasic',
                ],
                'fields' => [
                    [
                        'identifier' => 'standard',
                        'type' => 'link',
                    ],
                    [
                        'identifier' => 'a_tab',
                        'type' => 'Tab',
                    ],
                    [
                        'identifier' => 'foo',
                        'type' => 'Text',
                    ],
                    [
                        'identifier' => 'bar',
                        'type' => 'Textarea',
                    ],
                ],
            ],
        ];

        yield 'multiple basics are appended by top level basics array' => [
            'yaml' => [
                'basics' => [
                    'Basic1',
                    'Basic2',
                ],
                'fields' => [
                    [
                        'identifier' => 'standard',
                        'type' => 'link',
                    ],
                ],
            ],
            'basics' => [
                new LoadedBasic(
                    'foo',
                    'Basic1',
                    [
                        [
                            'identifier' => 'a_tab',
                            'type' => 'Tab',
                        ],
                        [
                            'identifier' => 'foo',
                            'type' => 'Text',
                        ],
                    ]
                ),
                new LoadedBasic(
                    'foo',
                    'Basic2',
                    [
                        [
                            'identifier' => 'bar',
                            'type' => 'Textarea',
                        ],
                    ]
                ),
            ],
            'expected' => [
                'basics' => [
                    'Basic1',
                    'Basic2',
                ],
                'fields' => [
                    [
                        'identifier' => 'standard',
                        'type' => 'link',
                    ],
                    [
                        'identifier' => 'a_tab',
                        'type' => 'Tab',
                    ],
                    [
                        'identifier' => 'foo',
                        'type' => 'Text',
                    ],
                    [
                        'identifier' => 'bar',
                        'type' => 'Textarea',
                    ],
                ],
            ],
        ];
    }

    #[DataProvider('basicsAreAppendedByTopLevelBasicsArrayDataProvider')]
    #[Test]
    public function basicsAreAppendedByTopLevelBasicsArray(array $yaml, array $basics, array $expected): void
    {
        $basicsRegistry = new BasicsRegistry();
        foreach ($basics as $basic) {
            $basicsRegistry->register($basic);
        }

        $basicsService = new BasicsService();

        self::assertSame($expected, $basicsService->applyBasics($basicsRegistry, $yaml));
    }

    #[Test]
    public function basicWithSameIdentifierThrowsException(): void
    {
        $basic1 = new LoadedBasic(
            'foo',
            'Basic1',
            [
                [
                    'identifier' => 'a_tab',
                    'type' => 'Tab',
                ],
                [
                    'identifier' => 'foo',
                    'type' => 'Text',
                ],
            ]
        );

        $basic2 = new LoadedBasic(
            'foo',
            'Basic1',
            [
                [
                    'identifier' => 'bar',
                    'type' => 'Textarea',
                ],
            ]
        );

        $basicsRegistry = new BasicsRegistry();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionCode(1701279535);

        $basicsRegistry->register($basic1);
        $basicsRegistry->register($basic2);
    }

    #[Test]
    public function nestedBasicsInfiniteLoopDetected(): void
    {
        $basic1 = new LoadedBasic(
            'foo',
            'Basic1',
            [
                [
                    'identifier' => 'Basic2',
                    'type' => 'Basic',
                ],
            ]
        );

        $basic2 = new LoadedBasic(
            'foo',
            'Basic2',
            [
                [
                    'identifier' => 'Basic1',
                    'type' => 'Basic',
                ],
            ]
        );

        $yaml = [
            'basics' => [
                'Basic1',
            ],
        ];

        $basicsRegistry = new BasicsRegistry();
        $basicsRegistry->register($basic1);
        $basicsRegistry->register($basic2);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionCode(1711291137);

        $basicsService = new BasicsService();
        $basicsService->applyBasics($basicsRegistry, $yaml);
    }
}
