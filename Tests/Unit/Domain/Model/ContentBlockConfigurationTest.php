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

namespace TYPO3\CMS\ContentBlocks\Tests\Unit\Domain\Model;

use TYPO3\CMS\ContentBlocks\Domain\Model\ContentBlockConfiguration;
use TYPO3\CMS\ContentBlocks\Factory\ContentBlockConfigurationFactory;
use TYPO3\CMS\ContentBlocks\Service\ConfigurationService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class ContentBlockConfigurationTest extends UnitTestCase
{
    public function checkContentBlockConfigurationDataProvider(): iterable
    {
        yield 'Check ContentBlockConfiguration' => [
            'contentBlock' => [
                'test' => true, // TODO: need a content block to complete test
                'icon' => 'typo3conf/contentBlocks/example-local/ContentBlockIcon.svg',
                'composerJson' => [
                    "name" => "typo3-contentblocks/example-local",
                    "description" => "Content block providing examples for all field types.",
                    "type" => "typo3-contentblock",
                ],
                'yaml' => [
                    'group' => 'common',
                    'fields' =>
                        [
                            0 =>
                                [
                                    'identifier' => 'text',
                                    'type' => 'Text',
                                    'languagePath' => 'test-language-path.xlf:temp',
                                    'properties' =>
                                        [
                                            'autocomplete' => true,
                                            'default' => 'Default value',
                                            'max' => 15,
                                            'placeholder' => 'Placeholder text',
                                            'size' => 20,
                                            'required' => false,
                                            'trim' => true,
                                        ],
                                    '_path' =>
                                        [
                                            0 => 'text',
                                        ],
                                    '_identifier' => 'text',
                                ],
                            1 =>
                                [
                                    'identifier' => 'textarea',
                                    'type' => 'Textarea',
                                    'languagePath' => 'test-language-path.xlf:temp',
                                    'properties' =>
                                        [
                                            'cols' => 40,
                                            'default' => 'Default value',
                                            'enableRichtext' => true,
                                            'max' => 150,
                                            'placeholder' => 'Placeholder text',
                                            'richtextConfiguration' => 'default',
                                            'rows' => 15,
                                            'required' => false,
                                            'trim' => true,
                                        ],
                                    '_path' =>
                                        [
                                            0 => 'textarea',
                                        ],
                                    '_identifier' => 'textarea',
                                ],
                            2 =>
                                [
                                    'identifier' => 'email',
                                    'type' => 'Email',
                                    'languagePath' => 'test-language-path.xlf:temp',
                                    'properties' =>
                                        [
                                            'autocomplete' => true,
                                            'default' => 'developer@localhost',
                                            'placeholder' => 'Placeholder text',
                                            'size' => 20,
                                            'required' => true,
                                            'trim' => true,
                                        ],
                                    '_path' =>
                                        [
                                            0 => 'email',
                                        ],
                                    '_identifier' => 'email',
                                ],
                        ],
                ],
            ],
            'expected' => [
                'create' => [
                    '__warning' => 'Contents of this "cb" configuration are not API yet and might change!',
                    'vendor' => 'typo3-contentblocks',
                    'package' => 'example-local',
                    'key' => 'example-local',
                    'path' => 'typo3conf/contentBlocks/example-local/',
                    'privatePath' => 'typo3conf/contentBlocks/example-local/Resources/Private/',
                    'publicPath' => 'typo3conf/contentBlocks/example-local/Resources/Public/',
                    'icon' => 'typo3conf/contentBlocks/example-local/ContentBlockIcon.svg',
                    'iconProviderClass' => 'TYPO3\\CMS\\Core\\Imaging\\IconProvider\\SvgIconProvider',
                    'CType' => 'typo3-contentblocks_example-local',
                    'composerJson' => [
                        "name" => "typo3-contentblocks/example-local",
                        "description" => "Content block providing examples for all field types.",
                        "type" => "typo3-contentblock",
                        'license' => 'GPL-2.0-or-later',
                        'require' => [
                            'typo3/cms-content-blocks' => '*',
                        ],
                    ],
                    'fields' =>
                        [
                            'text' =>
                                [
                                    'identifier' => 'text',
                                    'type' => 'input',
                                    // 'languagePath' => 'test-language-path.xlf:temp',
                                    'properties' =>
                                        [
                                            'autocomplete' => true,
                                            'default' => 'Default value',
                                            'max' => 15,
                                            'placeholder' => 'Placeholder text',
                                            'size' => 20,
                                            'required' => false,
                                        ],
                                    '_path' =>
                                        [
                                            0 => 'text',
                                        ],
                                    '_identifier' => 'text',
                                ],
                            'textarea' =>
                                [
                                    'identifier' => 'textarea',
                                    'type' => 'text',
                                    // 'languagePath' => 'test-language-path.xlf:temp',
                                    'properties' =>
                                        [
                                            'cols' => 40,
                                            'default' => 'Default value',
                                            'enableRichtext' => true,
                                            'max' => 150,
                                            'placeholder' => 'Placeholder text',
                                            'richtextConfiguration' => 'default',
                                            'rows' => 15,
                                            'required' => false,
                                        ],
                                    '_path' =>
                                        [
                                            0 => 'textarea',
                                        ],
                                    '_identifier' => 'textarea',
                                ],
                            'email' =>
                                [
                                    'identifier' => 'email',
                                    'type' => 'email',
                                    // 'languagePath' => 'test-language-path.xlf:temp',
                                    'properties' =>
                                        [
                                            'autocomplete' => true,
                                            'default' => 'developer@localhost',
                                            'placeholder' => 'Placeholder text',
                                            'size' => 20,
                                            'required' => true,
                                        ],
                                    '_path' =>
                                        [
                                            0 => 'email',
                                        ],
                                    '_identifier' => 'email',
                                ],
                            ],
                    'frontendTemplatesPath' => 'typo3conf/contentBlocks/example-local/Resources/Private',
                    'frontendPartialsPath' => 'typo3conf/contentBlocks/example-local/Resources/Private/Partials',
                    'frontendLayoutsPath' => 'typo3conf/contentBlocks/example-local/Resources/Private/Layouts',
                    'EditorPreview.html' => 'typo3conf/contentBlocks/example-local/Resources/Private/EditorPreview.html',
                    'labelsXlfPath' => 'typo3conf/contentBlocks/example-local/Resources/Private/Language/Labels.xlf',
                    'EditorLLL' => 'LLL:typo3conf/contentBlocks/example-local/Resources/Private/Language/Labels.xlf:typo3-contentblocks.example-local',
                    'FrontendLLL' => 'LLL:typo3conf/contentBlocks/example-local/Resources/Private/Language/Labels.xlf:typo3-contentblocks.example-local',
                    'composerJson' => [
                        "name" => "typo3-contentblocks/example-local",
                        "description" => "Content block providing examples for all field types.",
                        "type" => "typo3-contentblock",
                        'license' => 'GPL-2.0-or-later',
                        'require' => [
                            'typo3/cms-content-blocks' => '*',
                        ],
                    ],
                    'yaml' =>
                        [
                            'group' => 'common',
                            'fields' =>
                                [
                                    0 =>
                                        [
                                            'identifier' => 'text',
                                            'type' => 'input',
                                            'languagePath' => 'test-language-path.xlf:temp',
                                            'properties' =>
                                                [
                                                    'autocomplete' => true,
                                                    'default' => 'Default value',
                                                    'max' => 15,
                                                    'placeholder' => 'Placeholder text',
                                                    'size' => 20,
                                                    'required' => false,
                                                    'trim' => true,
                                                ],
                                            '_path' =>
                                                [
                                                    0 => 'text',
                                                ],
                                            '_identifier' => 'text',
                                        ],
                                    1 =>
                                        [
                                            'identifier' => 'textarea',
                                            'type' => 'text',
                                            'languagePath' => 'test-language-path.xlf:temp',
                                            'properties' =>
                                                [
                                                    'cols' => 40,
                                                    'default' => 'Default value',
                                                    'enableRichtext' => true,
                                                    'max' => 150,
                                                    'placeholder' => 'Placeholder text',
                                                    'richtextConfiguration' => 'default',
                                                    'rows' => 15,
                                                    'required' => false,
                                                    'trim' => true,
                                                ],
                                            '_path' =>
                                                [
                                                    0 => 'textarea',
                                                ],
                                            '_identifier' => 'textarea',
                                        ],
                                    2 =>
                                        [
                                            'identifier' => 'email',
                                            'type' => 'email',
                                            'languagePath' => 'test-language-path.xlf:temp',
                                            'properties' =>
                                                [
                                                    'autocomplete' => true,
                                                    'default' => 'developer@localhost',
                                                    'placeholder' => 'Placeholder text',
                                                    'size' => 20,
                                                    'required' => true,
                                                    'trim' => true,
                                                ],
                                            '_path' =>
                                                [
                                                    0 => 'email',
                                                ],
                                            '_identifier' => 'email',
                                        ],
                                ],
                        ],
                ],
            ],
        ];
    }

    /**
     * ContentBlockConfiguration Test
     *
     * @test
     * @dataProvider checkContentBlockConfigurationDataProvider
     */
    public function checkContentBlockConfiguration(array $contentBlock, array $expected)
    {
        $this->resetSingletonInstances = true;
        $configurationService = new ConfigurationService();

        /** @var ContentBlockConfigurationFactory $contentBlockConfigurationFactory */
        // $contentBlockConfigurationFactory = GeneralUtility::makeInstance(ContentBlockConfigurationFactory::class);
        $contentBlockConfigurationFactory = new ContentBlockConfigurationFactory(
            $configurationService
        );

        $contentBlockConfiguration = $contentBlockConfigurationFactory->createFromArray($contentBlock);
        // self::assertSame($expected['create'], $contentBlockConfiguration->toArray());
    }

    /* dump of an (old) complete ContentBlock configuration (THE all in example):
                'typo3-contentblocks_example-local' =>
                    [
                        '__warning' => 'Contents of this "cb" configuration are not API yet and might change!',
                        'vendor' => 'typo3-contentblocks',
                        'package' => 'example-local',
                        'key' => 'example-local',
                        'path' => 'typo3conf/contentBlocks/example-local/',
                        'srcPath' => 'typo3conf/contentBlocks/example-local/Resources/Private/',
                        'distPath' => 'typo3conf/contentBlocks/example-local/Resources/Public/',
                        'icon' => 'typo3conf/contentBlocks/example-local/ContentBlockIcon.svg',
                        'iconProviderClass' => 'TYPO3\\CMS\\Core\\Imaging\\IconProvider\\SvgIconProvider',
                        'CType' => 'typo3-contentblocks_example-local',
                        'fields' =>
                            [
                                'text' =>
                                    [
                                        'identifier' => 'text',
                                        'type' => 'Text',
                                        'properties' =>
                                            [
                                                'autocomplete' => true,
                                                'default' => 'Default value',
                                                'max' => 15,
                                                'placeholder' => 'Placeholder text',
                                                'size' => 20,
                                                'required' => false,
                                                'trim' => true,
                                            ],
                                        '_path' =>
                                            [
                                                0 => 'text',
                                            ],
                                        '_identifier' => 'text',
                                    ],
                                'textarea' =>
                                    [
                                        'identifier' => 'textarea',
                                        'type' => 'Textarea',
                                        'properties' =>
                                            [
                                                'cols' => 40,
                                                'default' => 'Default value',
                                                'enableRichtext' => true,
                                                'max' => 150,
                                                'placeholder' => 'Placeholder text',
                                                'richtextConfiguration' => 'default',
                                                'rows' => 15,
                                                'required' => false,
                                                'trim' => true,
                                            ],
                                        '_path' =>
                                            [
                                                0 => 'textarea',
                                            ],
                                        '_identifier' => 'textarea',
                                    ],
                                'email' =>
                                    [
                                        'identifier' => 'email',
                                        'type' => 'Email',
                                        'properties' =>
                                            [
                                                'autocomplete' => true,
                                                'default' => 'developer@localhost',
                                                'placeholder' => 'Placeholder text',
                                                'size' => 20,
                                                'required' => true,
                                                'trim' => true,
                                            ],
                                        '_path' =>
                                            [
                                                0 => 'email',
                                            ],
                                        '_identifier' => 'email',
                                    ],
                                'integer' =>
                                    [
                                        'identifier' => 'integer',
                                        'type' => 'Integer',
                                        'properties' =>
                                            [
                                                'default' => 0,
                                                'size' => 20,
                                                'required' => true,
                                                'trim' => true,
                                            ],
                                        '_path' =>
                                            [
                                                0 => 'integer',
                                            ],
                                        '_identifier' => 'integer',
                                    ],
                                'money' =>
                                    [
                                        'identifier' => 'money',
                                        'type' => 'Money',
                                        'properties' =>
                                            [
                                                'size' => 20,
                                                'required' => true,
                                                'trim' => true,
                                            ],
                                        '_path' =>
                                            [
                                                0 => 'money',
                                            ],
                                        '_identifier' => 'money',
                                    ],
                                'number' =>
                                    [
                                        'identifier' => 'number',
                                        'type' => 'Number',
                                        'properties' =>
                                            [
                                                'default' => 0,
                                                'size' => 20,
                                                'required' => true,
                                                'trim' => true,
                                            ],
                                        '_path' =>
                                            [
                                                0 => 'number',
                                            ],
                                        '_identifier' => 'number',
                                    ],
                                'percent' =>
                                    [
                                        'identifier' => 'percent',
                                        'type' => 'Percent',
                                        'properties' =>
                                            [
                                                'default' => 0,
                                                'range' =>
                                                    [
                                                        'lower' => 0,
                                                        'upper' => 100,
                                                    ],
                                                'required' => true,
                                                'size' => 20,
                                                'slider' =>
                                                    [
                                                        'step' => 1,
                                                        'width' => 100,
                                                    ],
                                                'trim' => true,
                                            ],
                                        '_path' =>
                                            [
                                                0 => 'percent',
                                            ],
                                        '_identifier' => 'percent',
                                    ],
                                'url' =>
                                    [
                                        'identifier' => 'url',
                                        'type' => 'Url',
                                        'properties' =>
                                            [
                                                'autocomplete' => true,
                                                'default' => 'https://typo3.org',
                                                'linkPopup' =>
                                                    [
                                                        'allowedExtensions' => 'pdf',
                                                        'blindLinkFields' => 'target,title',
                                                        'blindLinkOptions' => 'folder,spec,telefone,mail',
                                                    ],
                                                'max' => 150,
                                                'placeholder' => 'Placeholder text',
                                                'size' => 20,
                                                'required' => false,
                                            ],
                                        '_path' =>
                                            [
                                                0 => 'url',
                                            ],
                                        '_identifier' => 'url',
                                    ],
                                'tel' =>
                                    [
                                        'identifier' => 'tel',
                                        'type' => 'Tel',
                                        'properties' =>
                                            [
                                                'autocomplete' => true,
                                                'default' => 0,
                                                'size' => 20,
                                                'required' => false,
                                            ],
                                        '_path' =>
                                            [
                                                0 => 'tel',
                                            ],
                                        '_identifier' => 'tel',
                                    ],
                                'color' =>
                                    [
                                        'identifier' => 'color',
                                        'type' => 'Color',
                                        'properties' =>
                                            [
                                                'autocomplete' => true,
                                                'default' => '#0000aa',
                                                'size' => 5,
                                                'required' => false,
                                                'valuePicker' =>
                                                    [
                                                        'items' =>
                                                            [
                                                                '#FF0000' => 'Red',
                                                                '#008000' => 'Green',
                                                                '#0000FF' => 'Blue',
                                                            ],
                                                    ],
                                            ],
                                        '_path' =>
                                            [
                                                0 => 'color',
                                            ],
                                        '_identifier' => 'color',
                                    ],
                                'date' =>
                                    [
                                        'identifier' => 'date',
                                        'type' => 'Date',
                                        'properties' =>
                                            [
                                                'default' => '2020-12-12',
                                                'displayAge' => true,
                                                'size' => 20,
                                                'range' =>
                                                    [
                                                        'lower' => '2019-12-12',
                                                        'upper' => '2035-12-12',
                                                    ],
                                                'required' => true,
                                            ],
                                        '_path' =>
                                            [
                                                0 => 'date',
                                            ],
                                        '_identifier' => 'date',
                                    ],
                                'datetime' =>
                                    [
                                        'identifier' => 'datetime',
                                        'type' => 'DateTime',
                                        'properties' =>
                                            [
                                                'default' => '2020-01-31 12:00:00',
                                                'displayAge' => true,
                                                'size' => 20,
                                                'range' =>
                                                    [
                                                        'lower' => '2019-01-31 12:00:00',
                                                        'upper' => '2040-01-31 12:00:00',
                                                    ],
                                                'required' => true,
                                            ],
                                        '_path' =>
                                            [
                                                0 => 'datetime',
                                            ],
                                        '_identifier' => 'datetime',
                                    ],
                                'time' =>
                                    [
                                        'identifier' => 'time',
                                        'type' => 'Time',
                                        'properties' =>
                                            [
                                                'default' => '15:30',
                                                'displayAge' => true,
                                                'size' => 20,
                                                'range' =>
                                                    [
                                                        'lower' => '06:01',
                                                        'upper' => '17:59',
                                                    ],
                                                'required' => true,
                                            ],
                                        '_path' =>
                                            [
                                                0 => 'time',
                                            ],
                                        '_identifier' => 'time',
                                    ],
                                'select' =>
                                    [
                                        'identifier' => 'select',
                                        'type' => 'Select',
                                        'properties' =>
                                            [
                                                'items' =>
                                                    [
                                                        'one' => 'The first',
                                                        'two' => 'The second',
                                                        'three' => 'The third',
                                                    ],
                                                'prependLabel' => 'Please choose',
                                            ],
                                        '_path' =>
                                            [
                                                0 => 'select',
                                            ],
                                        '_identifier' => 'select',
                                    ],
                                'selectSideBySide' =>
                                    [
                                        'identifier' => 'selectSideBySide',
                                        'type' => 'MultiSelect',
                                        'properties' =>
                                            [
                                                'maxItems' => 2,
                                                'size' => 5,
                                                'items' =>
                                                    [
                                                        'one' => 'The first',
                                                        'two' => 'The second',
                                                        'three' => 'The third',
                                                    ],
                                            ],
                                        '_path' =>
                                            [
                                                0 => 'selectSideBySide',
                                            ],
                                        '_identifier' => 'selectSideBySide',
                                    ],
                                'checkboxes' =>
                                    [
                                        'identifier' => 'checkboxes',
                                        'type' => 'Checkbox',
                                        'properties' =>
                                            [
                                                'items' =>
                                                    [
                                                        'one' => 'The first',
                                                        'two' => 'The second',
                                                        'three' => 'The third',
                                                    ],
                                                'default' => 2,
                                            ],
                                        '_path' =>
                                            [
                                                0 => 'checkboxes',
                                            ],
                                        '_identifier' => 'checkboxes',
                                    ],
                                'radioboxes' =>
                                    [
                                        'identifier' => 'radioboxes',
                                        'type' => 'Radiobox',
                                        'properties' =>
                                            [
                                                'default' => 'two',
                                                'items' =>
                                                    [
                                                        'one' => 'The first',
                                                        'two' => 'The second',
                                                        'three' => 'The third',
                                                    ],
                                            ],
                                        '_path' =>
                                            [
                                                0 => 'radioboxes',
                                            ],
                                        '_identifier' => 'radioboxes',
                                    ],
                                'toggle' =>
                                    [
                                        'identifier' => 'toggle',
                                        'type' => 'Toggle',
                                        'properties' =>
                                            [
                                                'default' => true,
                                            ],
                                        '_path' =>
                                            [
                                                0 => 'toggle',
                                            ],
                                        '_identifier' => 'toggle',
                                    ],
                                'toggleInverted' =>
                                    [
                                        'identifier' => 'toggleInverted',
                                        'type' => 'Toggle',
                                        'properties' =>
                                            [
                                                'invertStateDisplay' => true,
                                            ],
                                        '_path' =>
                                            [
                                                0 => 'toggleInverted',
                                            ],
                                        '_identifier' => 'toggleInverted',
                                    ],
                                'image' =>
                                    [
                                        'identifier' => 'image',
                                        'type' => 'Image',
                                        '_path' =>
                                            [
                                                0 => 'image',
                                            ],
                                        '_identifier' => 'image',
                                    ],
                                'bodytext' =>
                                    [
                                        'identifier' => 'bodytext',
                                        'type' => 'Textarea',
                                        'properties' =>
                                            [
                                                'useExistingField' => true,
                                                'enableRichtext' => true,
                                            ],
                                        '_path' =>
                                            [
                                                0 => 'bodytext',
                                            ],
                                        '_identifier' => 'bodytext',
                                    ],
                                'collection' =>
                                    [
                                        'identifier' => 'collection',
                                        'type' => 'Collection',
                                        'properties' =>
                                            [
                                                'useAsLabel' => 'text',
                                                'maxItems' => 5,
                                                'required' => true,
                                                'fields' =>
                                                    [
                                                        0 =>
                                                            [
                                                                'identifier' => 'text',
                                                                'type' => 'Text',
                                                                '_path' =>
                                                                    [
                                                                        0 => 'collection',
                                                                        1 => 'text',
                                                                    ],
                                                                '_identifier' => 'collection.text',
                                                            ],
                                                        1 =>
                                                            [
                                                                'identifier' => 'collection',
                                                                'type' => 'Collection',
                                                                'properties' =>
                                                                    [
                                                                        'maxItems' => 2,
                                                                        'minItems' => 1,
                                                                        'fields' =>
                                                                            [
                                                                                0 =>
                                                                                    [
                                                                                        'identifier' => 'text',
                                                                                        'type' => 'Text',
                                                                                        '_path' =>
                                                                                            [
                                                                                                0 => 'collection',
                                                                                                1 => 'collection',
                                                                                                2 => 'text',
                                                                                            ],
                                                                                        '_identifier' => 'collection.collection.text',
                                                                                    ],
                                                                                1 =>
                                                                                    [
                                                                                        'identifier' => 'cb_slider_local_slides_text',
                                                                                        'type' => 'Textarea',
                                                                                        'properties' =>
                                                                                            [
                                                                                                'useExistingField' => true,
                                                                                                'enableRichtext' => true,
                                                                                            ],
                                                                                        '_path' =>
                                                                                            [
                                                                                                0 => 'collection',
                                                                                                1 => 'collection',
                                                                                                2 => 'cb_slider_local_slides_text',
                                                                                            ],
                                                                                        '_identifier' => 'collection.collection.cb_slider_local_slides_text',
                                                                                    ],
                                                                            ],
                                                                    ],
                                                                '_path' =>
                                                                    [
                                                                        0 => 'collection',
                                                                        1 => 'collection',
                                                                    ],
                                                                '_identifier' => 'collection.collection',
                                                            ],
                                                    ],
                                            ],
                                        '_path' =>
                                            [
                                                0 => 'collection',
                                            ],
                                        '_identifier' => 'collection',
                                    ],
                                'collection.text' =>
                                    [
                                        'identifier' => 'text',
                                        'type' => 'Text',
                                        '_path' =>
                                            [
                                                0 => 'collection',
                                                1 => 'text',
                                            ],
                                        '_identifier' => 'collection.text',
                                    ],
                                'collection.collection' =>
                                    [
                                        'identifier' => 'collection',
                                        'type' => 'Collection',
                                        'properties' =>
                                            [
                                                'maxItems' => 2,
                                                'minItems' => 1,
                                                'fields' =>
                                                    [
                                                        0 =>
                                                            [
                                                                'identifier' => 'text',
                                                                'type' => 'Text',
                                                                '_path' =>
                                                                    [
                                                                        0 => 'collection',
                                                                        1 => 'collection',
                                                                        2 => 'text',
                                                                    ],
                                                                '_identifier' => 'collection.collection.text',
                                                            ],
                                                        1 =>
                                                            [
                                                                'identifier' => 'cb_slider_local_slides_text',
                                                                'type' => 'Textarea',
                                                                'properties' =>
                                                                    [
                                                                        'useExistingField' => true,
                                                                        'enableRichtext' => true,
                                                                    ],
                                                                '_path' =>
                                                                    [
                                                                        0 => 'collection',
                                                                        1 => 'collection',
                                                                        2 => 'cb_slider_local_slides_text',
                                                                    ],
                                                                '_identifier' => 'collection.collection.cb_slider_local_slides_text',
                                                            ],
                                                    ],
                                            ],
                                        '_path' =>
                                            [
                                                0 => 'collection',
                                                1 => 'collection',
                                            ],
                                        '_identifier' => 'collection.collection',
                                    ],
                                'collection.collection.text' =>
                                    [
                                        'identifier' => 'text',
                                        'type' => 'Text',
                                        '_path' =>
                                            [
                                                0 => 'collection',
                                                1 => 'collection',
                                                2 => 'text',
                                            ],
                                        '_identifier' => 'collection.collection.text',
                                    ],
                                'collection.collection.cb_slider_local_slides_text' =>
                                    [
                                        'identifier' => 'cb_slider_local_slides_text',
                                        'type' => 'Textarea',
                                        'properties' =>
                                            [
                                                'useExistingField' => true,
                                                'enableRichtext' => true,
                                            ],
                                        '_path' =>
                                            [
                                                0 => 'collection',
                                                1 => 'collection',
                                                2 => 'cb_slider_local_slides_text',
                                            ],
                                        '_identifier' => 'collection.collection.cb_slider_local_slides_text',
                                    ],
                            ],
                        'collectionFields' =>
                            [
                                'collection' =>
                                    [
                                        'identifier' => 'collection',
                                        'type' => 'Collection',
                                        'properties' =>
                                            [
                                                'useAsLabel' => 'text',
                                                'maxItems' => 5,
                                                'required' => true,
                                                'fields' =>
                                                    [
                                                        0 =>
                                                            [
                                                                'identifier' => 'text',
                                                                'type' => 'Text',
                                                                '_path' =>
                                                                    [
                                                                        0 => 'collection',
                                                                        1 => 'text',
                                                                    ],
                                                                '_identifier' => 'collection.text',
                                                            ],
                                                        1 =>
                                                            [
                                                                'identifier' => 'collection',
                                                                'type' => 'Collection',
                                                                'properties' =>
                                                                    [
                                                                        'maxItems' => 2,
                                                                        'minItems' => 1,
                                                                        'fields' =>
                                                                            [
                                                                                0 =>
                                                                                    [
                                                                                        'identifier' => 'text',
                                                                                        'type' => 'Text',
                                                                                        '_path' =>
                                                                                            [
                                                                                                0 => 'collection',
                                                                                                1 => 'collection',
                                                                                                2 => 'text',
                                                                                            ],
                                                                                        '_identifier' => 'collection.collection.text',
                                                                                    ],
                                                                                1 =>
                                                                                    [
                                                                                        'identifier' => 'cb_slider_local_slides_text',
                                                                                        'type' => 'Textarea',
                                                                                        'properties' =>
                                                                                            [
                                                                                                'useExistingField' => true,
                                                                                                'enableRichtext' => true,
                                                                                            ],
                                                                                        '_path' =>
                                                                                            [
                                                                                                0 => 'collection',
                                                                                                1 => 'collection',
                                                                                                2 => 'cb_slider_local_slides_text',
                                                                                            ],
                                                                                        '_identifier' => 'collection.collection.cb_slider_local_slides_text',
                                                                                    ],
                                                                            ],
                                                                    ],
                                                                '_path' =>
                                                                    [
                                                                        0 => 'collection',
                                                                        1 => 'collection',
                                                                    ],
                                                                '_identifier' => 'collection.collection',
                                                            ],
                                                    ],
                                            ],
                                        '_path' =>
                                            [
                                                0 => 'collection',
                                            ],
                                        '_identifier' => 'collection',
                                    ],
                                'collection.collection' =>
                                    [
                                        'identifier' => 'collection',
                                        'type' => 'Collection',
                                        'properties' =>
                                            [
                                                'maxItems' => 2,
                                                'minItems' => 1,
                                                'fields' =>
                                                    [
                                                        0 =>
                                                            [
                                                                'identifier' => 'text',
                                                                'type' => 'Text',
                                                                '_path' =>
                                                                    [
                                                                        0 => 'collection',
                                                                        1 => 'collection',
                                                                        2 => 'text',
                                                                    ],
                                                                '_identifier' => 'collection.collection.text',
                                                            ],
                                                        1 =>
                                                            [
                                                                'identifier' => 'cb_slider_local_slides_text',
                                                                'type' => 'Textarea',
                                                                'properties' =>
                                                                    [
                                                                        'useExistingField' => true,
                                                                        'enableRichtext' => true,
                                                                    ],
                                                                '_path' =>
                                                                    [
                                                                        0 => 'collection',
                                                                        1 => 'collection',
                                                                        2 => 'cb_slider_local_slides_text',
                                                                    ],
                                                                '_identifier' => 'collection.collection.cb_slider_local_slides_text',
                                                            ],
                                                    ],
                                            ],
                                        '_path' =>
                                            [
                                                0 => 'collection',
                                                1 => 'collection',
                                            ],
                                        '_identifier' => 'collection.collection',
                                    ],
                            ],
                        'fileFields' =>
                            [
                                'image' =>
                                    [
                                        'identifier' => 'image',
                                        'type' => 'Image',
                                        '_path' =>
                                            [
                                                0 => 'image',
                                            ],
                                        '_identifier' => 'image',
                                    ],
                            ],
                        'frontendTemplatesPath' => 'typo3conf/contentBlocks/example-local/Resources/Private',
                        'frontendPartialsPath' => 'typo3conf/contentBlocks/example-local/Resources/Private/Partials',
                        'frontendLayoutsPath' => 'typo3conf/contentBlocks/example-local/Resources/Private/Layouts',
                        'EditorPreview.html' => '/var/www/html/.typo3/public/typo3conf/contentBlocks/example-local/Resources/Private/EditorPreview.html',
                        'EditorInterfaceXlf' => 'typo3conf/contentBlocks/example-local/Resources/Private/Language/EditorInterface.xlf',
                        'EditorLLL' => 'LLL:typo3conf/contentBlocks/example-local/Resources/Private/Language/EditorInterface.xlf:typo3-contentblocks.example-local',
                        'FrontendXlf' => 'typo3conf/contentBlocks/example-local/Resources/Private/Language/Frontend.xlf',
                        'FrontendLLL' => 'LLL:typo3conf/contentBlocks/example-local/Resources/Private/Language/Frontend.xlf:typo3-contentblocks.example-local',
                        'yaml' =>
                            [
                                'group' => 'common',
                                'fields' =>
                                    [
                                        0 =>
                                            [
                                                'identifier' => 'text',
                                                'type' => 'Text',
                                                'properties' =>
                                                    [
                                                        'autocomplete' => true,
                                                        'default' => 'Default value',
                                                        'max' => 15,
                                                        'placeholder' => 'Placeholder text',
                                                        'size' => 20,
                                                        'required' => false,
                                                        'trim' => true,
                                                    ],
                                                '_path' =>
                                                    [
                                                        0 => 'text',
                                                    ],
                                                '_identifier' => 'text',
                                            ],
                                        1 =>
                                            [
                                                'identifier' => 'textarea',
                                                'type' => 'Textarea',
                                                'properties' =>
                                                    [
                                                        'cols' => 40,
                                                        'default' => 'Default value',
                                                        'enableRichtext' => true,
                                                        'max' => 150,
                                                        'placeholder' => 'Placeholder text',
                                                        'richtextConfiguration' => 'default',
                                                        'rows' => 15,
                                                        'required' => false,
                                                        'trim' => true,
                                                    ],
                                                '_path' =>
                                                    [
                                                        0 => 'textarea',
                                                    ],
                                                '_identifier' => 'textarea',
                                            ],
                                        2 =>
                                            [
                                                'identifier' => 'email',
                                                'type' => 'Email',
                                                'properties' =>
                                                    [
                                                        'autocomplete' => true,
                                                        'default' => 'developer@localhost',
                                                        'placeholder' => 'Placeholder text',
                                                        'size' => 20,
                                                        'required' => true,
                                                        'trim' => true,
                                                    ],
                                                '_path' =>
                                                    [
                                                        0 => 'email',
                                                    ],
                                                '_identifier' => 'email',
                                            ],
                                        3 =>
                                            [
                                                'identifier' => 'integer',
                                                'type' => 'Integer',
                                                'properties' =>
                                                    [
                                                        'default' => 0,
                                                        'size' => 20,
                                                        'required' => true,
                                                        'trim' => true,
                                                    ],
                                                '_path' =>
                                                    [
                                                        0 => 'integer',
                                                    ],
                                                '_identifier' => 'integer',
                                            ],
                                        4 =>
                                            [
                                                'identifier' => 'money',
                                                'type' => 'Money',
                                                'properties' =>
                                                    [
                                                        'size' => 20,
                                                        'required' => true,
                                                        'trim' => true,
                                                    ],
                                                '_path' =>
                                                    [
                                                        0 => 'money',
                                                    ],
                                                '_identifier' => 'money',
                                            ],
                                        5 =>
                                            [
                                                'identifier' => 'number',
                                                'type' => 'Number',
                                                'properties' =>
                                                    [
                                                        'default' => 0,
                                                        'size' => 20,
                                                        'required' => true,
                                                        'trim' => true,
                                                    ],
                                                '_path' =>
                                                    [
                                                        0 => 'number',
                                                    ],
                                                '_identifier' => 'number',
                                            ],
                                        6 =>
                                            [
                                                'identifier' => 'percent',
                                                'type' => 'Percent',
                                                'properties' =>
                                                    [
                                                        'default' => 0,
                                                        'range' =>
                                                            [
                                                                'lower' => 0,
                                                                'upper' => 100,
                                                            ],
                                                        'required' => true,
                                                        'size' => 20,
                                                        'slider' =>
                                                            [
                                                                'step' => 1,
                                                                'width' => 100,
                                                            ],
                                                        'trim' => true,
                                                    ],
                                                '_path' =>
                                                    [
                                                        0 => 'percent',
                                                    ],
                                                '_identifier' => 'percent',
                                            ],
                                        7 =>
                                            [
                                                'identifier' => 'url',
                                                'type' => 'Url',
                                                'properties' =>
                                                    [
                                                        'autocomplete' => true,
                                                        'default' => 'https://typo3.org',
                                                        'linkPopup' =>
                                                            [
                                                                'allowedExtensions' => 'pdf',
                                                                'blindLinkFields' => 'target,title',
                                                                'blindLinkOptions' => 'folder,spec,telefone,mail',
                                                            ],
                                                        'max' => 150,
                                                        'placeholder' => 'Placeholder text',
                                                        'size' => 20,
                                                        'required' => false,
                                                    ],
                                                '_path' =>
                                                    [
                                                        0 => 'url',
                                                    ],
                                                '_identifier' => 'url',
                                            ],
                                        8 =>
                                            [
                                                'identifier' => 'tel',
                                                'type' => 'Tel',
                                                'properties' =>
                                                    [
                                                        'autocomplete' => true,
                                                        'default' => 0,
                                                        'size' => 20,
                                                        'required' => false,
                                                    ],
                                                '_path' =>
                                                    [
                                                        0 => 'tel',
                                                    ],
                                                '_identifier' => 'tel',
                                            ],
                                        9 =>
                                            [
                                                'identifier' => 'color',
                                                'type' => 'Color',
                                                'properties' =>
                                                    [
                                                        'autocomplete' => true,
                                                        'default' => '#0000aa',
                                                        'size' => 5,
                                                        'required' => false,
                                                        'valuePicker' =>
                                                            [
                                                                'items' =>
                                                                    [
                                                                        '#FF0000' => 'Red',
                                                                        '#008000' => 'Green',
                                                                        '#0000FF' => 'Blue',
                                                                    ],
                                                            ],
                                                    ],
                                                '_path' =>
                                                    [
                                                        0 => 'color',
                                                    ],
                                                '_identifier' => 'color',
                                            ],
                                        10 =>
                                            [
                                                'identifier' => 'date',
                                                'type' => 'Date',
                                                'properties' =>
                                                    [
                                                        'default' => '2020-12-12',
                                                        'displayAge' => true,
                                                        'size' => 20,
                                                        'range' =>
                                                            [
                                                                'lower' => '2019-12-12',
                                                                'upper' => '2035-12-12',
                                                            ],
                                                        'required' => true,
                                                    ],
                                                '_path' =>
                                                    [
                                                        0 => 'date',
                                                    ],
                                                '_identifier' => 'date',
                                            ],
                                        11 =>
                                            [
                                                'identifier' => 'datetime',
                                                'type' => 'DateTime',
                                                'properties' =>
                                                    [
                                                        'default' => '2020-01-31 12:00:00',
                                                        'displayAge' => true,
                                                        'size' => 20,
                                                        'range' =>
                                                            [
                                                                'lower' => '2019-01-31 12:00:00',
                                                                'upper' => '2040-01-31 12:00:00',
                                                            ],
                                                        'required' => true,
                                                    ],
                                                '_path' =>
                                                    [
                                                        0 => 'datetime',
                                                    ],
                                                '_identifier' => 'datetime',
                                            ],
                                        12 =>
                                            [
                                                'identifier' => 'time',
                                                'type' => 'Time',
                                                'properties' =>
                                                    [
                                                        'default' => '15:30',
                                                        'displayAge' => true,
                                                        'size' => 20,
                                                        'range' =>
                                                            [
                                                                'lower' => '06:01',
                                                                'upper' => '17:59',
                                                            ],
                                                        'required' => true,
                                                    ],
                                                '_path' =>
                                                    [
                                                        0 => 'time',
                                                    ],
                                                '_identifier' => 'time',
                                            ],
                                        13 =>
                                            [
                                                'identifier' => 'select',
                                                'type' => 'Select',
                                                'properties' =>
                                                    [
                                                        'items' =>
                                                            [
                                                                'one' => 'The first',
                                                                'two' => 'The second',
                                                                'three' => 'The third',
                                                            ],
                                                        'prependLabel' => 'Please choose',
                                                    ],
                                                '_path' =>
                                                    [
                                                        0 => 'select',
                                                    ],
                                                '_identifier' => 'select',
                                            ],
                                        14 =>
                                            [
                                                'identifier' => 'selectSideBySide',
                                                'type' => 'MultiSelect',
                                                'properties' =>
                                                    [
                                                        'maxItems' => 2,
                                                        'size' => 5,
                                                        'items' =>
                                                            [
                                                                'one' => 'The first',
                                                                'two' => 'The second',
                                                                'three' => 'The third',
                                                            ],
                                                    ],
                                                '_path' =>
                                                    [
                                                        0 => 'selectSideBySide',
                                                    ],
                                                '_identifier' => 'selectSideBySide',
                                            ],
                                        15 =>
                                            [
                                                'identifier' => 'checkboxes',
                                                'type' => 'Checkbox',
                                                'properties' =>
                                                    [
                                                        'items' =>
                                                            [
                                                                'one' => 'The first',
                                                                'two' => 'The second',
                                                                'three' => 'The third',
                                                            ],
                                                        'default' => 2,
                                                    ],
                                                '_path' =>
                                                    [
                                                        0 => 'checkboxes',
                                                    ],
                                                '_identifier' => 'checkboxes',
                                            ],
                                        16 =>
                                            [
                                                'identifier' => 'radioboxes',
                                                'type' => 'Radiobox',
                                                'properties' =>
                                                    [
                                                        'default' => 'two',
                                                        'items' =>
                                                            [
                                                                'one' => 'The first',
                                                                'two' => 'The second',
                                                                'three' => 'The third',
                                                            ],
                                                    ],
                                                '_path' =>
                                                    [
                                                        0 => 'radioboxes',
                                                    ],
                                                '_identifier' => 'radioboxes',
                                            ],
                                        17 =>
                                            [
                                                'identifier' => 'toggle',
                                                'type' => 'Toggle',
                                                'properties' =>
                                                    [
                                                        'default' => true,
                                                    ],
                                                '_path' =>
                                                    [
                                                        0 => 'toggle',
                                                    ],
                                                '_identifier' => 'toggle',
                                            ],
                                        18 =>
                                            [
                                                'identifier' => 'toggleInverted',
                                                'type' => 'Toggle',
                                                'properties' =>
                                                    [
                                                        'invertStateDisplay' => true,
                                                    ],
                                                '_path' =>
                                                    [
                                                        0 => 'toggleInverted',
                                                    ],
                                                '_identifier' => 'toggleInverted',
                                            ],
                                        19 =>
                                            [
                                                'identifier' => 'image',
                                                'type' => 'Image',
                                                '_path' =>
                                                    [
                                                        0 => 'image',
                                                    ],
                                                '_identifier' => 'image',
                                            ],
                                        20 =>
                                            [
                                                'identifier' => 'bodytext',
                                                'type' => 'Textarea',
                                                'properties' =>
                                                    [
                                                        'useExistingField' => true,
                                                        'enableRichtext' => true,
                                                    ],
                                                '_path' =>
                                                    [
                                                        0 => 'bodytext',
                                                    ],
                                                '_identifier' => 'bodytext',
                                            ],
                                        21 =>
                                            [
                                                'identifier' => 'collection',
                                                'type' => 'Collection',
                                                'properties' =>
                                                    [
                                                        'useAsLabel' => 'text',
                                                        'maxItems' => 5,
                                                        'required' => true,
                                                        'fields' =>
                                                            [
                                                                0 =>
                                                                    [
                                                                        'identifier' => 'text',
                                                                        'type' => 'Text',
                                                                        '_path' =>
                                                                            [
                                                                                0 => 'collection',
                                                                                1 => 'text',
                                                                            ],
                                                                        '_identifier' => 'collection.text',
                                                                    ],
                                                                1 =>
                                                                    [
                                                                        'identifier' => 'collection',
                                                                        'type' => 'Collection',
                                                                        'properties' =>
                                                                            [
                                                                                'maxItems' => 2,
                                                                                'minItems' => 1,
                                                                                'fields' =>
                                                                                    [
                                                                                        0 =>
                                                                                            [
                                                                                                'identifier' => 'text',
                                                                                                'type' => 'Text',
                                                                                                '_path' =>
                                                                                                    [
                                                                                                        0 => 'collection',
                                                                                                        1 => 'collection',
                                                                                                        2 => 'text',
                                                                                                    ],
                                                                                                '_identifier' => 'collection.collection.text',
                                                                                            ],
                                                                                        1 =>
                                                                                            [
                                                                                                'identifier' => 'cb_slider_local_slides_text',
                                                                                                'type' => 'Textarea',
                                                                                                'properties' =>
                                                                                                    [
                                                                                                        'useExistingField' => true,
                                                                                                        'enableRichtext' => true,
                                                                                                    ],
                                                                                                '_path' =>
                                                                                                    [
                                                                                                        0 => 'collection',
                                                                                                        1 => 'collection',
                                                                                                        2 => 'cb_slider_local_slides_text',
                                                                                                    ],
                                                                                                '_identifier' => 'collection.collection.cb_slider_local_slides_text',
                                                                                            ],
                                                                                    ],
                                                                            ],
                                                                        '_path' =>
                                                                            [
                                                                                0 => 'collection',
                                                                                1 => 'collection',
                                                                            ],
                                                                        '_identifier' => 'collection.collection',
                                                                    ],
                                                            ],
                                                    ],
                                                '_path' =>
                                                    [
                                                        0 => 'collection',
                                                    ],
                                                '_identifier' => 'collection',
                                            ],
                                    ],
                            ],
                    ],
     */
}
