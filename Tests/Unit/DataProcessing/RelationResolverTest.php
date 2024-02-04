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

namespace TYPO3\CMS\ContentBlocks\Tests\Unit\DataProcessing;

use TYPO3\CMS\ContentBlocks\DataProcessing\RelationResolver;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class RelationResolverTest extends UnitTestCase
{
    /**
     * @test
     */
    public function tableListUidPairsConvertedToTableListCorrectly(): void
    {
        $relationResolverMock = $this->createMock(RelationResolver::class);
        $relationResolverReflection = new \ReflectionClass(RelationResolver::class);
        $getTableListFromTableUidPairs = $relationResolverReflection->getMethod('getTableListFromTableUidPairs');

        $input = 'tt_content_1,pages_3,tt_content_42';
        $expected = [
            'tt_content',
            'pages',
            'tt_content',
        ];

        $result = $getTableListFromTableUidPairs->invokeArgs($relationResolverMock, [$input]);

        self::assertSame($expected, $result);
    }
}
