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

use TYPO3\CMS\ContentBlocks\FieldConfiguration\FileFieldConfiguration;
use TYPO3\CMS\Core\Resource\AbstractFile;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class FileFieldConfigurationTest extends UnitTestCase
{
    public static function getTcaReturnsExpectedTcaDataProvider(): iterable
    {
        yield 'truthy values' => [
            'config' => [
                'enableImageManipulation' => 1,
                'properties' => [
                    'non_available_field' => 'foo',
                    'allowed' => 'common-image-types',
                    'disallowed' => 'png',
                    'appearance' => [
                        'foo' => 'bar',
                    ],
                    'behaviour' => [
                        'foo' => 'bar',
                    ],
                    'readOnly' => 1,
                    'minitems' => 1,
                    'maxitems' => 1,
                ],
            ],
            'expectedTca' => [
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
                        'types' => [
                            '0' => [
                                'showitem' => '--palette--;;imageoverlayPalette,--palette--;;filePalette',
                            ],
                            AbstractFile::FILETYPE_TEXT => [
                                'showitem' => '--palette--;;imageoverlayPalette,--palette--;;filePalette',
                            ],
                            AbstractFile::FILETYPE_IMAGE => [
                                'showitem' => '--palette--;;imageoverlayPalette,--palette--;;filePalette',
                            ],
                            AbstractFile::FILETYPE_AUDIO => [
                                'showitem' => '--palette--;;audioOverlayPalette,--palette--;;filePalette',
                            ],
                            AbstractFile::FILETYPE_VIDEO => [
                                'showitem' => '--palette--;;videoOverlayPalette,--palette--;;filePalette',
                            ],
                            AbstractFile::FILETYPE_APPLICATION => [
                                'showitem' => '--palette--;;imageoverlayPalette,--palette--;;filePalette',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        yield 'falsy values' => [
            'config' => [
                'enableImageManipulation' => 0,
                'properties' => [
                    'non_available_field' => 'foo',
                    'allowed' => '',
                    'disallowed' => '',
                    'appearance' => [],
                    'behaviour' => [],
                    'readOnly' => 0,
                    'minitems' => 0,
                    'maxitems' => 0,
                ],
            ],
            'expectedTca' => [
                'config' => [
                    'type' => 'file',
                ],
            ],
        ];

        yield 'allowed and disallowed accept arrays' => [
            'config' => [
                'enableImageManipulation' => 0,
                'properties' => [
                    'non_available_field' => 'foo',
                    'allowed' => ['common-image-types'],
                    'disallowed' => ['png'],
                ],
            ],
            'expectedTca' => [
                'config' => [
                    'type' => 'file',
                    'allowed' => ['common-image-types'],
                    'disallowed' => ['png'],
                ],
            ],
        ];
    }

    /**
     * @test
     * @dataProvider getTcaReturnsExpectedTcaDataProvider
     */
    public function getTcaReturnsExpectedTca(array $config, array $expectedTca): void
    {
        $fieldConfiguration = FileFieldConfiguration::createFromArray($config);

        self::assertSame($expectedTca, $fieldConfiguration->getTca());
    }

    public static function getSqlReturnsExpectedSqlDefinitionDataProvider(): iterable
    {
        yield 'default integer column' => [
            'uniqueColumnName' => 'cb_example_myText',
            'expectedSql' => '`cb_example_myText` int(11) DEFAULT \'0\' NOT NULL',
        ];
    }

    /**
     * @test
     * @dataProvider getSqlReturnsExpectedSqlDefinitionDataProvider
     */
    public function getSqlReturnsExpectedSqlDefinition(string $uniqueColumnName, string $expectedSql): void
    {
        $inputFieldConfiguration = FileFieldConfiguration::createFromArray([]);

        self::assertSame($expectedSql, $inputFieldConfiguration->getSql($uniqueColumnName));
    }
}
