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

namespace TYPO3\CMS\ContentBlocks\Tests\Functional\Generator;

use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class TcaGeneratorTest extends FunctionalTestCase
{
    protected array $coreExtensionsToLoad = [
        'content_blocks',
    ];

    protected array $testExtensionsToLoad = [
       'typo3/sysext/content_blocks/Tests/Fixtures/Extensions/simple',
    ];

    /**
     * @test
     */
    public function coreLabelsAreNotOverriddenIfMissingInLanguageFile(): void
    {
        self::assertTrue(!isset($GLOBALS['TCA']['tt_content']['types']['simple_simple']['columnsOverrides']['header']['label']));
    }

    /**
     * @test
     */
    public function coreLabelsAreOverriddenIfTranslationExistsInLanguageFile(): void
    {
        self::assertSame(
            'LLL:EXT:simple/ContentBlocks/ContentElements/simple2/Source/Language/Labels.xlf:header.label',
            $GLOBALS['TCA']['tt_content']['types']['simple_simple2']['columnsOverrides']['header']['label']
        );
    }

    /**
     * @test
     */
    public function labelCanBeSetStaticallyInYaml(): void
    {
        self::assertSame(
            'My static label',
            $GLOBALS['TCA']['tt_content']['types']['simple_simple2']['columnsOverrides']['simple_simple2_aField']['label']
        );
    }

    /**
     * @test
     */
    public function descriptionCanBeSetStaticallyInYaml(): void
    {
        self::assertSame(
            'My static description',
            $GLOBALS['TCA']['tt_content']['types']['simple_simple2']['columnsOverrides']['simple_simple2_aField']['description']
        );
    }

    /**
     * @test
     */
    public function labelCanBeSetStaticallyInYamlInsideCollection(): void
    {
        self::assertSame(
            'Label in Inline',
            $GLOBALS['TCA']['simple_simple2_collection']['columns']['header']['label']
        );
    }

    /**
     * @test
     */
    public function descriptionCanBeSetStaticallyInYamlInsideCollection(): void
    {
        self::assertSame(
            'Label in Inline',
            $GLOBALS['TCA']['simple_simple2_collection']['columns']['header']['label']
        );
    }

    /**
     * @test
     */
    public function labelOfCollectionFieldFallsBackToIdentifierIfNotDefinedInLanguageFile(): void
    {
        self::assertSame(
            'header2',
            $GLOBALS['TCA']['simple_simple2_collection']['columns']['header2']['label']
        );
    }

    /**
     * @test
     */
    public function paletteLabelCanBeSetStaticallyInYaml(): void
    {
        self::assertSame(
            'My Palette label',
            $GLOBALS['TCA']['tt_content']['palettes']['simple_simple2_palette_1']['label']
        );
    }

    /**
     * @test
     */
    public function paletteDescriptionCanBeSetStaticallyInYaml(): void
    {
        self::assertSame(
            'My Palette description',
            $GLOBALS['TCA']['tt_content']['palettes']['simple_simple2_palette_1']['description']
        );
    }

    /**
     * @test
     */
    public function tabLabelCanBeSetStaticallyInYaml(): void
    {
        self::assertStringContainsString(
            '--div--;My Tab label',
            $GLOBALS['TCA']['tt_content']['types']['simple_simple2']['showitem']
        );
    }

    /**
     * @test
     */
    public function basicsAreAppendedToTheEndFromTopLevelBasicsArray(): void
    {
        self::assertStringContainsString(
            '--palette--;;simple_basics_palette,--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,--palette--;;simple_basics_frames_palette,--palette--;;simple_basics_links_palette,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,categories',
            $GLOBALS['TCA']['tt_content']['types']['simple_basics']['showitem']
        );
    }

    /**
     * @test
     */
    public function basicIncludedAsTypeAddedToPalette(): void
    {
        self::assertStringContainsString(
            'simple_basics_basic_field',
            $GLOBALS['TCA']['tt_content']['palettes']['simple_basics_palette']['showitem']
        );
    }

    /**
     * @test
     */
    public function typeFieldSelectAddedForRecordType(): void
    {
        self::assertSame(
            [
                [
                    'label' => 'LLL:EXT:simple/ContentBlocks/RecordTypes/record1/Source/Language/Labels.xlf:content-blocks.record1.title',
                    'value' => 'record1',
                    'icon' => 'custom_record-record1-icon',
                    'group' => '',
                ],
                [
                    'label' => 'LLL:EXT:simple/ContentBlocks/RecordTypes/record2/Source/Language/Labels.xlf:content-blocks.record2.title',
                    'value' => 'record2',
                    'icon' => 'custom_record-record2-icon',
                    'group' => '',
                ],
            ],
            $GLOBALS['TCA']['custom_record']['columns']['type']['config']['items'],
        );
    }

    /**
     * @test
     */
    public function typeFieldDefaultValueAddedForTheFirstRecord(): void
    {
        self::assertSame(
            'record1',
            $GLOBALS['TCA']['custom_record']['columns']['type']['config']['default'],
        );
    }
}
