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
use TYPO3\CMS\ContentBlocks\Generator\TypoScriptGenerator;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class TypoScriptGeneratorTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'typo3conf/ext/content_blocks/Tests/Fixtures/Extensions/test_content_blocks_b',
        'typo3conf/ext/content_blocks',
    ];

    #[Test]
    public function typoScriptIsGenerated(): void
    {
        /** @var TypoScriptGenerator $typoScriptGenerator */
        $typoScriptGenerator = $this->get(TypoScriptGenerator::class);
        $typoScript = $typoScriptGenerator->generate();

        $expected = <<<HEREDOC
tt_content.typo3tests_contentelementb =< lib.contentBlock
tt_content.typo3tests_contentelementb {
    file = EXT:test_content_blocks_b/ContentBlocks/ContentElements/content-element-b/templates/frontend.html
    partialRootPaths {
        20 = EXT:test_content_blocks_b/ContentBlocks/ContentElements/content-element-b/templates/partials/
    }
    layoutRootPaths {
        20 = EXT:test_content_blocks_b/ContentBlocks/ContentElements/content-element-b/templates/layouts/
    }
    settings._content_block_name = typo3tests/content-element-b
}
HEREDOC;

        self::assertSame($expected, $typoScript);
    }
}
