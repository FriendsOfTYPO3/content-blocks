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

use TYPO3\CMS\ContentBlocks\FieldConfiguration\AbstractFieldConfiguration;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class AbstractFieldConfigurationTest extends UnitTestCase
{
    /**
     * dataprovider for checking AbstractFieldConfiguration
     */
    public function checkAbstractFieldConfigurationDataProvider(): iterable
    {
        yield 'Check abstract field configurations.' => [
            'config' => [
                'identifier' => 'text',
                'languagePath' => 'test-path-for-abstract.xlf:test',
                'properties' => [
                    'autocomplete' => true,
                    'default' => 'Default value',
                    'max' => 15,
                    'placeholder' => 'Placeholder text',
                    'size' => 20,
                    'required' => false,
                    'trim' => true,
                ],
                '_path' => [],
                '_identifier' => 'text',
            ],
            'expected' => [
                'create' => 'text', // Create result (create from array via construct)
                'getTcaTemplate' => [ // Check getTcaTemplate
                    'label' => 'LLL:test-path-for-abstract.xlf:test.label',
                    'description' => 'LLL:test-path-for-abstract.xlf:test.description',
                    'config' => [],
                    'exclude' => 1,
                ],
            ],
        ];
    }

    /**
     * AbstractFieldConfiguration Test
     *
     * @test
     * @dataProvider checkAbstractFieldConfigurationDataProvider
     */
    public function checkAbstractFieldConfiguration(array $config, array $expected): void
    {
        // AbstractFieldConfiguration Test

        $fieldsConfig = new AbstractFieldConfiguration($config);
        self::assertSame($expected['create'], $fieldsConfig->identifier);

        self::assertSame($expected['getTcaTemplate'], $fieldsConfig->getTcaTemplate());
    }
}
