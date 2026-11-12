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

namespace TYPO3\CMS\ContentBlocks\Tests\Unit\CropVariants;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\ContentBlocks\CropVariants\CropVariantsRegistry;
use TYPO3\CMS\ContentBlocks\CropVariants\CropVariantsService;
use TYPO3\CMS\ContentBlocks\CropVariants\LoadedCropVariant;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class CropVariantsServiceTest extends UnitTestCase
{
    private const DESKTOP_CONFIGURATION = [
        'title' => 'Desktop',
        'allowedAspectRatios' => [
            'landscape' => [
                'title' => 'Landscape',
                'value' => '16 / 9',
            ],
        ],
    ];

    private const MOBILE_CONFIGURATION = [
        'title' => 'Mobile',
        'allowedAspectRatios' => [
            'square' => [
                'title' => 'Square',
                'value' => 1,
            ],
        ],
    ];

    public static function cropVariantReferencesAreResolvedDataProvider(): iterable
    {
        yield 'Reference is resolved on first level' => [
            'yaml' => [
                'fields' => [
                    [
                        'identifier' => 'image',
                        'type' => 'File',
                        'cropVariants' => [
                            'desktop' => 'vendor/desktop',
                        ],
                    ],
                ],
            ],
            'expected' => [
                'fields' => [
                    [
                        'identifier' => 'image',
                        'type' => 'File',
                        'cropVariants' => [
                            'desktop' => self::DESKTOP_CONFIGURATION,
                        ],
                    ],
                ],
            ],
        ];

        yield 'Reference and inline Crop Variant can be mixed' => [
            'yaml' => [
                'fields' => [
                    [
                        'identifier' => 'image',
                        'type' => 'File',
                        'cropVariants' => [
                            'desktop' => 'vendor/desktop',
                            'mobile' => self::MOBILE_CONFIGURATION,
                        ],
                    ],
                ],
            ],
            'expected' => [
                'fields' => [
                    [
                        'identifier' => 'image',
                        'type' => 'File',
                        'cropVariants' => [
                            'desktop' => self::DESKTOP_CONFIGURATION,
                            'mobile' => self::MOBILE_CONFIGURATION,
                        ],
                    ],
                ],
            ],
        ];

        yield 'Reference is resolved inside Palette and Collection' => [
            'yaml' => [
                'fields' => [
                    [
                        'identifier' => 'palette',
                        'type' => 'Palette',
                        'fields' => [
                            [
                                'identifier' => 'collection',
                                'type' => 'Collection',
                                'fields' => [
                                    [
                                        'identifier' => 'image',
                                        'type' => 'File',
                                        'cropVariants' => [
                                            'desktop' => 'vendor/desktop',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'expected' => [
                'fields' => [
                    [
                        'identifier' => 'palette',
                        'type' => 'Palette',
                        'fields' => [
                            [
                                'identifier' => 'collection',
                                'type' => 'Collection',
                                'fields' => [
                                    [
                                        'identifier' => 'image',
                                        'type' => 'File',
                                        'cropVariants' => [
                                            'desktop' => self::DESKTOP_CONFIGURATION,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        yield 'Reference is resolved inside FlexForm Sheet' => [
            'yaml' => [
                'fields' => [
                    [
                        'identifier' => 'flex',
                        'type' => 'FlexForm',
                        'fields' => [
                            [
                                'identifier' => 'sheet',
                                'type' => 'Sheet',
                                'fields' => [
                                    [
                                        'identifier' => 'image',
                                        'type' => 'File',
                                        'cropVariants' => [
                                            'desktop' => 'vendor/desktop',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'expected' => [
                'fields' => [
                    [
                        'identifier' => 'flex',
                        'type' => 'FlexForm',
                        'fields' => [
                            [
                                'identifier' => 'sheet',
                                'type' => 'Sheet',
                                'fields' => [
                                    [
                                        'identifier' => 'image',
                                        'type' => 'File',
                                        'cropVariants' => [
                                            'desktop' => self::DESKTOP_CONFIGURATION,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        yield 'Reference is resolved inside Type Override' => [
            'yaml' => [
                'fields' => [
                    [
                        'identifier' => 'image',
                        'type' => 'File',
                        'overrideType' => [
                            'image' => [
                                [
                                    'identifier' => 'preview_image',
                                    'type' => 'File',
                                    'cropVariants' => [
                                        'desktop' => 'vendor/desktop',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'expected' => [
                'fields' => [
                    [
                        'identifier' => 'image',
                        'type' => 'File',
                        'overrideType' => [
                            'image' => [
                                [
                                    'identifier' => 'preview_image',
                                    'type' => 'File',
                                    'cropVariants' => [
                                        'desktop' => self::DESKTOP_CONFIGURATION,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        yield 'Yaml without fields is returned unchanged' => [
            'yaml' => [
                'name' => 'vendor/name',
            ],
            'expected' => [
                'name' => 'vendor/name',
            ],
        ];
    }

    #[Test]
    #[DataProvider('cropVariantReferencesAreResolvedDataProvider')]
    public function cropVariantReferencesAreResolved(array $yaml, array $expected): void
    {
        $cropVariantsRegistry = new CropVariantsRegistry();
        $cropVariantsRegistry->register(new LoadedCropVariant('foo', 'vendor/desktop', self::DESKTOP_CONFIGURATION, 'Desktop.yaml'));
        $cropVariantsService = new CropVariantsService();

        $result = $cropVariantsService->applyCropVariants($cropVariantsRegistry, $yaml);

        self::assertSame($expected, $result);
    }

    #[Test]
    public function unknownCropVariantReferenceThrowsException(): void
    {
        $yaml = [
            'fields' => [
                [
                    'identifier' => 'image',
                    'type' => 'File',
                    'cropVariants' => [
                        'desktop' => 'vendor/unknown',
                    ],
                ],
            ],
        ];
        $cropVariantsService = new CropVariantsService();

        $this->expectException(\OutOfBoundsException::class);
        $this->expectExceptionCode(1794501604);

        $cropVariantsService->applyCropVariants(new CropVariantsRegistry(), $yaml);
    }
}
