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

namespace TYPO3\CMS\ContentBlocks\Tests\Functional\DataProcessing;

use TYPO3\CMS\ContentBlocks\DataProcessing\RelationResolver;
use TYPO3\CMS\ContentBlocks\Loader\ContentBlockLoader;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\WorkspaceAspect;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class RelationResolverTest extends FunctionalTestCase
{
    protected array $coreExtensionsToLoad = [
//        'content_blocks',
        'workspaces',
    ];

    protected array $testExtensionsToLoad = [
        'typo3conf/ext/content_blocks/Tests/Fixtures/Extensions/test_content_blocks_b',
        'typo3conf/ext/content_blocks',
    ];

    protected array $pathsToProvideInTestInstance = [
        'typo3conf/ext/content_blocks/Tests/Fixtures/TestFolder/' => 'fileadmin/',
    ];

    /**
     * @test
     */
    public function canResolveFileReferences(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/file_reference.csv');

        $tableDefinitionCollection = $this->get(ContentBlockLoader::class)->load();
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaColumnsDefinition()->getField('image');
        $dummyRecord = [
            'uid' => 1,
            'image' => 1,
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService());
        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

        self::assertCount(1, $result);
        self::assertInstanceOf(FileReference::class, $result[0]);
    }

    /**
     * @test
     */
    public function canResolveFilesFromFolder(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/folder_files.csv');

        $tableDefinitionCollection = $this->get(ContentBlockLoader::class)->load();
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaColumnsDefinition()->getField('typo3tests_contentelementb_folder');
        $dummyRecord = [
            'uid' => 1,
            'typo3tests_contentelementb_folder' => '1:/',
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService());
        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

        self::assertCount(1, $result);
        self::assertInstanceOf(File::class, $result[0]);
    }

    /**
     * @test
     */
    public function canResolveFilesFromFolderRecursive(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/folder_files.csv');

        $tableDefinitionCollection = $this->get(ContentBlockLoader::class)->load();
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaColumnsDefinition()->getField('typo3tests_contentelementb_folder_recursive');
        $dummyRecord = [
            'uid' => 1,
            'typo3tests_contentelementb_folder_recursive' => '1:/',
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService());
        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

        self::assertCount(2, $result);
        self::assertInstanceOf(File::class, $result[0]);
        self::assertInstanceOf(File::class, $result[1]);
    }

    /**
     * @test
     */
    public function canResolveCollections(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/collections.csv');

        $tableDefinitionCollection = $this->get(ContentBlockLoader::class)->load();
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaColumnsDefinition()->getField('typo3tests_contentelementb_collection');
        $dummyRecord = [
            'uid' => 1,
            'typo3tests_contentelementb_collection' => 2,
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService());
        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

        self::assertCount(2, $result);
        self::assertSame('lorem foo bar', $result[0]['fieldA']);
        self::assertSame('lorem foo bar 2', $result[1]['fieldA']);
    }

    /**
     * @test
     */
    public function canResolveCollectionsRecursively(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/collections_recursive.csv');

        $tableDefinitionCollection = $this->get(ContentBlockLoader::class)->load();
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaColumnsDefinition()->getField('typo3tests_contentelementb_collection_recursive');
        $dummyRecord = [
            'uid' => 1,
            'typo3tests_contentelementb_collection_recursive' => 2,
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService());
        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

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
    public function canResolveCollectionsInWorkspaces(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/collections.csv');
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/be_users.csv');

        $tableDefinitionCollection = $this->get(ContentBlockLoader::class)->load();
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaColumnsDefinition()->getField('typo3tests_contentelementb_collection');
        $this->setUpBackendUser(1);
        $this->setWorkspaceId(1);
        $dummyRecord = [
            'typo3tests_contentelementb_collection' => 2,
            't3ver_oid' => 1,
            't3_origuid' => 1,
            't3ver_wsid' => 1,
            '_ORIG_uid' => 2,
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService());
        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

        self::assertCount(2, $result);
        self::assertSame('lorem foo bar WS', $result[0]['fieldA']);
        self::assertSame('lorem foo bar 2 WS', $result[1]['fieldA']);
    }

    /**
     * @test
     */
    public function canResolveCategoriesManyToMany(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/category_many_to_many.csv');

        $tableDefinitionCollection = $this->get(ContentBlockLoader::class)->load();
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaColumnsDefinition()->getField('typo3tests_contentelementb_categories_mm');
        $dummyRecord = [
            'uid' => 1,
            'typo3tests_contentelementb_categories_mm' => 2,
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService());
        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

        self::assertCount(2, $result);
        self::assertSame('Category 1', $result[0]['title']);
        self::assertSame('Category 2', $result[1]['title']);
    }

    /**
     * @test
     */
    public function canResolveCategoriesManyToManyInWorkspaces(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/category_many_to_many.csv');
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/be_users.csv');

        $tableDefinitionCollection = $this->get(ContentBlockLoader::class)->load();
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaColumnsDefinition()->getField('typo3tests_contentelementb_categories_mm');
        $this->setUpBackendUser(1);
        $this->setWorkspaceId(1);
        $dummyRecord = [
            't3ver_oid' => 1,
            't3_origuid' => 1,
            't3ver_wsid' => 1,
            '_ORIG_uid' => 2,
            'typo3tests_contentelementb_categories_mm' => 2,
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService());
        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

        self::assertCount(2, $result);
        self::assertSame('Category 1 ws', $result[0]['title']);
        self::assertSame('Category 2 ws', $result[1]['title']);
    }

    /**
     * @test
     */
    public function canResolveCategoriesOneToOne(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/category_one_to_one.csv');

        $tableDefinitionCollection = $this->get(ContentBlockLoader::class)->load();
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaColumnsDefinition()->getField('typo3tests_contentelementb_categories_11');
        $dummyRecord = [
            'uid' => 1,
            'typo3tests_contentelementb_categories_11' => 7,
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService());
        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

        self::assertCount(1, $result);
        self::assertSame('Category 1', $result[0]['title']);
    }

    /**
     * @test
     */
    public function canResolveCategoriesOneToMany(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/category_one_to_many.csv');

        $tableDefinitionCollection = $this->get(ContentBlockLoader::class)->load();
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaColumnsDefinition()->getField('typo3tests_contentelementb_categories_1m');
        $dummyRecord = [
            'uid' => 1,
            'typo3tests_contentelementb_categories_1m' => '7,8',
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService());
        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

        self::assertCount(2, $result);
        self::assertSame('Category 1', $result[0]['title']);
        self::assertSame('Category 2', $result[1]['title']);
    }

    /**
     * @test
     */
    public function canResolveDbRelation(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/db_relation.csv');

        $tableDefinitionCollection = $this->get(ContentBlockLoader::class)->load();
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaColumnsDefinition()->getField('typo3tests_contentelementb_pages_relation');
        $dummyRecord = [
            'uid' => 1,
            'typo3tests_contentelementb_pages_relation' => '1,2',
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService());
        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

        self::assertCount(2, $result);
        self::assertSame('Page 1', $result[0]['title']);
        self::assertSame('Page 2', $result[1]['title']);
    }

    /**
     * @test
     */
    public function canResolveDbRelationsInWorkspaces(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/db_relation.csv');
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/be_users.csv');

        $tableDefinitionCollection = $this->get(ContentBlockLoader::class)->load();
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaColumnsDefinition()->getField('typo3tests_contentelementb_pages_relation');
        $this->setUpBackendUser(1);
        $this->setWorkspaceId(1);
        $dummyRecord = [
            't3ver_oid' => 1,
            't3_origuid' => 1,
            't3ver_wsid' => 1,
            '_ORIG_uid' => 2,
            'typo3tests_contentelementb_pages_relation' => '1,2',
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService());
        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

        self::assertCount(2, $result);
        self::assertSame('Page 1 ws', $result[0]['title']);
        self::assertSame('Page 2 ws', $result[1]['title']);
    }

    /**
     * @test
     */
    public function canResolveMultipleDbRelations(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/db_relation_multiple.csv');

        $tableDefinitionCollection = $this->get(ContentBlockLoader::class)->load();
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaColumnsDefinition()->getField('typo3tests_contentelementb_pages_content_relation');
        $dummyRecord = [
            'uid' => 1,
            'typo3tests_contentelementb_pages_content_relation' => 'pages_1,pages_2,tt_content_1,tt_content_2',
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService());
        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

        self::assertCount(4, $result);
        self::assertSame('Page 1', $result[0]['title']);
        self::assertSame('Page 2', $result[1]['title']);
        self::assertSame('Content 1', $result[2]['header']);
        self::assertSame('Content 2', $result[3]['header']);
    }

    /**
     * @test
     */
    public function canResolveDbRelationsMM(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/db_relation_mm.csv');

        $tableDefinitionCollection = $this->get(ContentBlockLoader::class)->load();
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaColumnsDefinition()->getField('typo3tests_contentelementb_pages_mm');
        $dummyRecord = [
            'uid' => 1,
            'typo3tests_contentelementb_pages_mm' => 2,
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService());
        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

        self::assertCount(2, $result);
        self::assertSame('Page 1', $result[0]['title']);
        self::assertSame('Page 2', $result[1]['title']);
    }

    /**
     * @test
     */
    public function selectCheckboxCommaListConvertedToArray(): void
    {
        $tableDefinitionCollection = $this->get(ContentBlockLoader::class)->load();
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaColumnsDefinition()->getField('typo3tests_contentelementb_select_checkbox');
        $dummyRecord = [
            'uid' => 1,
            'typo3tests_contentelementb_select_checkbox' => '1,2,3',
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService());
        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

        self::assertSame(['1', '2', '3'], $result);
    }

    /**
     * @test
     */
    public function selectSingleBoxCommaListConvertedToArray(): void
    {
        $tableDefinitionCollection = $this->get(ContentBlockLoader::class)->load();
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaColumnsDefinition()->getField('typo3tests_contentelementb_select_single_box');
        $dummyRecord = [
            'uid' => 1,
            'typo3tests_contentelementb_select_single_box' => '1,2,3',
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService());
        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

        self::assertSame(['1', '2', '3'], $result);
    }

    /**
     * @test
     */
    public function selectMultipleSideBySideCommaListConvertedToArray(): void
    {
        $tableDefinitionCollection = $this->get(ContentBlockLoader::class)->load();
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaColumnsDefinition()->getField('typo3tests_contentelementb_select_multiple');
        $dummyRecord = [
            'uid' => 1,
            'typo3tests_contentelementb_select_multiple' => '1,2,3',
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService());
        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

        self::assertSame(['1', '2', '3'], $result);
    }

    /**
     * @test
     */
    public function canResolveSelectForeignTableSingle(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/select_foreign.csv');
        $tableDefinitionCollection = $this->get(ContentBlockLoader::class)->load();
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaColumnsDefinition()->getField('typo3tests_contentelementb_select_foreign');
        $dummyRecord = [
            'uid' => 1,
            'typo3tests_contentelementb_select_foreign' => '1',
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService());
        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

        self::assertSame('Record 1', $result['title']);
    }

    /**
     * @test
     */
    public function canResolveSelectForeignTableMultiple(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/select_foreign.csv');
        $tableDefinitionCollection = $this->get(ContentBlockLoader::class)->load();
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaColumnsDefinition()->getField('typo3tests_contentelementb_select_foreign_multiple');
        $dummyRecord = [
            'uid' => 1,
            'typo3tests_contentelementb_select_foreign_multiple' => '1,2',
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService());
        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

        self::assertCount(2, $result);
        self::assertSame('Record 1', $result[0]['title']);
        self::assertSame('Record 2', $result[1]['title']);
    }

    /**
     * @test
     */
    public function canResolveSelectForeignTableRecursive(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/select_foreign_recursive.csv');
        $tableDefinitionCollection = $this->get(ContentBlockLoader::class)->load();
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaColumnsDefinition()->getField('typo3tests_contentelementb_select_foreign');
        $dummyRecord = [
            'uid' => 1,
            'typo3tests_contentelementb_select_foreign' => '1',
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService());
        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

        self::assertSame('Record 1', $result['title']);
        self::assertCount(1, $result['record_collection']);
        self::assertSame('Collection 1', $result['record_collection'][0]['text']);
    }

    /**
     * @test
     */
    public function canResolveFlexForm(): void
    {
        $tableDefinitionCollection = $this->get(ContentBlockLoader::class)->load();
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaColumnsDefinition()->getField('typo3tests_contentelementb_flexfield');
        $dummyRecord = [
            'uid' => 1,
            'typo3tests_contentelementb_flexfield' => '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>
<T3FlexForms>
    <data>
        <sheet index="sDEF">
            <language index="lDEF">
                <field index="header">
                    <value index="vDEF">Header in Flex</value>
                </field>
                <field index="textarea">
                    <value index="vDEF">Text in Flex</value>
                </field>
            </language>
        </sheet>
    </data>
</T3FlexForms>',
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService());
        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

        self::assertSame('Header in Flex', $result['header']);
        self::assertSame('Text in Flex', $result['textarea']);
    }

    /**
     * @test
     */
    public function canResolveFlexFormWithSheetsOtherThanDefault(): void
    {
        $tableDefinitionCollection = $this->get(ContentBlockLoader::class)->load();
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaColumnsDefinition()->getField('typo3tests_contentelementb_flexfield');
        $dummyRecord = [
            'uid' => 1,
            'typo3tests_contentelementb_flexfield' => '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>
<T3FlexForms>
    <data>
        <sheet index="sheet1">
            <language index="lDEF">
                <field index="header">
                    <value index="vDEF">Header in Flex</value>
                </field>
                <field index="textarea">
                    <value index="vDEF">Text in Flex</value>
                </field>
            </language>
        </sheet>
        <sheet index="sheet2">
            <language index="lDEF">
                <field index="link">
                    <value index="vDEF">Link</value>
                </field>
                <field index="number">
                    <value index="vDEF">12</value>
                </field>
            </language>
        </sheet>
    </data>
</T3FlexForms>',
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService());
        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

        self::assertSame('Header in Flex', $result['header']);
        self::assertSame('Text in Flex', $result['textarea']);
        self::assertSame('Link', $result['link']);
        self::assertSame('12', $result['number']);
    }

    protected function setWorkspaceId(int $workspaceId): void
    {
        $GLOBALS['BE_USER']->workspace = $workspaceId;
        GeneralUtility::makeInstance(Context::class)->setAspect('workspace', new WorkspaceAspect($workspaceId));
    }
}
