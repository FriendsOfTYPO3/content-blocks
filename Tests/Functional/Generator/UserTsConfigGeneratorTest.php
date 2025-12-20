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
use TYPO3\CMS\ContentBlocks\Generator\UserTsConfigGenerator;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class UserTsConfigGeneratorTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'typo3conf/ext/content_blocks/Tests/Fixtures/Extensions/test_content_blocks_b',
        'typo3conf/ext/content_blocks',
    ];

    #[Test]
    public function userTsConfigIsGenerated(): void
    {
        /** @var UserTsConfigGenerator $userTsConfigGenerator */
        $userTsConfigGenerator = $this->get(UserTsConfigGenerator::class);
        $result = $userTsConfigGenerator->generate();
        $expected = 'options.pageTree.doktypesToShowInNewPageDragArea := addToList(942)';
        self::assertSame($expected, $result);
    }
}
