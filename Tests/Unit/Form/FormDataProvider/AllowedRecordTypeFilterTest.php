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

namespace TYPO3\CMS\ContentBlocks\Tests\Unit\Form\FormDataProvider;

use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\ContentBlocks\Form\FormDataProvider\AllowedRecordTypeFilter;
use TYPO3\CMS\Core\Schema\Struct\SelectItem;
use TYPO3\TestingFramework\Core\BaseTestCase;

final class AllowedRecordTypeFilterTest extends BaseTestCase
{
    #[Test]
    public function itemsAreFilteredAndSortedCorrectly(): void
    {
        $allowedRecordTypes = ['a', 'c'];
        $items = [
            [
                'label' => 'C',
                'value' => 'c',
            ],
            new SelectItem('select', 'B', 'b'),
            [
                'label' => 'A',
                'value' => 'a',
            ],
        ];
        $expected = [
            new SelectItem('select', 'A', 'a'),
            new SelectItem('select', 'C', 'c'),
        ];
        $allowedRecordTypeFilter = new AllowedRecordTypeFilter();
        $result = $allowedRecordTypeFilter->filterAndSortItems($items, $allowedRecordTypes);
        self::assertEquals($expected, $result);
    }
}
