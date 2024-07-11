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

namespace TYPO3\CMS\ContentBlocks\Tests\Unit\FieldTypes;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\ContentBlocks\FieldType\FileFieldType;
use TYPO3\CMS\Core\Resource\AbstractFile;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class FileFieldTypeTest extends UnitTestCase
{
    public static function getTcaReturnsExpectedTcaDataProvider(): iterable
    {
        yield 'truthy values' => [
            'config' => [
                'label' => 'foo',
                'description' => 'foo',
                'displayCond' => [
                    'foo' => 'bar',
                ],
                'l10n_display' => 'foo',
                'l10n_mode' => 'foo',
                'onChange' => 'foo',
                'exclude' => true,
                'extendedPalette' => 1,
                'non_available_field' => 'foo',
                'allowed' => 'common-image-types',
                'disallowed' => 'png',
                'appearance' => [
                    'foo' => 'bar',
                ],
                'behaviour' => [
                    'foo' => 'bar',
                ],
                'overrideChildTca' => [
                    'foo' => 'bar',
                ],
                'readOnly' => 1,
                'minitems' => 1,
                'maxitems' => 1,
                'cropVariants' => [
                    'foo' => [
                        'allowedAspectRatios' => [
                            'portrait' => [
                                'title' => 'Portrait',
                                'value' => '3 / 4',
                            ],
                            'landscape' => [
                                'title' => 'Landscape',
                                'value' => '1.333',
                            ],
                            'plain integer' => [
                                'title' => 'Plain Integer',
                                'value' => 4,
                            ],
                            'wrong input 1' => [
                                'title' => 'wrong input 1',
                                'value' => 'a',
                            ],
                            'wrong input 2' => [
                                'title' => 'wrong input 2',
                                'value' => 'a / b',
                            ],
                            'wrong input 3' => [
                                'title' => 'wrong input 3',
                                'value' => '1 /',
                            ],
                            'wrong input 4' => [
                                'title' => 'wrong input 4',
                                'value' => '/ 1',
                            ],
                        ],
                    ],
                ],
            ],
            'expectedTca' => [
                'label' => 'foo',
                'description' => 'foo',
                'displayCond' => [
                    'foo' => 'bar',
                ],
                'l10n_display' => 'foo',
                'l10n_mode' => 'foo',
                'onChange' => 'foo',
                'exclude' => true,
                'config' => [
                    'type' => 'file',
                    'allowed' => 'common-image-types',
                    'disallowed' => 'png',
                    'appearance' => [
                        'foo' => 'bar',
                    ],
                    'behaviour' => [
                        'foo' => 'bar',
                    ],
                    'readOnly' => true,
                    'minitems' => 1,
                    'maxitems' => 1,
                    'overrideChildTca' => [
                        'foo' => 'bar',
                        'columns' => [
                            'crop' => [
                                'config' => [
                                    'cropVariants' => [
                                        'foo' => [
                                            'allowedAspectRatios' => [
                                                'portrait' => [
                                                    'title' => 'Portrait',
                                                    'value' => 0.75,
                                                ],
                                                'landscape' => [
                                                    'title' => 'Landscape',
                                                    'value' => 1.333,
                                                ],
                                                'plain integer' => [
                                                    'title' => 'Plain Integer',
                                                    'value' => 4.0,
                                                ],
                                                'wrong input 1' => [
                                                    'title' => 'wrong input 1',
                                                    'value' => 0.0,
                                                ],
                                                'wrong input 2' => [
                                                    'title' => 'wrong input 2',
                                                    'value' => 0.0,
                                                ],
                                                'wrong input 3' => [
                                                    'title' => 'wrong input 3',
                                                    'value' => 1.0,
                                                ],
                                                'wrong input 4' => [
                                                    'title' => 'wrong input 4',
                                                    'value' => 0.0,
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        yield 'falsy values' => [
            'config' => [
                'label' => '',
                'description' => null,
                'displayCond' => [],
                'l10n_display' => '',
                'l10n_mode' => '',
                'onChange' => '',
                'exclude' => false,
                'extendedPalette' => 0,
                'non_available_field' => 'foo',
                'allowed' => '',
                'disallowed' => '',
                'appearance' => [],
                'behaviour' => [],
                'readOnly' => 0,
                'minitems' => 0,
                'maxitems' => 0,
                'cropVariants' => [],
                'overrideChildTca' => [],
            ],
            'expectedTca' => [
                'config' => [
                    'type' => 'file',
                    'overrideChildTca' => [
                        'types' => [
                            AbstractFile::FILETYPE_IMAGE => [
                                'showitem' => '--palette--;;basicoverlayPalette,--palette--;;filePalette',
                            ],
                            AbstractFile::FILETYPE_AUDIO => [
                                'showitem' => '--palette--;;basicoverlayPalette,--palette--;;filePalette',
                            ],
                            AbstractFile::FILETYPE_VIDEO => [
                                'showitem' => '--palette--;;basicoverlayPalette,--palette--;;filePalette',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    #[DataProvider('getTcaReturnsExpectedTcaDataProvider')]
    #[Test]
    public function getTcaReturnsExpectedTca(array $config, array $expectedTca): void
    {
        $fieldConfiguration = FileFieldType::createFromArray($config);

        self::assertSame($expectedTca, $fieldConfiguration->getTca());
    }
}
