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
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentTypeIcon;
use TYPO3\CMS\ContentBlocks\Form\FormDataProvider\AllowedRecordTypeFilter;
use TYPO3\CMS\ContentBlocks\Loader\LoadedContentBlock;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
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
        $contentBlockRegistry = new ContentBlockRegistry();
        $allowedRecordTypeFilter = new AllowedRecordTypeFilter($contentBlockRegistry);
        $result = $allowedRecordTypeFilter->filterAndSortItems($items, $allowedRecordTypes);
        self::assertEquals($expected, $result);
    }

    #[Test]
    public function itemsAreFilteredByAllowedContentBlocks(): void
    {
        $items = [
            [
                'label' => 'A',
                'value' => 'a',
            ],
            new SelectItem('select', 'B', 'b'),
            [
                'label' => 'C',
                'value' => 'c',
            ],
            new SelectItem('select', '', '--div--'),
        ];
        $loadedContentBlockA = new LoadedContentBlock(
            name: 'content-blocks/a',
            yaml: ['typeField' => 'CType', 'table' => 'tt_content', 'typeName' => 'a'],
            icon: ContentTypeIcon::fromArray([]),
            hostExtension: '',
            extPath: '',
            contentType: ContentType::CONTENT_ELEMENT
        );
        $loadedContentBlockB = new LoadedContentBlock(
            name: 'content-blocks/b',
            yaml: ['typeField' => 'CType', 'table' => 'tt_content', 'typeName' => 'b'],
            icon: ContentTypeIcon::fromArray([]),
            hostExtension: '',
            extPath: '',
            contentType: ContentType::CONTENT_ELEMENT
        );
        $loadedContentBlockC = new LoadedContentBlock(
            name: 'content-blocks/c',
            yaml: ['typeField' => 'CType', 'table' => 'tt_content', 'typeName' => 'c'],
            icon: ContentTypeIcon::fromArray([]),
            hostExtension: '',
            extPath: '',
            contentType: ContentType::CONTENT_ELEMENT
        );
        $contentBlockRegistry = new ContentBlockRegistry();
        $contentBlockRegistry->register($loadedContentBlockA);
        $contentBlockRegistry->register($loadedContentBlockB);
        $contentBlockRegistry->register($loadedContentBlockC);
        $allowedRecordTypeFilter = new AllowedRecordTypeFilter($contentBlockRegistry);

        $result = $allowedRecordTypeFilter->filterByAllowedContentBlocks(
            $items,
            [$loadedContentBlockA, $loadedContentBlockC],
            'tt_content'
        );

        $expected = [
            new SelectItem('select', 'A', 'a'),
            new SelectItem('select', 'C', 'c'),
            new SelectItem('select', '', '--div--'),
        ];

        self::assertEquals($expected, $result);
    }
}
