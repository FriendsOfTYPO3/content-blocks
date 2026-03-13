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

final class FileFieldSchemaTest extends UnitTestCase
{
    public static function fileFieldSchemaValidationWorksAsExpectedDataProvider(): iterable
    {
        yield 'Only type' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'file',
                        'type' => 'File',
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
                        'identifier' => 'file',
                        'type' => 'File',
                        'label' => 'File Label',
                        'description' => 'File Description',
                        'useExistingField' => false,
                        'prefixField' => false,
                        'prefixType' => 'vendor',
                        'displayCond' => 'FIELD:title:REQ:true',
                        'onChange' => 'reload',
                        'allowed' => 'jpg,png',
                        'disallowed' => 'php,exe',
                        'appearance' => (object)[
                            'collapseAll' => true,
                            'expandSingle' => false,
                            'createNewRelationLinkTitle' => 'Create new relation',
                            'addMediaLinkTitle' => 'Add media by URL',
                            'uploadFilesLinkTitle' => 'Select & upload files',
                            'useSortable' => true,
                            'showPossibleLocalizationRecords' => true,
                            'showAllLocalizationLink' => true,
                            'showSynchronizationLink' => true,
                            'enabledControls' => (object)[
                                'edit' => true,
                                'info' => true,
                                'dragdrop' => true,
                                'sort' => false,
                                'hide' => true,
                                'delete' => true,
                                'localize' => true,
                            ],
                            'headerThumbnail' => (object)[
                                'width' => 100,
                                'height' => '100',
                            ],
                            'fileUploadAllowed' => true,
                            'fileByUrlAllowed' => true,
                            'elementBrowserEnabled' => true,
                        ],
                        'behaviour' => (object)[
                            'allowLanguageSynchronization' => false,
                            'disableMovingChildrenWithParent' => false,
                            'enableCascadingDelete' => true,
                        ],
                        'fieldInformation' => (object)[],
                        'fieldWizard' => (object)[
                            'defaultLanguageDifferences' => (object)[],
                            'localizationStateSelector' => (object)[],
                            'otherLanguageContent' => (object)[],
                        ],
                        'maxitems' => 5,
                        'minitems' => 1,
                        'overrideChildTca' => (object)[],
                        'readOnly' => false,
                        'relationship' => 'oneToMany',
                        'extendedPalette' => false,
                        'cropVariants' => (object)[
                            'default' => (object)[
                                'title' => 'Teaser',
                                'allowedAspectRatios' => (object)[
                                    '16/9' => (object)[
                                        'title' => '16:9',
                                        'value' => '16 / 9',
                                    ],
                                    'landscape' => (object)[
                                        'title' => 'Landscape',
                                        'value' => 1.333,
                                    ],
                                ],
                                'coverAreas' => [
                                    (object)[
                                        'x' => 0.25,
                                        'y' => 0.3,
                                        'width' => 100,
                                        'height' => 100,
                                    ],
                                ],
                                'focusArea' => (object)[
                                    'x' => 1,
                                    'y' => 1,
                                    'width' => 100,
                                    'height' => 100,
                                ],
                                'cropArea' => (object)[
                                    'x' => 0.1,
                                    'y' => 0.2,
                                    'width' => 0.7,
                                    'height' => 0.6,
                                ],
                                "selectedRatio" => "landscape",
                            ],
                            "other" => (object)[
                                "disabled" => true
                            ],
                        ],
                    ],
                ],
            ],
            'valid' => true,
        ];

        yield 'allowed as comma list' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'file',
                        'type' => 'File',
                        'allowed' => 'jpg,png',
                    ],
                ],
            ],
            'valid' => true,
        ];

        yield 'allowed as enum' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'file',
                        'type' => 'File',
                        'allowed' => 'common-image-types',
                    ],
                ],
            ],
            'valid' => true,
        ];

        yield 'allowed as array' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'file',
                        'type' => 'File',
                        'allowed' => ['jpg', 'png'],
                    ],
                ],
            ],
            'valid' => true,
        ];

        yield 'disallowed as comma list' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'file',
                        'type' => 'File',
                        'disallowed' => 'exe,php',
                    ],
                ],
            ],
            'valid' => true,
        ];

        yield 'disallowed as enum' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'file',
                        'type' => 'File',
                        'disallowed' => 'common-media-types',
                    ],
                ],
            ],
            'valid' => true,
        ];

        yield 'disallowed as array' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'file',
                        'type' => 'File',
                        'disallowed' => ['exe', 'php'],
                    ],
                ],
            ],
            'valid' => true,
        ];

        yield 'maxitems too small' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'file',
                        'type' => 'File',
                        'maxitems' => 0,
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
                        'identifier' => 'file',
                        'type' => 'File',
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
                        'identifier' => 'file',
                        'type' => 'File',
                        'relationship' => 'manyToMany',
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
                        'identifier' => 'file',
                        'type' => 'File',
                        'unknown' => 'unknown',
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'appearance unknown property' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'file',
                        'type' => 'File',
                        'appearance' => (object)[
                            'unknown' => 'unknown',
                        ],
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'enabledControls unknown property' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'file',
                        'type' => 'File',
                        'appearance' => (object)[
                            'enabledControls' => (object)[
                                'unknown' => 'unknown',
                            ],
                        ],
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'overrideType valid' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'file',
                        'type' => 'File',
                        'overrideType' => (object)[
                            'image' => [
                                (object)[
                                    'identifier' => 'text',
                                    'type' => 'Text',
                                ],
                            ],
                            'video' => [
                                (object)[
                                    'identifier' => 'text',
                                    'type' => 'Text',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'valid' => true,
        ];

        yield 'overrideType invalid key' => [
            'data' => (object)[
                'name' => 'json/schema-test',
                'fields' => [
                    (object)[
                        'identifier' => 'file',
                        'type' => 'File',
                        'overrideType' => (object)[
                            'invalid' => [
                                (object)[
                                    'identifier' => 'text',
                                    'type' => 'Text',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'valid' => false,
        ];
    }

    #[Test]
    #[DataProvider('fileFieldSchemaValidationWorksAsExpectedDataProvider')]
    public function fileFieldSchemaValidationWorksAsExpected(object $data, bool $valid): void
    {
        $jsonSchemaValidator = new JsonSchemaValidator();

        $validationResult = $jsonSchemaValidator->isValidContentElement($data);

        self::assertSame($valid, $validationResult);
    }
}
