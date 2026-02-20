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

final class CollectionFieldSchemaTest extends UnitTestCase
{
    public static function collectionFieldSchemaValidationWorksAsExpectedDataProvider(): iterable
    {
        yield 'Only type' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'collection',
                        'type' => 'Collection',
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
                        'identifier' => 'collection',
                        'type' => 'Collection',
                        'label' => 'Collection Label',
                        'description' => 'Collection Description',
                        'labelField' => 'title',
                        'fallbackLabelFields' => ['description'],
                        'table' => 'tx_myext_collection',
                        'shareAcrossTables' => true,
                        'shareAcrossFields' => true,
                        'allowedRecordTypes' => ['type1', 'type2'],
                        'appearance' => (object)[
                            'collapseAll' => true,
                            'expandSingle' => true,
                            'showNewRecordLink' => true,
                            'newRecordLinkAddTitle' => true,
                            'newRecordLinkTitle' => 'LLL:EXT:myext/Resources/Private/Language/locallang.xlf:new_record',
                            'createNewRelationLinkTitle' => 'LLL:EXT:myext/Resources/Private/Language/locallang.xlf:create_new',
                            'levelLinksPosition' => 'bottom',
                            'useCombination' => true,
                            'suppressCombinationWarning' => true,
                            'useSortable' => true,
                            'showPossibleLocalizationRecords' => true,
                            'showAllLocalizationLink' => true,
                            'showSynchronizationLink' => true,
                            'enabledControls' => (object)[
                                'info' => true,
                                'new' => true,
                                'dragdrop' => true,
                                'sort' => true,
                                'hide' => true,
                                'delete' => true,
                                'localize' => true,
                            ],
                            'showPossibleRecordsSelector' => true,
                            'elementBrowserEnabled' => true,
                        ],
                        'autoSizeMax' => 10,
                        'behaviour' => (object)[
                            'allowLanguageSynchronization' => true,
                            'disableMovingChildrenWithParent' => true,
                            'enableCascadingDelete' => true,
                        ],
                        'customControls' => [
                            (object)['userFunc' => 'Vendor\\Ext\\UserFunc->method'],
                        ],
                        'filter' => [
                            (object)[
                                'userFunc' => 'Vendor\\Ext\\Filter->method',
                                'parameters' => (object)['foo' => 'bar'],
                            ],
                        ],
                        'foreign_default_sortby' => 'title',
                        'foreign_field' => 'parent_uid',
                        'foreign_label' => 'title',
                        'foreign_match_fields' => (object)['fieldname' => 'collection'],
                        'foreign_selector' => 'uid_local',
                        'foreign_sortby' => 'sorting',
                        'fieldControl' => (object)['foo' => 'bar'],
                        'fieldInformation' => (object)['foo' => 'bar'],
                        'fieldWizard' => (object)[
                            'defaultLanguageDifferences' => (object)['foo' => 'bar'],
                            'localizationStateSelector' => (object)['foo' => 'bar'],
                            'otherLanguageContent' => (object)['foo' => 'bar'],
                        ],
                        'foreign_table' => 'tx_myext_child',
                        'foreign_table_field' => 'parent_table',
                        'foreign_unique' => 'uid_local',
                        'maxitems' => 5,
                        'minitems' => 1,
                        'MM' => 'tx_myext_mm',
                        'MM_opposite_field' => 'local_field',
                        'overrideChildTca' => (object)['columns' => (object)[]],
                        'readOnly' => true,
                        'relationship' => 'oneToMany',
                        'size' => 5,
                        'symmetric_field' => 'symmetric_field',
                        'symmetric_label' => 'symmetric_label',
                        'symmetric_sortby' => 'symmetric_sortby',
                        'fields' => [
                            (object)[
                                'identifier' => 'child_title',
                                'type' => 'Text',
                            ],
                        ],
                    ],
                ],
            ],
            'valid' => true,
        ];

        yield 'labelField as array' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'collection',
                        'type' => 'Collection',
                        'labelField' => ['title', 'subtitle'],
                    ],
                ],
            ],
            'valid' => true,
        ];

        yield 'invalid relationship' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'collection',
                        'type' => 'Collection',
                        'relationship' => 'invalid',
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'invalid levelLinksPosition' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'collection',
                        'type' => 'Collection',
                        'appearance' => (object)[
                            'levelLinksPosition' => 'invalid',
                        ],
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'unknown property' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'collection',
                        'type' => 'Collection',
                        'unknown' => 'property',
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'minitems too small' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'collection',
                        'type' => 'Collection',
                        'minitems' => 0,
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
                        'identifier' => 'collection',
                        'type' => 'Collection',
                        'autoSizeMax' => 0,
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'customControls userFunc missing' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'collection',
                        'type' => 'Collection',
                        'customControls' => [
                            (object)['foo' => 'bar'],
                        ],
                    ],
                ],
            ],
            'valid' => false,
        ];
    }

    #[Test]
    #[DataProvider('collectionFieldSchemaValidationWorksAsExpectedDataProvider')]
    public function collectionFieldSchemaValidationWorksAsExpected(object $data, bool $valid): void
    {
        $jsonSchemaValidator = new JsonSchemaValidator();

        $validationResult = $jsonSchemaValidator->validateContentElement($data, 'http://typo3.org/content-element.json');

        self::assertSame($valid, $validationResult);
    }
}
