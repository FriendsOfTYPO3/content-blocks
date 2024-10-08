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

use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class TcaGeneratorTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'typo3conf/ext/content_blocks/Tests/Fixtures/Extensions/test_content_blocks_c',
        'typo3conf/ext/content_blocks',
    ];

    #[Test]
    public function coreLabelsAreNotOverriddenIfMissingInLanguageFile(): void
    {
        self::assertTrue(!isset($GLOBALS['TCA']['tt_content']['types']['simple_simple']['columnsOverrides']['header']['label']));
    }

    #[Test]
    public function coreLabelsAreOverriddenIfTranslationExistsInLanguageFile(): void
    {
        self::assertSame(
            'LLL:EXT:test_content_blocks_c/ContentBlocks/ContentElements/simple2/language/labels.xlf:header.label',
            $GLOBALS['TCA']['tt_content']['types']['simple_simple2']['columnsOverrides']['header']['label']
        );
    }

    #[Test]
    public function labelCanBeSetStaticallyInYaml(): void
    {
        self::assertSame(
            'My static label',
            $GLOBALS['TCA']['tt_content']['types']['simple_simple2']['columnsOverrides']['simple_simple2_aField']['label']
        );
    }

    #[Test]
    public function descriptionCanBeSetStaticallyInYaml(): void
    {
        self::assertSame(
            'My static description',
            $GLOBALS['TCA']['tt_content']['types']['simple_simple2']['columnsOverrides']['simple_simple2_aField']['description']
        );
    }

    #[Test]
    public function labelCanBeSetStaticallyInYamlInsideCollection(): void
    {
        self::assertSame(
            'Label in Inline',
            $GLOBALS['TCA']['simple_simple2_collection']['columns']['header']['label']
        );
    }

    #[Test]
    public function descriptionCanBeSetStaticallyInYamlInsideCollection(): void
    {
        self::assertSame(
            'Label in Inline',
            $GLOBALS['TCA']['simple_simple2_collection']['columns']['header']['label']
        );
    }

    #[Test]
    public function labelOfCollectionFieldFallsBackToIdentifierIfNotDefinedInLanguageFile(): void
    {
        self::assertSame(
            'header2',
            $GLOBALS['TCA']['simple_simple2_collection']['columns']['header2']['label']
        );
    }

    #[Test]
    public function paletteLabelCanBeSetStaticallyInYaml(): void
    {
        self::assertSame(
            'My Palette label',
            $GLOBALS['TCA']['tt_content']['palettes']['simple_simple2_palette_1']['label']
        );
    }

    #[Test]
    public function paletteDescriptionCanBeSetStaticallyInYaml(): void
    {
        self::assertSame(
            'My Palette description',
            $GLOBALS['TCA']['tt_content']['palettes']['simple_simple2_palette_1']['description']
        );
    }

    #[Test]
    public function tabLabelCanBeSetStaticallyInYaml(): void
    {
        self::assertStringContainsString(
            '--div--;My Tab label',
            $GLOBALS['TCA']['tt_content']['types']['simple_simple2']['showitem']
        );
    }

    #[Test]
    public function basicsAreAppendedToTheEndFromTopLevelBasicsArray(): void
    {
        self::assertStringContainsString(
            '--palette--;;simple_basics_palette,--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,--palette--;;simple_basics_frames_palette,--palette--;;simple_basics_links_palette,--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,categories',
            $GLOBALS['TCA']['tt_content']['types']['simple_basics']['showitem']
        );
    }

    #[Test]
    public function basicIncludedAsTypeAddedToPalette(): void
    {
        self::assertStringContainsString(
            'simple_basics_basic_field',
            $GLOBALS['TCA']['tt_content']['palettes']['simple_basics_palette']['showitem']
        );
    }

    #[Test]
    public function typeFieldSelectAddedForRecordType(): void
    {
        self::assertSame(
            [
                [
                    'label' => 'LLL:EXT:test_content_blocks_c/ContentBlocks/RecordTypes/record1/language/labels.xlf:title',
                    'value' => 'record1',
                    'icon' => 'custom_record-record1-cc2849f',
                    'group' => null,
                    'description' => 'LLL:EXT:test_content_blocks_c/ContentBlocks/RecordTypes/record1/language/labels.xlf:description',
                ],
                [
                    'label' => 'content-blocks/record2',
                    'value' => 'record2',
                    'icon' => 'custom_record-record2-cc2849f',
                    'group' => null,
                    'description' => '',
                ],
            ],
            $GLOBALS['TCA']['custom_record']['columns']['type']['config']['items'],
        );
    }

    #[Test]
    public function typeFieldDefaultValueAddedForTheFirstRecord(): void
    {
        self::assertSame(
            'record1',
            $GLOBALS['TCA']['custom_record']['columns']['type']['config']['default'],
        );
    }
}
