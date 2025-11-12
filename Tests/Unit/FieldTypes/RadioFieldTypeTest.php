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
use TYPO3\CMS\ContentBlocks\Tests\Unit\Fixtures\FieldTypeRegistryTestFactory;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class RadioFieldTypeTest extends UnitTestCase
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
                'non_available_field' => 'foo',
                'default' => 1,
                'readOnly' => 1,
                'itemsProcFunc' => 'foo',
                'itemsProcessors' => [
                    100 => [
                        'class' => 'Foobar',
                    ],
                ],
                'items' => [
                    'foo',
                ],
                'itemsProcConfig' => [
                    'foo' => 'bar',
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
                    'type' => 'radio',
                    'default' => 1,
                    'readOnly' => true,
                    'itemsProcFunc' => 'foo',
                    'itemsProcessors' => [
                        100 => [
                            'class' => 'Foobar',
                        ],
                    ],
                    'items' => [
                        'foo',
                    ],
                    'itemsProcConfig' => [
                        'foo' => 'bar',
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
                'non_available_field' => 'foo',
                'default' => '',
                'readOnly' => 0,
                'itemsProcFunc' => '',
                'itemsProcessors' => [],
                'items' => [],
            ],
            'expectedTca' => [
                'config' => [
                    'type' => 'radio',
                ],
            ],
        ];
    }

    #[DataProvider('getTcaReturnsExpectedTcaDataProvider')]
    #[Test]
    public function getTcaReturnsExpectedTca(array $config, array $expectedTca): void
    {
        $fieldTypeRegistry = FieldTypeRegistryTestFactory::create();
        $fieldType = $fieldTypeRegistry->get('Radio');
        $fieldConfiguration = $fieldType->createFromArray($config);

        self::assertSame($expectedTca, $fieldConfiguration->getTca());
    }
}
