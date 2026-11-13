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
                    'SimpleBasic.yaml',
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
                    'PaletteBasic.yaml',
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
                    'TabBasic.yaml',
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
                    'TabBasic.yaml',
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
                    'FieldBasic.yaml',
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
                    'SimpleBasic.yaml',
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
                    'SimpleBasic.yaml',
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
                    'PaletteBasic.yaml',
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
                    'TabBasic.yaml',
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
                    'TabBasic.yaml',
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
                    'FieldBasic.yaml',
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
                    ],
                    'Basic1.yaml',
                ),
                new LoadedBasic(
                    'foo',
                    'Basic2',
                    [
                        [
                            'identifier' => 'bar',
                            'type' => 'Textarea',
                        ],
                    ],
                    'Basic2.yaml',
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
            ],
            'Basic1.yaml',
        );

        $basic2 = new LoadedBasic(
            'foo',
            'Basic1',
            [
                [
                    'identifier' => 'bar',
                    'type' => 'Textarea',
                ],
            ],
            'Basic1.yaml',
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
            ],
            'Basic1.yaml',
        );

        $basic2 = new LoadedBasic(
            'foo',
            'Basic2',
            [
                [
                    'identifier' => 'Basic1',
                    'type' => 'Basic',
                ],
            ],
            'Basic1.yaml',
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

    #[Test]
    public function nestedBasicsInfiniteLoopInsideCollectionDetected(): void
    {
        $basic = new LoadedBasic(
            'foo',
            'Basic1',
            [
                [
                    'identifier' => 'collection',
                    'type' => 'Collection',
                    'fields' => [
                        [
                            'identifier' => 'Basic1',
                            'type' => 'Basic',
                        ],
                    ],
                ],
            ],
            'Basic1.yaml',
        );

        $yaml = [
            'basics' => [
                'Basic1',
            ],
        ];

        $basicsRegistry = new BasicsRegistry();
        $basicsRegistry->register($basic);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionCode(1711291137);

        $basicsService = new BasicsService();
        $basicsService->applyBasics($basicsRegistry, $yaml);
    }

    #[Test]
    public function siblingBasicsDoNotIncreaseNestingDepth(): void
    {
        $basicsRegistry = new BasicsRegistry();
        $fields = [];
        for ($i = 0; $i < 120; $i++) {
            $basicsRegistry->register(new LoadedBasic(
                'foo',
                'Basic' . $i,
                [
                    [
                        'identifier' => 'field' . $i,
                        'type' => 'Text',
                    ],
                ],
                'Basic' . $i . '.yaml',
            ));
            $fields[] = [
                'identifier' => 'Basic' . $i,
                'type' => 'Basic',
            ];
        }

        $basicsService = new BasicsService();
        $result = $basicsService->applyBasics($basicsRegistry, ['fields' => $fields]);

        self::assertCount(120, $result['fields']);
    }

    public static function basicsOverridesAreAppliedDataProvider(): iterable
    {
        yield 'Field on root level added by top level basics array' => [
            'yaml' => [
                'name' => 'foo/bar',
                'basics' => [
                    'SimpleBasic',
                ],
                'basicsOverrides' => [
                    'background_type' => [
                        'default' => 1,
                    ],
                ],
            ],
            'expected' => [
                'name' => 'foo/bar',
                'basics' => [
                    'SimpleBasic',
                ],
                'basicsOverrides' => [
                    'background_type' => [
                        'default' => 1,
                    ],
                ],
                'fields' => [
                    [
                        'identifier' => 'tab',
                        'type' => 'Tab',
                    ],
                    [
                        'identifier' => 'background_type',
                        'type' => 'Select',
                        'default' => 1,
                    ],
                ],
            ],
        ];

        yield 'Field inside root level Palette added by Basic type' => [
            'yaml' => [
                'name' => 'foo/bar',
                'fields' => [
                    [
                        'identifier' => 'palette',
                        'type' => 'Palette',
                        'fields' => [
                            [
                                'identifier' => 'SimpleBasic',
                                'type' => 'Basic',
                            ],
                        ],
                    ],
                ],
                'basicsOverrides' => [
                    'background_type' => [
                        'label' => 'Background',
                        'default' => 1,
                        'required' => true,
                    ],
                ],
            ],
            'expected' => [
                'name' => 'foo/bar',
                'fields' => [
                    [
                        'identifier' => 'palette',
                        'type' => 'Palette',
                        'fields' => [
                            [
                                'identifier' => 'tab',
                                'type' => 'Tab',
                            ],
                            [
                                'identifier' => 'background_type',
                                'type' => 'Select',
                                'label' => 'Background',
                                'default' => 1,
                                'required' => true,
                            ],
                        ],
                    ],
                ],
                'basicsOverrides' => [
                    'background_type' => [
                        'label' => 'Background',
                        'default' => 1,
                        'required' => true,
                    ],
                ],
            ],
        ];
    }

    #[Test]
    #[DataProvider('basicsOverridesAreAppliedDataProvider')]
    public function basicsOverridesAreApplied(array $yaml, array $expected): void
    {
        $basicsService = new BasicsService();

        $result = $basicsService->applyBasics($this->createBasicsRegistryForOverrides(), $yaml);

        self::assertSame($expected, $result);
    }

    public static function invalidBasicsOverridesThrowExceptionDataProvider(): iterable
    {
        yield 'Options are not a map' => [
            'basicsOverrides' => [
                'background_type' => 1,
            ],
            'expectedExceptionCode' => 1794570535,
        ];

        yield 'Unsupported option' => [
            'basicsOverrides' => [
                'background_type' => [
                    'items' => [],
                ],
            ],
            'expectedExceptionCode' => 1794570536,
        ];

        yield 'Unknown identifier' => [
            'basicsOverrides' => [
                'unknown' => [
                    'default' => 1,
                ],
            ],
            'expectedExceptionCode' => 1794570537,
        ];

        yield 'Tab identifier' => [
            'basicsOverrides' => [
                'tab' => [
                    'default' => 1,
                ],
            ],
            'expectedExceptionCode' => 1794570537,
        ];

        yield 'Field inside Collection' => [
            'basicsOverrides' => [
                'collection_field' => [
                    'default' => 1,
                ],
            ],
            'expectedExceptionCode' => 1794570537,
        ];
    }

    #[Test]
    #[DataProvider('invalidBasicsOverridesThrowExceptionDataProvider')]
    public function invalidBasicsOverridesThrowException(array $basicsOverrides, int $expectedExceptionCode): void
    {
        $yaml = [
            'name' => 'foo/bar',
            'basics' => [
                'SimpleBasic',
            ],
            'fields' => [
                [
                    'identifier' => 'collection',
                    'type' => 'Collection',
                    'fields' => [
                        [
                            'identifier' => 'collection_field',
                            'type' => 'Text',
                        ],
                    ],
                ],
            ],
            'basicsOverrides' => $basicsOverrides,
        ];

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode($expectedExceptionCode);

        $basicsService = new BasicsService();
        $basicsService->applyBasics($this->createBasicsRegistryForOverrides(), $yaml);
    }

    private function createBasicsRegistryForOverrides(): BasicsRegistry
    {
        $basicsRegistry = new BasicsRegistry();
        $basicsRegistry->register(new LoadedBasic(
            'foo',
            'SimpleBasic',
            [
                [
                    'identifier' => 'tab',
                    'type' => 'Tab',
                ],
                [
                    'identifier' => 'background_type',
                    'type' => 'Select',
                ],
            ],
            'SimpleBasic.yaml',
        ));

        return $basicsRegistry;
    }
}
