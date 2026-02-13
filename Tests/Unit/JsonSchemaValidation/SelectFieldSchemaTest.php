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

namespace TYPO3\CMS\ContentBlocks\Tests\Unit\JsonSchemaValidation;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\ContentBlocks\JsonSchemaValidation\JsonSchemaValidator;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class SelectFieldSchemaTest extends UnitTestCase
{
    public static function selectFieldSchemaValidationWorksAsExpectedDataProvider(): iterable
    {
        yield 'Only type' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'select',
                        'type' => 'Select',
                    ],
                ],
            ],
            'valid' => true,
        ];

        yield 'Valid properties' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'select',
                        'type' => 'Select',
                        'allowNonIdValues' => true,
                        'appearance' => (object)[
                            'expandAll' => true,
                        ],
                        'authMode' => 'explicitAllow',
                        'autoSizeMax' => 10,
                        'behaviour' => (object)[
                            'allowLanguageSynchronization' => true,
                        ],
                        'dbFieldLength' => 255,
                        'dontRemapTablesOnCopy' => ['table1', 'table2'],
                        'default' => 'default_value',
                        'disableNoMatchingValueElement' => true,
                        'exclusiveKeys' => 'key1,key2',
                        'fieldControl' => (object)['foo' => 'bar'],
                        'fieldInformation' => (object)['foo' => 'bar'],
                        'fieldWizard' => (object)[
                            'defaultLanguageDifferences' => (object)['foo' => 'bar'],
                            'localizationStateSelector' => (object)['foo' => 'bar'],
                            'otherLanguageContent' => (object)['foo' => 'bar'],
                        ],
                        'fileFolderConfig' => (object)[
                            'allowedExtensions' => 'jpg,png',
                            'depth' => 10,
                            'folder' => 'EXT:my_ext/Resources/Public/Images',
                        ],
                        'foreign_table' => 'tx_my_table',
                        'foreign_table_item_group' => 'group_field',
                        'foreign_table_prefix' => 'Prefix: ',
                        'foreign_table_where' => 'AND 1=1',
                        'itemGroups' => (object)[
                            'group1' => 'Group 1',
                        ],
                        'items' => [
                            (object)[
                                'label' => 'Item 1',
                                'value' => 'value1',
                                'icon' => 'icon-name',
                                'group' => 'group1',
                                'description' => 'Description',
                            ],
                            (object)[
                                'label' => 'Item 2',
                                'value' => 2,
                                'description' => (object)[
                                    'title' => 'Title',
                                    'description' => 'Detailed description',
                                ],
                            ],
                        ],
                        'itemsProcessors' => (object)[
                            '10' => (object)[
                                'class' => 'My\\Class',
                                'parameters' => (object)['foo' => 'bar'],
                            ],
                        ],
                        'itemsProcFunc' => 'My\\Class->myMethod',
                        'itemsProcConfig' => (object)['foo' => 'bar'],
                        'localizeReferencesAtParentLocalization' => true,
                        'maxitems' => 10,
                        'minitems' => 1,
                        'MM' => 'tx_my_mm_table',
                        'MM_match_fields' => (object)['foo' => 'bar'],
                        'MM_opposite_field' => 'other_field',
                        'MM_oppositeUsage' => (object)['foo' => 'bar'],
                        'MM_table_where' => 'AND 1=1',
                        'multiple' => true,
                        'readOnly' => false,
                        'relationship' => 'oneToMany',
                        'renderType' => 'selectSingle',
                        'size' => 1,
                        'sortItems' => (object)[
                            'label' => 'asc',
                            'value' => 'desc',
                        ],
                        'treeConfig' => (object)[
                            'childrenField' => 'children',
                            'appearance' => (object)[
                                'showHeader' => true,
                                'expandAll' => true,
                                'maxLevels' => 5,
                                'nonSelectableLevels' => '0,1',
                            ],
                        ],
                    ],
                ],
            ],
            'valid' => true,
        ];

        yield 'unknown property' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'select',
                        'type' => 'Select',
                        'unknown' => 'unknown',
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'autoSizeMax too small' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'select',
                        'type' => 'Select',
                        'autoSizeMax' => 0,
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'dbFieldLength too small' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'select',
                        'type' => 'Select',
                        'dbFieldLength' => 0,
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'fileFolderConfig depth too large' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'select',
                        'type' => 'Select',
                        'fileFolderConfig' => (object)[
                            'depth' => 100,
                        ],
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'items without required value' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'select',
                        'type' => 'Select',
                        'items' => [
                            (object)[
                                'label' => 'Label',
                            ],
                        ],
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'invalid relationship' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'select',
                        'type' => 'Select',
                        'relationship' => 'manyToMany',
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'invalid renderType' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'select',
                        'type' => 'Select',
                        'renderType' => 'invalid',
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'size too small' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'select',
                        'type' => 'Select',
                        'size' => 0,
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'treeConfig missing childrenField or parentField' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'select',
                        'type' => 'Select',
                        'treeConfig' => (object)[
                            'dataProvider' => 'My\\Class',
                        ],
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'treeConfig with parentField' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'select',
                        'type' => 'Select',
                        'treeConfig' => (object)[
                            'parentField' => 'pid',
                        ],
                    ],
                ],
            ],
            'valid' => true,
        ];
    }

    #[Test]
    #[DataProvider('selectFieldSchemaValidationWorksAsExpectedDataProvider')]
    public function selectFieldSchemaValidationWorksAsExpected(object $data, bool $valid): void
    {
        $jsonSchemaValidator = new JsonSchemaValidator();

        $validationResult = $jsonSchemaValidator->validateContentElement($data, 'http://typo3.org/content-element.json');

        self::assertSame($valid, $validationResult);
    }
}
