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

final class RelationFieldSchemaTest extends UnitTestCase
{
    public static function relationFieldSchemaValidationWorksAsExpectedDataProvider(): iterable
    {
        yield 'Only type' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'relation',
                        'type' => 'Relation',
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
                        'identifier' => 'relation',
                        'type' => 'Relation',
                        'allowed' => 'pages,tt_content',
                        'autoSizeMax' => 10,
                        'behaviour' => (object)[
                            'allowLanguageSynchronization' => true,
                        ],
                        'default' => 0,
                        'dontRemapTablesOnCopy' => ['pages'],
                        'elementBrowserEntryPoints' => (object)[
                            '_default' => 123,
                            'pages' => 456,
                        ],
                        'fieldControl' => (object)[
                            'addRecord' => (object)['disabled' => false],
                            'editPopup' => (object)['disabled' => false],
                            'listModule' => (object)['disabled' => false],
                            'elementBrowser' => (object)['disabled' => false],
                            'insertClipboard' => (object)['disabled' => false],
                        ],
                        'fieldInformation' => (object)['foo' => 'bar'],
                        'fieldWizard' => (object)[
                            'defaultLanguageDifferences' => (object)['foo' => 'bar'],
                            'localizationStateSelector' => (object)['foo' => 'bar'],
                            'otherLanguageContent' => (object)['foo' => 'bar'],
                            'recordsOverview' => (object)['disabled' => false],
                            'tableList' => (object)['disabled' => false],
                        ],
                        'filter' => [
                            (object)[
                                'userFunc' => 'My\\Class->myFilter',
                                'parameters' => (object)['foo' => 'bar'],
                            ],
                        ],
                        'foreign_table' => 'tt_content',
                        'hideDeleteIcon' => true,
                        'hideMoveIcons' => true,
                        'hideSuggest' => true,
                        'localizeReferencesAtParentLocalization' => true,
                        'maxitems' => 10,
                        'minitems' => 1,
                        'MM' => 'tx_my_mm',
                        'MM_match_fields' => (object)['foo' => 'bar'],
                        'MM_opposite_field' => 'other_field',
                        'MM_oppositeUsage' => (object)['foo' => 'bar'],
                        'MM_table_where' => 'AND 1=1',
                        'multiple' => true,
                        'prepend_tname' => true,
                        'readOnly' => false,
                        'relationship' => 'oneToMany',
                        'size' => 1,
                        'suggestOptions' => (object)[
                            'additionalSearchFields' => 'header',
                            'addWhere' => 'AND pid=0',
                            'cssClass' => 'my-class',
                            'maxItemsInResultList' => 5,
                            'maxPathTitleLength' => 50,
                            'minimumCharacters' => 3,
                            'orderBy' => 'header',
                            'pidList' => '0,1',
                            'pidDepth' => 1,
                            'receiverClass' => 'My\\Class',
                            'renderFunc' => 'My\\Class->render',
                            'searchCondition' => 'header LIKE "%foo%"',
                            'searchWholePhrase' => true,
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
                        'identifier' => 'relation',
                        'type' => 'Relation',
                        'unknown' => 'unknown',
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
                        'identifier' => 'relation',
                        'type' => 'Relation',
                        'minitems' => 0,
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
                        'identifier' => 'relation',
                        'type' => 'Relation',
                        'relationship' => 'manyToMany',
                    ],
                ],
            ],
            'valid' => false,
        ];
    }

    #[Test]
    #[DataProvider('relationFieldSchemaValidationWorksAsExpectedDataProvider')]
    public function relationFieldSchemaValidationWorksAsExpected(object $data, bool $valid): void
    {
        $jsonSchemaValidator = new JsonSchemaValidator();

        $validationResult = $jsonSchemaValidator->validateContentElement($data, 'http://typo3.org/content-element.json');

        self::assertSame($valid, $validationResult);
    }
}
