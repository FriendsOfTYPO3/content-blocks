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

namespace TYPO3\CMS\ContentBlocks\Tests\Functional;

use TYPO3\CMS\ContentBlocks\Loader\LoaderFactory;
use TYPO3\CMS\ContentBlocks\RelationResolver;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class RelationResolverTest extends FunctionalTestCase
{
    protected array $coreExtensionsToLoad = [
        'content_blocks',
    ];

    protected array $pathsToProvideInTestInstance = [
        'typo3/sysext/content_blocks/Tests/Fixtures/ContentBlocks/' => 'typo3conf/content-blocks/',
    ];

    /**
     * @test
     */
    public function canResolveFileReferences(): void
    {
        $this->importCSVDataSet('typo3/sysext/content_blocks/Tests/Fixtures/DataSet/file_reference.csv');

        $tableDefinitionCollection = $this->get(LoaderFactory::class)->load();
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $fieldDefinition = $tableDefinition->getTcaColumnsDefinition()->getField('image');
        $dummyRecord = [
            'uid' => 1,
            'image' => 1,
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection);
        $result = $relationResolver->processField($fieldDefinition, $dummyRecord, 'tt_content');

        self::assertCount(1, $result);
        self::assertInstanceOf(FileReference::class, $result[0]);
    }

    /**
     * @test
     */
    public function canResolveCollections(): void
    {
        $this->importCSVDataSet('typo3/sysext/content_blocks/Tests/Fixtures/DataSet/collections.csv');

        $tableDefinitionCollection = $this->get(LoaderFactory::class)->load();
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $fieldDefinition = $tableDefinition->getTcaColumnsDefinition()->getField('bar_foo_collection');
        $dummyRecord = [
            'uid' => 1,
            'bar_foo_collection' => 2,
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection);
        $result = $relationResolver->processField($fieldDefinition, $dummyRecord, 'tt_content');

        self::assertCount(2, $result);
        self::assertSame('lorem foo bar', $result[0]['fieldA']);
        self::assertSame('lorem foo bar 2', $result[1]['fieldA']);
    }

    /**
     * @test
     */
    public function canResolveCollectionsRecursively(): void
    {
        $this->importCSVDataSet('typo3/sysext/content_blocks/Tests/Fixtures/DataSet/collections_recursive.csv');

        $tableDefinitionCollection = $this->get(LoaderFactory::class)->load();
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $fieldDefinition = $tableDefinition->getTcaColumnsDefinition()->getField('bar_foo_collection_recursive');
        $dummyRecord = [
            'uid' => 1,
            'bar_foo_collection_recursive' => 2,
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection);
        $result = $relationResolver->processField($fieldDefinition, $dummyRecord, 'tt_content');

        self::assertCount(2, $result);
        self::assertSame('lorem foo bar A', $result[0]['fieldA']);
        self::assertSame('lorem foo bar A2', $result[1]['fieldA']);
        self::assertCount(2, $result[0]['collection_inner']);
        self::assertSame('lorem foo bar B', $result[0]['collection_inner'][0]['fieldB']);
        self::assertSame('lorem foo bar B2', $result[0]['collection_inner'][1]['fieldB']);
    }

    /**
     * @test
     */
    public function canResolveCategoriesManyToMany(): void
    {
        $this->importCSVDataSet('typo3/sysext/content_blocks/Tests/Fixtures/DataSet/category_many_to_many.csv');

        $tableDefinitionCollection = $this->get(LoaderFactory::class)->load();
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $fieldDefinition = $tableDefinition->getTcaColumnsDefinition()->getField('bar_foo_categories_mm');
        $dummyRecord = [
            'uid' => 1,
            'bar_foo_categories_mm' => 2,
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection);
        $result = $relationResolver->processField($fieldDefinition, $dummyRecord, 'tt_content');

        self::assertCount(2, $result);
        self::assertSame('Category 1', $result[0]['title']);
        self::assertSame('Category 2', $result[1]['title']);
    }

    /**
     * @test
     */
    public function canResolveCategoriesOneToOne(): void
    {
        $this->importCSVDataSet('typo3/sysext/content_blocks/Tests/Fixtures/DataSet/category_one_to_one.csv');

        $tableDefinitionCollection = $this->get(LoaderFactory::class)->load();
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $fieldDefinition = $tableDefinition->getTcaColumnsDefinition()->getField('bar_foo_categories_11');
        $dummyRecord = [
            'uid' => 1,
            'bar_foo_categories_11' => 7,
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection);
        $result = $relationResolver->processField($fieldDefinition, $dummyRecord, 'tt_content');

        self::assertCount(1, $result);
        self::assertSame('Category 1', $result[0]['title']);
    }

    /**
     * @test
     */
    public function canResolveCategoriesOneToMany(): void
    {
        $this->importCSVDataSet('typo3/sysext/content_blocks/Tests/Fixtures/DataSet/category_one_to_many.csv');

        $tableDefinitionCollection = $this->get(LoaderFactory::class)->load();
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $fieldDefinition = $tableDefinition->getTcaColumnsDefinition()->getField('bar_foo_categories_1m');
        $dummyRecord = [
            'uid' => 1,
            'bar_foo_categories_1m' => '7,8',
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection);
        $result = $relationResolver->processField($fieldDefinition, $dummyRecord, 'tt_content');

        self::assertCount(2, $result);
        self::assertSame('Category 1', $result[0]['title']);
        self::assertSame('Category 2', $result[1]['title']);
    }

    /**
     * @test
     */
    public function canResolveDbReferences(): void
    {
        $this->importCSVDataSet('typo3/sysext/content_blocks/Tests/Fixtures/DataSet/db_reference.csv');

        $tableDefinitionCollection = $this->get(LoaderFactory::class)->load();
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $fieldDefinition = $tableDefinition->getTcaColumnsDefinition()->getField('bar_foo_pages_reference');
        $dummyRecord = [
            'uid' => 1,
            'bar_foo_pages_reference' => '1,2',
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection);
        $result = $relationResolver->processField($fieldDefinition, $dummyRecord, 'tt_content');

        self::assertCount(2, $result);
        self::assertSame('Page 1', $result[0]['title']);
        self::assertSame('Page 2', $result[1]['title']);
    }

    /**
     * @test
     */
    public function canResolveMultipleDbReferences(): void
    {
        $this->importCSVDataSet('typo3/sysext/content_blocks/Tests/Fixtures/DataSet/db_reference_multiple.csv');

        $tableDefinitionCollection = $this->get(LoaderFactory::class)->load();
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $fieldDefinition = $tableDefinition->getTcaColumnsDefinition()->getField('bar_foo_pages_content_reference');
        $dummyRecord = [
            'uid' => 1,
            'bar_foo_pages_content_reference' => 'pages_1,pages_2,tt_content_1,tt_content_2',
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection);
        $result = $relationResolver->processField($fieldDefinition, $dummyRecord, 'tt_content');

        self::assertCount(4, $result);
        self::assertSame('Page 1', $result[0]['title']);
        self::assertSame('Page 2', $result[1]['title']);
        self::assertSame('Content 1', $result[2]['header']);
        self::assertSame('Content 2', $result[3]['header']);
    }

    /**
     * @test
     */
    public function canResolveDbReferencesMM(): void
    {
        $this->importCSVDataSet('typo3/sysext/content_blocks/Tests/Fixtures/DataSet/db_reference_mm.csv');

        $tableDefinitionCollection = $this->get(LoaderFactory::class)->load();
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $fieldDefinition = $tableDefinition->getTcaColumnsDefinition()->getField('bar_foo_pages_mm');
        $dummyRecord = [
            'uid' => 1,
            'bar_foo_pages_mm' => 2,
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection);
        $result = $relationResolver->processField($fieldDefinition, $dummyRecord, 'tt_content');

        self::assertCount(2, $result);
        self::assertSame('Page 1', $result[0]['title']);
        self::assertSame('Page 2', $result[1]['title']);
    }

    /**
     * @test
     */
    public function selectCheckboxCommaListConvertedToArray(): void
    {
        $tableDefinitionCollection = $this->get(LoaderFactory::class)->load();
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $fieldDefinition = $tableDefinition->getTcaColumnsDefinition()->getField('bar_foo_select_checkbox');
        $dummyRecord = [
            'uid' => 1,
            'bar_foo_select_checkbox' => '1,2,3',
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection);
        $result = $relationResolver->processField($fieldDefinition, $dummyRecord, 'tt_content');

        self::assertSame(['1', '2', '3'], $result);
    }

    /**
     * @test
     */
    public function selectSingleBoxCommaListConvertedToArray(): void
    {
        $tableDefinitionCollection = $this->get(LoaderFactory::class)->load();
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $fieldDefinition = $tableDefinition->getTcaColumnsDefinition()->getField('bar_foo_select_single_box');
        $dummyRecord = [
            'uid' => 1,
            'bar_foo_select_single_box' => '1,2,3',
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection);
        $result = $relationResolver->processField($fieldDefinition, $dummyRecord, 'tt_content');

        self::assertSame(['1', '2', '3'], $result);
    }

    /**
     * @test
     */
    public function selectMultipleSideBySideCommaListConvertedToArray(): void
    {
        $tableDefinitionCollection = $this->get(LoaderFactory::class)->load();
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $fieldDefinition = $tableDefinition->getTcaColumnsDefinition()->getField('bar_foo_select_multiple');
        $dummyRecord = [
            'uid' => 1,
            'bar_foo_select_multiple' => '1,2,3',
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection);
        $result = $relationResolver->processField($fieldDefinition, $dummyRecord, 'tt_content');

        self::assertSame(['1', '2', '3'], $result);
    }

    /**
     * @test
     */
    public function canResolveSelectForeignTable(): void
    {
        $this->importCSVDataSet('typo3/sysext/content_blocks/Tests/Fixtures/DataSet/select_foreign.csv');
        $tableDefinitionCollection = $this->get(LoaderFactory::class)->load();
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $fieldDefinition = $tableDefinition->getTcaColumnsDefinition()->getField('bar_foo_select_foreign');
        $dummyRecord = [
            'uid' => 1,
            'bar_foo_select_foreign' => '1,2',
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection);
        $result = $relationResolver->processField($fieldDefinition, $dummyRecord, 'tt_content');

        self::assertCount(2, $result);
        self::assertSame('Page 1', $result[0]['title']);
        self::assertSame('Page 2', $result[1]['title']);
    }
}
