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

use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\ContentBlocks\DataProcessing\RelationResolver;
use TYPO3\CMS\ContentBlocks\DataProcessing\RelationResolverSession;
use TYPO3\CMS\ContentBlocks\Definition\Factory\TableDefinitionCollectionFactory;
use TYPO3\CMS\ContentBlocks\FieldType\FieldTypeRegistry;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\ContentBlocks\Schema\SimpleTcaSchemaFactory;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\LanguageAspect;
use TYPO3\CMS\Core\Context\WorkspaceAspect;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\TypoScript\AST\Node\RootNode;
use TYPO3\CMS\Core\TypoScript\FrontendTypoScript;
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

    #[Test]
    public function canResolveFileReferences(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/file_reference.csv');

        $simpleTcaSchemaFactory = $this->get(SimpleTcaSchemaFactory::class);
        $fieldTypeRegistry = $this->get(FieldTypeRegistry::class);
        $contentBlockRegistry = $this->get(ContentBlockRegistry::class);
        $tableDefinitionCollection = $this->get(TableDefinitionCollectionFactory::class)->create($contentBlockRegistry, $fieldTypeRegistry, $simpleTcaSchemaFactory);
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getContentTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaFieldDefinitionCollection()->getField('image');
        $dummyRecord = [
            'uid' => 1,
            'image' => 1,
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService(), new RelationResolverSession());

        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

        self::assertCount(1, $result);
        self::assertInstanceOf(FileReference::class, $result[0]);
    }

    #[Test]
    public function canResolveFilesFromFolder(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/folder_files.csv');

        $simpleTcaSchemaFactory = $this->get(SimpleTcaSchemaFactory::class);
        $fieldTypeRegistry = $this->get(FieldTypeRegistry::class);
        $contentBlockRegistry = $this->get(ContentBlockRegistry::class);
        $tableDefinitionCollection = $this->get(TableDefinitionCollectionFactory::class)->create($contentBlockRegistry, $fieldTypeRegistry, $simpleTcaSchemaFactory);
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getContentTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaFieldDefinitionCollection()->getField('typo3tests_contentelementb_folder');
        $dummyRecord = [
            'uid' => 1,
            'typo3tests_contentelementb_folder' => '1:/',
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService(), new RelationResolverSession());

        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

        self::assertCount(1, $result);
        self::assertInstanceOf(File::class, $result[0]);
    }

    #[Test]
    public function canResolveFilesFromFolderRecursive(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/folder_files.csv');

        $simpleTcaSchemaFactory = $this->get(SimpleTcaSchemaFactory::class);
        $fieldTypeRegistry = $this->get(FieldTypeRegistry::class);
        $contentBlockRegistry = $this->get(ContentBlockRegistry::class);
        $tableDefinitionCollection = $this->get(TableDefinitionCollectionFactory::class)->create($contentBlockRegistry, $fieldTypeRegistry, $simpleTcaSchemaFactory);
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getContentTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaFieldDefinitionCollection()->getField('typo3tests_contentelementb_folder_recursive');
        $dummyRecord = [
            'uid' => 1,
            'typo3tests_contentelementb_folder_recursive' => '1:/',
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService(), new RelationResolverSession());

        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

        self::assertCount(2, $result);
        self::assertInstanceOf(File::class, $result[0]);
        self::assertInstanceOf(File::class, $result[1]);
    }

    #[Test]
    public function canResolveCollections(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/collections.csv');

        $simpleTcaSchemaFactory = $this->get(SimpleTcaSchemaFactory::class);
        $fieldTypeRegistry = $this->get(FieldTypeRegistry::class);
        $contentBlockRegistry = $this->get(ContentBlockRegistry::class);
        $tableDefinitionCollection = $this->get(TableDefinitionCollectionFactory::class)->create($contentBlockRegistry, $fieldTypeRegistry, $simpleTcaSchemaFactory);
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getContentTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaFieldDefinitionCollection()->getField('typo3tests_contentelementb_collection');
        $dummyRecord = [
            'uid' => 1,
            'typo3tests_contentelementb_collection' => 2,
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService(), new RelationResolverSession());

        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

        self::assertCount(2, $result);
        self::assertSame('lorem foo bar', $result[0]->resolved['fieldA']);
        self::assertSame('lorem foo bar 2', $result[1]->resolved['fieldA']);
    }

    #[Test]
    public function canResolveCollectionsWithAlternativeTableName(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/collections_alternative_table_name.csv');

        $simpleTcaSchemaFactory = $this->get(SimpleTcaSchemaFactory::class);
        $fieldTypeRegistry = $this->get(FieldTypeRegistry::class);
        $contentBlockRegistry = $this->get(ContentBlockRegistry::class);
        $tableDefinitionCollection = $this->get(TableDefinitionCollectionFactory::class)->create($contentBlockRegistry, $fieldTypeRegistry, $simpleTcaSchemaFactory);
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getContentTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaFieldDefinitionCollection()->getField('typo3tests_contentelementb_collection2');
        $dummyRecord = [
            'uid' => 1,
            'typo3tests_contentelementb_collection2' => 2,
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService(), new RelationResolverSession());

        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

        self::assertCount(2, $result);
        self::assertSame('lorem foo bar', $result[0]->resolved['fieldA']);
        self::assertSame('lorem foo bar 2', $result[1]->resolved['fieldA']);
    }

    #[Test]
    public function canResolveCollectionsExternal(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/collections_external.csv');

        $simpleTcaSchemaFactory = $this->get(SimpleTcaSchemaFactory::class);
        $fieldTypeRegistry = $this->get(FieldTypeRegistry::class);
        $contentBlockRegistry = $this->get(ContentBlockRegistry::class);
        $tableDefinitionCollection = $this->get(TableDefinitionCollectionFactory::class)->create($contentBlockRegistry, $fieldTypeRegistry, $simpleTcaSchemaFactory);
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getContentTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaFieldDefinitionCollection()->getField('typo3tests_contentelementb_collection_external');
        $dummyRecord = [
            'uid' => 1,
            'typo3tests_contentelementb_collection_external' => 2,
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService(), new RelationResolverSession());

        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

        self::assertCount(2, $result);
        self::assertSame('Record 1', $result[0]->resolved['title']);
        self::assertSame('Record 2', $result[1]->resolved['title']);
    }

    #[Test]
    public function canResolveCollectionsRecursively(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/collections_recursive.csv');

        $simpleTcaSchemaFactory = $this->get(SimpleTcaSchemaFactory::class);
        $fieldTypeRegistry = $this->get(FieldTypeRegistry::class);
        $contentBlockRegistry = $this->get(ContentBlockRegistry::class);
        $tableDefinitionCollection = $this->get(TableDefinitionCollectionFactory::class)->create($contentBlockRegistry, $fieldTypeRegistry, $simpleTcaSchemaFactory);
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getContentTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaFieldDefinitionCollection()->getField('typo3tests_contentelementb_collection_recursive');
        $dummyRecord = [
            'uid' => 1,
            'typo3tests_contentelementb_collection_recursive' => 2,
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService(), new RelationResolverSession());

        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

        self::assertCount(2, $result);
        self::assertSame('lorem foo bar A', $result[0]->resolved['fieldA']);
        self::assertSame('lorem foo bar A2', $result[1]->resolved['fieldA']);
        self::assertCount(2, $result[0]->resolved['collection_inner']);
        self::assertSame('lorem foo bar B', $result[0]->resolved['collection_inner'][0]->resolved['fieldB']);
        self::assertSame('lorem foo bar B2', $result[0]->resolved['collection_inner'][1]->resolved['fieldB']);
    }

    #[Test]
    public function canResolveCollectionsInWorkspaces(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/collections.csv');
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/be_users.csv');

        $simpleTcaSchemaFactory = $this->get(SimpleTcaSchemaFactory::class);
        $fieldTypeRegistry = $this->get(FieldTypeRegistry::class);
        $contentBlockRegistry = $this->get(ContentBlockRegistry::class);
        $tableDefinitionCollection = $this->get(TableDefinitionCollectionFactory::class)->create($contentBlockRegistry, $fieldTypeRegistry, $simpleTcaSchemaFactory);
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getContentTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaFieldDefinitionCollection()->getField('typo3tests_contentelementb_collection');
        $this->setUpBackendUser(1);
        $this->setWorkspaceId(1);
        $dummyRecord = [
            'typo3tests_contentelementb_collection' => 2,
            't3ver_oid' => 1,
            't3ver_wsid' => 1,
            '_ORIG_uid' => 2,
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService(), new RelationResolverSession());

        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

        self::assertCount(2, $result);
        self::assertSame('lorem foo bar WS', $result[0]->resolved['fieldA']);
        self::assertSame('lorem foo bar 2 WS', $result[1]->resolved['fieldA']);
    }

    #[Test]
    public function canResolveCategoriesManyToMany(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/category_many_to_many.csv');

        $simpleTcaSchemaFactory = $this->get(SimpleTcaSchemaFactory::class);
        $fieldTypeRegistry = $this->get(FieldTypeRegistry::class);
        $contentBlockRegistry = $this->get(ContentBlockRegistry::class);
        $tableDefinitionCollection = $this->get(TableDefinitionCollectionFactory::class)->create($contentBlockRegistry, $fieldTypeRegistry, $simpleTcaSchemaFactory);
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getContentTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaFieldDefinitionCollection()->getField('typo3tests_contentelementb_categories_mm');
        $dummyRecord = [
            'uid' => 1,
            'typo3tests_contentelementb_categories_mm' => 2,
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService(), new RelationResolverSession());

        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

        self::assertCount(2, $result);
        self::assertSame('Category 1', $result[0]->resolved['title']);
        self::assertSame('Category 2', $result[1]->resolved['title']);
    }

    #[Test]
    public function canResolveCategoriesManyToManyInWorkspaces(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/category_many_to_many.csv');
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/be_users.csv');

        $simpleTcaSchemaFactory = $this->get(SimpleTcaSchemaFactory::class);
        $fieldTypeRegistry = $this->get(FieldTypeRegistry::class);
        $contentBlockRegistry = $this->get(ContentBlockRegistry::class);
        $tableDefinitionCollection = $this->get(TableDefinitionCollectionFactory::class)->create($contentBlockRegistry, $fieldTypeRegistry, $simpleTcaSchemaFactory);
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getContentTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaFieldDefinitionCollection()->getField('typo3tests_contentelementb_categories_mm');
        $this->setUpBackendUser(1);
        $this->setWorkspaceId(1);
        $dummyRecord = [
            't3ver_oid' => 1,
            't3ver_wsid' => 1,
            '_ORIG_uid' => 2,
            'typo3tests_contentelementb_categories_mm' => 2,
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService(), new RelationResolverSession());

        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

        self::assertCount(2, $result);
        self::assertSame('Category 1 ws', $result[0]->resolved['title']);
        self::assertSame('Category 2 ws', $result[1]->resolved['title']);
    }

    #[Test]
    public function canResolveCategoriesManyToManyLocalized(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/category_many_to_many_localized.csv');
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/be_users.csv');

        $simpleTcaSchemaFactory = $this->get(SimpleTcaSchemaFactory::class);
        $fieldTypeRegistry = $this->get(FieldTypeRegistry::class);
        $contentBlockRegistry = $this->get(ContentBlockRegistry::class);
        $tableDefinitionCollection = $this->get(TableDefinitionCollectionFactory::class)->create($contentBlockRegistry, $fieldTypeRegistry, $simpleTcaSchemaFactory);
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getContentTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaFieldDefinitionCollection()->getField('typo3tests_contentelementb_categories_mm');

        $context = GeneralUtility::makeInstance(Context::class);
        $context->setAspect('language', new LanguageAspect(1, 1, LanguageAspect::OVERLAYS_OFF));
        $frontendTypoScript = new FrontendTypoScript(new RootNode(), []);
        $frontendTypoScript->setSetupArray([]);
        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest())
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_FE)
            ->withAttribute('frontend.typoscript', $frontendTypoScript);

        $dummyRecord = [
            'uid' => 1,
            'typo3tests_contentelementb_categories_mm' => 2,
            '_LOCALIZED_UID' => 2,
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService(), new RelationResolverSession());

        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

        self::assertCount(1, $result);
        self::assertSame('Category 1 translated', $result[0]->resolved['title']);
    }

    #[Test]
    public function canResolveCategoriesOneToOne(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/category_one_to_one.csv');

        $simpleTcaSchemaFactory = $this->get(SimpleTcaSchemaFactory::class);
        $fieldTypeRegistry = $this->get(FieldTypeRegistry::class);
        $contentBlockRegistry = $this->get(ContentBlockRegistry::class);
        $tableDefinitionCollection = $this->get(TableDefinitionCollectionFactory::class)->create($contentBlockRegistry, $fieldTypeRegistry, $simpleTcaSchemaFactory);
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getContentTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaFieldDefinitionCollection()->getField('typo3tests_contentelementb_categories_11');
        $dummyRecord = [
            'uid' => 1,
            'typo3tests_contentelementb_categories_11' => 7,
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService(), new RelationResolverSession());

        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

        self::assertCount(1, $result);
        self::assertSame('Category 1', $result[0]->resolved['title']);
    }

    #[Test]
    public function canResolveCategoriesOneToMany(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/category_one_to_many.csv');

        $simpleTcaSchemaFactory = $this->get(SimpleTcaSchemaFactory::class);
        $fieldTypeRegistry = $this->get(FieldTypeRegistry::class);
        $contentBlockRegistry = $this->get(ContentBlockRegistry::class);
        $tableDefinitionCollection = $this->get(TableDefinitionCollectionFactory::class)->create($contentBlockRegistry, $fieldTypeRegistry, $simpleTcaSchemaFactory);
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getContentTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaFieldDefinitionCollection()->getField('typo3tests_contentelementb_categories_1m');
        $dummyRecord = [
            'uid' => 1,
            'typo3tests_contentelementb_categories_1m' => '7,8',
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService(), new RelationResolverSession());

        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

        self::assertCount(2, $result);
        self::assertSame('Category 1', $result[0]->resolved['title']);
        self::assertSame('Category 2', $result[1]->resolved['title']);
    }

    #[Test]
    public function canResolveDbRelation(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/db_relation.csv');

        $simpleTcaSchemaFactory = $this->get(SimpleTcaSchemaFactory::class);
        $fieldTypeRegistry = $this->get(FieldTypeRegistry::class);
        $contentBlockRegistry = $this->get(ContentBlockRegistry::class);
        $tableDefinitionCollection = $this->get(TableDefinitionCollectionFactory::class)->create($contentBlockRegistry, $fieldTypeRegistry, $simpleTcaSchemaFactory);
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getContentTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaFieldDefinitionCollection()->getField('typo3tests_contentelementb_pages_relation');
        $dummyRecord = [
            'uid' => 1,
            'typo3tests_contentelementb_pages_relation' => '1,2',
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService(), new RelationResolverSession());

        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

        self::assertCount(2, $result);
        self::assertSame('Page 1', $result[0]->resolved['title']);
        self::assertSame('Page 2', $result[1]->resolved['title']);
    }

    #[Test]
    public function canResolveCircularRelation(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/circular_relation.csv');

        $simpleTcaSchemaFactory = $this->get(SimpleTcaSchemaFactory::class);
        $fieldTypeRegistry = $this->get(FieldTypeRegistry::class);
        $contentBlockRegistry = $this->get(ContentBlockRegistry::class);
        $tableDefinitionCollection = $this->get(TableDefinitionCollectionFactory::class)->create($contentBlockRegistry, $fieldTypeRegistry, $simpleTcaSchemaFactory);
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getContentTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaFieldDefinitionCollection()->getField('typo3tests_contentelementb_circular_relation');
        $dummyRecord = [
            'uid' => 1,
            'typo3tests_contentelementb_circular_relation' => '1',
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService(), new RelationResolverSession());
        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

        self::assertCount(1, $result);
        self::assertSame(1, $result[0]->resolved['uid']);
    }

    #[Test]
    public function canResolveDbRelationRecursive(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/db_relation_recursive.csv');

        $simpleTcaSchemaFactory = $this->get(SimpleTcaSchemaFactory::class);
        $fieldTypeRegistry = $this->get(FieldTypeRegistry::class);
        $contentBlockRegistry = $this->get(ContentBlockRegistry::class);
        $tableDefinitionCollection = $this->get(TableDefinitionCollectionFactory::class)->create($contentBlockRegistry, $fieldTypeRegistry, $simpleTcaSchemaFactory);
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getContentTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaFieldDefinitionCollection()->getField('typo3tests_contentelementb_record_relation_recursive');
        $dummyRecord = [
            'uid' => 1,
            'typo3tests_contentelementb_record_relation_recursive' => '1,2',
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService(), new RelationResolverSession());

        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

        self::assertSame('Record 1', $result[0]->resolved['title']);
        self::assertSame('Record 2', $result[1]->resolved['title']);
        self::assertCount(1, $result[0]->resolved['record_collection']);
        self::assertCount(1, $result[1]->resolved['record_collection']);
        self::assertSame('Collection 1', $result[0]->resolved['record_collection'][0]->resolved['text']);
        self::assertSame('Collection 2', $result[1]->resolved['record_collection'][0]->resolved['text']);
    }

    #[Test]
    public function canResolveDbRelationsInWorkspaces(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/db_relation.csv');
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/be_users.csv');

        $simpleTcaSchemaFactory = $this->get(SimpleTcaSchemaFactory::class);
        $fieldTypeRegistry = $this->get(FieldTypeRegistry::class);
        $contentBlockRegistry = $this->get(ContentBlockRegistry::class);
        $tableDefinitionCollection = $this->get(TableDefinitionCollectionFactory::class)->create($contentBlockRegistry, $fieldTypeRegistry, $simpleTcaSchemaFactory);
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getContentTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaFieldDefinitionCollection()->getField('typo3tests_contentelementb_pages_relation');
        $this->setUpBackendUser(1);
        $this->setWorkspaceId(1);
        $dummyRecord = [
            't3ver_oid' => 1,
            't3ver_wsid' => 1,
            '_ORIG_uid' => 2,
            'typo3tests_contentelementb_pages_relation' => '1,2',
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService(), new RelationResolverSession());

        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

        self::assertCount(2, $result);
        self::assertSame('Page 1 ws', $result[0]->resolved['title']);
        self::assertSame('Page 2 ws', $result[1]->resolved['title']);
    }

    #[Test]
    public function canResolveMultipleDbRelations(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/db_relation_multiple.csv');

        $simpleTcaSchemaFactory = $this->get(SimpleTcaSchemaFactory::class);
        $fieldTypeRegistry = $this->get(FieldTypeRegistry::class);
        $contentBlockRegistry = $this->get(ContentBlockRegistry::class);
        $tableDefinitionCollection = $this->get(TableDefinitionCollectionFactory::class)->create($contentBlockRegistry, $fieldTypeRegistry, $simpleTcaSchemaFactory);
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getContentTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaFieldDefinitionCollection()->getField('typo3tests_contentelementb_pages_content_relation');
        $dummyRecord = [
            'uid' => 1,
            'typo3tests_contentelementb_pages_content_relation' => 'pages_1,pages_2,tt_content_1,tt_content_2',
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService(), new RelationResolverSession());

        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

        self::assertCount(4, $result);
        self::assertSame('Page 1', $result[0]->resolved['title']);
        self::assertSame('Page 2', $result[1]->resolved['title']);
        self::assertSame('Content 1', $result[2]->resolved['header']);
        self::assertSame('Content 2', $result[3]->resolved['header']);
    }

    #[Test]
    public function canResolveDbRelationsMM(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/db_relation_mm.csv');

        $simpleTcaSchemaFactory = $this->get(SimpleTcaSchemaFactory::class);
        $fieldTypeRegistry = $this->get(FieldTypeRegistry::class);
        $contentBlockRegistry = $this->get(ContentBlockRegistry::class);
        $tableDefinitionCollection = $this->get(TableDefinitionCollectionFactory::class)->create($contentBlockRegistry, $fieldTypeRegistry, $simpleTcaSchemaFactory);
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getContentTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaFieldDefinitionCollection()->getField('typo3tests_contentelementb_pages_mm');
        $dummyRecord = [
            'uid' => 1,
            'typo3tests_contentelementb_pages_mm' => 2,
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService(), new RelationResolverSession());

        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

        self::assertCount(2, $result);
        self::assertSame('Page 1', $result[0]->resolved['title']);
        self::assertSame('Page 2', $result[1]->resolved['title']);
    }

    #[Test]
    public function selectCheckboxCommaListConvertedToArray(): void
    {
        $simpleTcaSchemaFactory = $this->get(SimpleTcaSchemaFactory::class);
        $fieldTypeRegistry = $this->get(FieldTypeRegistry::class);
        $contentBlockRegistry = $this->get(ContentBlockRegistry::class);
        $tableDefinitionCollection = $this->get(TableDefinitionCollectionFactory::class)->create($contentBlockRegistry, $fieldTypeRegistry, $simpleTcaSchemaFactory);
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getContentTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaFieldDefinitionCollection()->getField('typo3tests_contentelementb_select_checkbox');
        $dummyRecord = [
            'uid' => 1,
            'typo3tests_contentelementb_select_checkbox' => '1,2,3',
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService(), new RelationResolverSession());

        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

        self::assertSame(['1', '2', '3'], $result);
    }

    #[Test]
    public function selectSingleBoxCommaListConvertedToArray(): void
    {
        $simpleTcaSchemaFactory = $this->get(SimpleTcaSchemaFactory::class);
        $fieldTypeRegistry = $this->get(FieldTypeRegistry::class);
        $contentBlockRegistry = $this->get(ContentBlockRegistry::class);
        $tableDefinitionCollection = $this->get(TableDefinitionCollectionFactory::class)->create($contentBlockRegistry, $fieldTypeRegistry, $simpleTcaSchemaFactory);
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getContentTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaFieldDefinitionCollection()->getField('typo3tests_contentelementb_select_single_box');
        $dummyRecord = [
            'uid' => 1,
            'typo3tests_contentelementb_select_single_box' => '1,2,3',
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService(), new RelationResolverSession());

        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

        self::assertSame(['1', '2', '3'], $result);
    }

    #[Test]
    public function selectMultipleSideBySideCommaListConvertedToArray(): void
    {
        $simpleTcaSchemaFactory = $this->get(SimpleTcaSchemaFactory::class);
        $fieldTypeRegistry = $this->get(FieldTypeRegistry::class);
        $contentBlockRegistry = $this->get(ContentBlockRegistry::class);
        $tableDefinitionCollection = $this->get(TableDefinitionCollectionFactory::class)->create($contentBlockRegistry, $fieldTypeRegistry, $simpleTcaSchemaFactory);
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getContentTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaFieldDefinitionCollection()->getField('typo3tests_contentelementb_select_multiple');
        $dummyRecord = [
            'uid' => 1,
            'typo3tests_contentelementb_select_multiple' => '1,2,3',
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService(), new RelationResolverSession());

        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

        self::assertSame(['1', '2', '3'], $result);
    }

    #[Test]
    public function selectMultipleSideBySideWithOneValueConvertedToArray(): void
    {
        $simpleTcaSchemaFactory = $this->get(SimpleTcaSchemaFactory::class);
        $fieldTypeRegistry = $this->get(FieldTypeRegistry::class);
        $contentBlockRegistry = $this->get(ContentBlockRegistry::class);
        $tableDefinitionCollection = $this->get(TableDefinitionCollectionFactory::class)->create($contentBlockRegistry, $fieldTypeRegistry, $simpleTcaSchemaFactory);
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getContentTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaFieldDefinitionCollection()->getField('typo3tests_contentelementb_select_multiple');
        $dummyRecord = [
            'uid' => 1,
            'typo3tests_contentelementb_select_multiple' => '1',
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService(), new RelationResolverSession());

        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

        self::assertSame(['1'], $result);
    }

    #[Test]
    public function selectMultipleSideBySideWithEmptyOneValueConvertedToArray(): void
    {
        $simpleTcaSchemaFactory = $this->get(SimpleTcaSchemaFactory::class);
        $fieldTypeRegistry = $this->get(FieldTypeRegistry::class);
        $contentBlockRegistry = $this->get(ContentBlockRegistry::class);
        $tableDefinitionCollection = $this->get(TableDefinitionCollectionFactory::class)->create($contentBlockRegistry, $fieldTypeRegistry, $simpleTcaSchemaFactory);
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getContentTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaFieldDefinitionCollection()->getField('typo3tests_contentelementb_select_multiple');
        $dummyRecord = [
            'uid' => 1,
            'typo3tests_contentelementb_select_multiple' => '',
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService(), new RelationResolverSession());

        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

        self::assertSame([], $result);
    }

    #[Test]
    public function canResolveSelectForeignTableSingle(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/select_foreign.csv');
        $simpleTcaSchemaFactory = $this->get(SimpleTcaSchemaFactory::class);
        $fieldTypeRegistry = $this->get(FieldTypeRegistry::class);
        $contentBlockRegistry = $this->get(ContentBlockRegistry::class);
        $tableDefinitionCollection = $this->get(TableDefinitionCollectionFactory::class)->create($contentBlockRegistry, $fieldTypeRegistry, $simpleTcaSchemaFactory);
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getContentTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaFieldDefinitionCollection()->getField('typo3tests_contentelementb_select_foreign');
        $dummyRecord = [
            'uid' => 1,
            'typo3tests_contentelementb_select_foreign' => '1',
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService(), new RelationResolverSession());

        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

        self::assertSame('Record 1', $result->resolved['title']);
    }

    #[Test]
    public function canResolveSelectForeignTableMultiple(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/select_foreign.csv');
        $simpleTcaSchemaFactory = $this->get(SimpleTcaSchemaFactory::class);
        $fieldTypeRegistry = $this->get(FieldTypeRegistry::class);
        $contentBlockRegistry = $this->get(ContentBlockRegistry::class);
        $tableDefinitionCollection = $this->get(TableDefinitionCollectionFactory::class)->create($contentBlockRegistry, $fieldTypeRegistry, $simpleTcaSchemaFactory);
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getContentTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaFieldDefinitionCollection()->getField('typo3tests_contentelementb_select_foreign_multiple');
        $dummyRecord = [
            'uid' => 1,
            'typo3tests_contentelementb_select_foreign_multiple' => '1,2',
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService(), new RelationResolverSession());

        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

        self::assertCount(2, $result);
        self::assertSame('Record 1', $result[0]->resolved['title']);
        self::assertSame('Record 2', $result[1]->resolved['title']);
    }

    #[Test]
    public function canResolveSelectForeignTableMultipleAndSame(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/foreign_table_select_multiple.csv');
        $simpleTcaSchemaFactory = $this->get(SimpleTcaSchemaFactory::class);
        $fieldTypeRegistry = $this->get(FieldTypeRegistry::class);
        $contentBlockRegistry = $this->get(ContentBlockRegistry::class);
        $tableDefinitionCollection = $this->get(TableDefinitionCollectionFactory::class)->create($contentBlockRegistry, $fieldTypeRegistry, $simpleTcaSchemaFactory);
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getContentTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaFieldDefinitionCollection()->getField('typo3tests_contentelementb_select_foreign_multiple');
        $dummyRecord = [
            'uid' => 1,
            'typo3tests_contentelementb_select_foreign_multiple' => '1,1',
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService(), new RelationResolverSession());

        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

        self::assertCount(2, $result);
        self::assertSame('Record 1', $result[0]->resolved['title']);
        self::assertSame('Collection 1', $result[0]->resolved['record_collection'][0]->resolved['text']);
        self::assertSame('Record 1', $result[1]->resolved['title']);
        self::assertSame('Collection 1', $result[1]->resolved['record_collection'][0]->resolved['text']);
    }

    #[Test]
    public function canResolveSelectForeignNativeTableSingle(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/select_foreign_native.csv');
        $simpleTcaSchemaFactory = $this->get(SimpleTcaSchemaFactory::class);
        $fieldTypeRegistry = $this->get(FieldTypeRegistry::class);
        $contentBlockRegistry = $this->get(ContentBlockRegistry::class);
        $tableDefinitionCollection = $this->get(TableDefinitionCollectionFactory::class)->create($contentBlockRegistry, $fieldTypeRegistry, $simpleTcaSchemaFactory);
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getContentTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaFieldDefinitionCollection()->getField('typo3tests_contentelementb_select_foreign_native');
        $dummyRecord = [
            'uid' => 1,
            'typo3tests_contentelementb_select_foreign_native' => '1',
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService(), new RelationResolverSession());

        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

        self::assertSame('Record 1', $result->resolved['title']);
    }

    #[Test]
    public function canResolveSelectForeignNativeTableMultiple(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/select_foreign_native.csv');
        $simpleTcaSchemaFactory = $this->get(SimpleTcaSchemaFactory::class);
        $fieldTypeRegistry = $this->get(FieldTypeRegistry::class);
        $contentBlockRegistry = $this->get(ContentBlockRegistry::class);
        $tableDefinitionCollection = $this->get(TableDefinitionCollectionFactory::class)->create($contentBlockRegistry, $fieldTypeRegistry, $simpleTcaSchemaFactory);
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getContentTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaFieldDefinitionCollection()->getField('typo3tests_contentelementb_select_foreign_native_multiple');
        $dummyRecord = [
            'uid' => 1,
            'typo3tests_contentelementb_select_foreign_native_multiple' => '1,2',
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService(), new RelationResolverSession());

        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

        self::assertCount(2, $result);
        self::assertSame('Record 1', $result[0]->resolved['title']);
        self::assertSame('Record 2', $result[1]->resolved['title']);
    }

    #[Test]
    public function canResolveSelectForeignTableRecursive(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/select_foreign_recursive.csv');
        $simpleTcaSchemaFactory = $this->get(SimpleTcaSchemaFactory::class);
        $fieldTypeRegistry = $this->get(FieldTypeRegistry::class);
        $contentBlockRegistry = $this->get(ContentBlockRegistry::class);
        $tableDefinitionCollection = $this->get(TableDefinitionCollectionFactory::class)->create($contentBlockRegistry, $fieldTypeRegistry, $simpleTcaSchemaFactory);
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getContentTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaFieldDefinitionCollection()->getField('typo3tests_contentelementb_select_foreign');
        $dummyRecord = [
            'uid' => 1,
            'typo3tests_contentelementb_select_foreign' => '1',
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService(), new RelationResolverSession());

        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

        self::assertSame('Record 1', $result->resolved['title']);
        self::assertCount(1, $result->resolved['record_collection']);
        self::assertSame('Collection 1', $result->resolved['record_collection'][0]->resolved['text']);
    }

    #[Test]
    public function canResolveFlexForm(): void
    {
        $simpleTcaSchemaFactory = $this->get(SimpleTcaSchemaFactory::class);
        $fieldTypeRegistry = $this->get(FieldTypeRegistry::class);
        $contentBlockRegistry = $this->get(ContentBlockRegistry::class);
        $tableDefinitionCollection = $this->get(TableDefinitionCollectionFactory::class)->create($contentBlockRegistry, $fieldTypeRegistry, $simpleTcaSchemaFactory);
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getContentTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaFieldDefinitionCollection()->getField('typo3tests_contentelementb_flexfield');
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

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService(), new RelationResolverSession());

        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

        self::assertSame('Header in Flex', $result['header']);
        self::assertSame('Text in Flex', $result['textarea']);
    }

    #[Test]
    public function canResolveFlexFormWithSheetsOtherThanDefault(): void
    {
        $simpleTcaSchemaFactory = $this->get(SimpleTcaSchemaFactory::class);
        $fieldTypeRegistry = $this->get(FieldTypeRegistry::class);
        $contentBlockRegistry = $this->get(ContentBlockRegistry::class);
        $tableDefinitionCollection = $this->get(TableDefinitionCollectionFactory::class)->create($contentBlockRegistry, $fieldTypeRegistry, $simpleTcaSchemaFactory);
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getContentTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaFieldDefinitionCollection()->getField('typo3tests_contentelementb_flexfield');
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

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService(), new RelationResolverSession());

        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

        self::assertSame('Header in Flex', $result['header']);
        self::assertSame('Text in Flex', $result['textarea']);
        self::assertSame('Link', $result['link']);
        self::assertSame('12', $result['number']);
    }

    #[Test]
    public function canResolveJson(): void
    {
        $simpleTcaSchemaFactory = $this->get(SimpleTcaSchemaFactory::class);
        $fieldTypeRegistry = $this->get(FieldTypeRegistry::class);
        $contentBlockRegistry = $this->get(ContentBlockRegistry::class);
        $tableDefinitionCollection = $this->get(TableDefinitionCollectionFactory::class)->create($contentBlockRegistry, $fieldTypeRegistry, $simpleTcaSchemaFactory);
        $tableDefinition = $tableDefinitionCollection->getTable('tt_content');
        $elementDefinition = $tableDefinition->getContentTypeDefinitionCollection()->getType('typo3tests_contentelementb');
        $fieldDefinition = $tableDefinition->getTcaFieldDefinitionCollection()->getField('typo3tests_contentelementb_json');
        $dummyRecord = [
            'uid' => 1,
            'typo3tests_contentelementb_json' => '{"foo": "bar"}',
        ];

        $relationResolver = new RelationResolver($tableDefinitionCollection, new FlexFormService(), new RelationResolverSession());

        $result = $relationResolver->processField($fieldDefinition, $elementDefinition, $dummyRecord, 'tt_content');

        self::assertSame(['foo' => 'bar'], $result);
    }

    protected function setWorkspaceId(int $workspaceId): void
    {
        $GLOBALS['BE_USER']->workspace = $workspaceId;
        GeneralUtility::makeInstance(Context::class)->setAspect('workspace', new WorkspaceAspect($workspaceId));
    }
}
