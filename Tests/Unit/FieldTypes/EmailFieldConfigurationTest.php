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

use TYPO3\CMS\ContentBlocks\FieldConfiguration\EmailFieldConfiguration;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class EmailFieldConfigurationTest extends UnitTestCase
{
    /**
     * dataprovider for checking EmailFieldConfiguration
     */
    public function checkEmailFieldConfigurationDataProvider(): iterable
    {
        yield 'Check email field configurations.' => [
            'contentBlock' => [
                'EditorInterfaceXlf' => 'typo3conf/contentBlocks/example/src/Language/EditorInterface.xlf',
                'vendor' => 'typo3-contentblocks',
                'package' => 'example',
            ],
            'fieldsList' => [
                'email' => [
                    'identifier' => 'email',
                    'type' => 'Email',
                    'properties' => [
                        'autocomplete' => true,
                        'default' => 'developer@localhost.mail',
                        'placeholder' => 'Placeholder text',
                        'size' => 20,
                        'required' => true,
                        'trim' => true,
                    ],
                    '_path' => [],
                    '_identifier' => 'email',
                ],
            ],

            'uniqueColumnName' => 'cb_example_email',
            'expected' => [
                'getSql' => '`cb_example_email` VARCHAR(20) DEFAULT \'\' NOT NULL',
                'construct' => [
                    'identifier' => 'email',
                    'type' => 'Email',
                    'properties' => [
                        'autocomplete' => true,
                        'default' => 'developer@localhost.mail',
                        'placeholder' => 'Placeholder text',
                        'size' => 20,
                        'required' => true,
                        'trim' => true,
                    ],
                    '_path' => [],
                    '_identifier' => 'email',
                ],
            ],
        ];
    }

    /**
     * EmailFieldConfiguration Test
     *
     * @test
     * @dataProvider checkEmailFieldConfigurationDataProvider
     */
    public function checkEmailFieldConfiguration(array $contentBlock, array $fieldsList, string $uniqueColumnName, array $expected): void
    {
        // Email field test
        $emailField = new EmailFieldConfiguration($fieldsList['email']);
        self::assertSame($expected['getSql'], $emailField->getSql($uniqueColumnName));

        self::assertSame($expected['construct'], $emailField->toArray());
    }
}
