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
            'LLL:EXT:simple/ContentBlocks/ContentTypes/simple2/Source/Language/Labels.xlf:header.label',
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
}
