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
            'LLL:EXT:simple/ContentBlocks/simple2/Source/Language/Labels.xlf:header.label',
            $GLOBALS['TCA']['tt_content']['types']['simple_simple2']['columnsOverrides']['header']['label']
        );
    }
}
