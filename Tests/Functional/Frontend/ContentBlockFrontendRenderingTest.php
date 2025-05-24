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

namespace TYPO3\CMS\ContentBlocks\Tests\Functional\Frontend;

use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Tests\Functional\SiteHandling\SiteBasedTestTrait;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class ContentBlockFrontendRenderingTest extends FunctionalTestCase
{
    use SiteBasedTestTrait;

    protected array $coreExtensionsToLoad = [
        'workspaces',
    ];

    protected array $testExtensionsToLoad = [
        'typo3conf/ext/content_blocks/Tests/Fixtures/Extensions/test_content_blocks_b',
        'typo3conf/ext/content_blocks/Tests/Fixtures/Extensions/test_content_blocks_c',
        'typo3conf/ext/content_blocks',
    ];

    protected const LANGUAGE_PRESETS = [
        'EN' => ['id' => 0, 'title' => 'English', 'locale' => 'en_US.UTF8'],
    ];

    protected const ROOT_PAGE_ID = 1;

    public function setUp(): void
    {
        parent::setUp();
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/base.csv');
        $this->writeSiteConfiguration(
            'fluid_template',
            $this->buildSiteConfiguration(self::ROOT_PAGE_ID, '/'),
        );
    }

    #[Test]
    public function variablesAndAssetsRendered(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/frontend_simple_element.csv');
        $this->setUpFrontendRootPage(
            self::ROOT_PAGE_ID,
            [
                'EXT:content_blocks/Tests/Functional/Frontend/Fixtures/frontend.typoscript',
            ]
        );
        $response = $this->executeFrontendSubRequest((new InternalRequest())->withPageId(self::ROOT_PAGE_ID));
        $html = (string)$response->getBody();
        self::assertStringContainsString('HeaderSimple', $html);
        self::assertStringContainsString('BodytextSimple', $html);
        self::assertStringContainsString('Simple Content Block', $html);
        self::assertStringContainsString('<p>uid:1</p>', $html);
        self::assertStringContainsString('<p>pid:1</p>', $html);
        self::assertStringContainsString('<p>languageId:0</p>', $html);
        self::assertStringContainsString('<p>typeName:simple_simple</p>', $html);
        self::assertStringContainsString('<p>CType:simple_simple</p>', $html);
        self::assertStringContainsString('<p>tableName:tt_content</p>', $html);
        self::assertStringContainsString('<p>creationDate:1697810914</p>', $html);
        self::assertStringContainsString('<p>updateDate:1697810925</p>', $html);
        self::assertStringContainsString('<link href="/typo3conf/ext/test_content_blocks_c/Resources/Public/ContentBlocks/simple/simple/Frontend.css', $html);
        self::assertStringContainsString('<script src="/typo3conf/ext/test_content_blocks_c/Resources/Public/ContentBlocks/simple/simple/Frontend.js', $html);
        self::assertStringContainsString('<img src="/typo3conf/ext/test_content_blocks_c/Resources/Public/ContentBlocks/simple/simple/icon.svg', $html);
        self::assertStringContainsString('<img src="http://localhost/typo3conf/ext/test_content_blocks_c/Resources/Public/ContentBlocks/simple/simple/icon.svg', $html);
    }

    #[Test]
    public function relationsAreResolvedForCollections(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/collections.csv');
        $this->setUpFrontendRootPage(
            self::ROOT_PAGE_ID,
            [
                'EXT:content_blocks/Tests/Functional/Frontend/Fixtures/frontend.typoscript',
            ]
        );
        $response = $this->executeFrontendSubRequest((new InternalRequest())->withPageId(self::ROOT_PAGE_ID));
        $html = (string)$response->getBody();

        self::assertStringContainsString('fieldA1: lorem foo bar', $html);
        self::assertStringContainsString('fieldA2: lorem foo bar 2', $html);
    }

    #[Test]
    public function relationsAreResolvedForCollectionsRelationOneToOne(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/collections_one_to_one.csv');
        $this->setUpFrontendRootPage(
            self::ROOT_PAGE_ID,
            [
                'EXT:content_blocks/Tests/Functional/Frontend/Fixtures/frontend.typoscript',
            ]
        );
        $response = $this->executeFrontendSubRequest((new InternalRequest())->withPageId(self::ROOT_PAGE_ID));
        $html = (string)$response->getBody();

        self::assertStringContainsString('fieldAOneToOne11: lorem foo bar', $html);
    }

    #[Test]
    public function relationsAreResolvedForCollectionsRecursive(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/collections_recursive.csv');
        $this->setUpFrontendRootPage(
            self::ROOT_PAGE_ID,
            [
                'EXT:content_blocks/Tests/Functional/Frontend/Fixtures/frontend.typoscript',
            ]
        );
        $response = $this->executeFrontendSubRequest((new InternalRequest())->withPageId(self::ROOT_PAGE_ID));
        $html = (string)$response->getBody();

        self::assertStringContainsString('fieldA1: lorem foo bar A', $html);
        self::assertStringContainsString('fieldA_raw1: 2', $html);
        self::assertStringContainsString('fieldB1: lorem foo bar B', $html);
        self::assertStringContainsString('fieldA_raw1: 2', $html);
        self::assertStringContainsString('fieldB2: lorem foo bar B2', $html);
        self::assertStringContainsString('fieldA2: lorem foo bar A2', $html);
    }

    #[Test]
    public function relationsAreResolvedForFileReferences(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/file_references.csv');
        $this->setUpFrontendRootPage(
            self::ROOT_PAGE_ID,
            [
                'EXT:content_blocks/Tests/Functional/Frontend/Fixtures/frontend.typoscript',
            ]
        );
        $response = $this->executeFrontendSubRequest((new InternalRequest())->withPageId(self::ROOT_PAGE_ID));
        $html = (string)$response->getBody();

        self::assertStringContainsString('image:kasper-skarhoj1.jpg', $html);
    }

    #[Test]
    public function relationsAreResolvedForSelectSingleForeignTable(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/foreign_table_select.csv');
        $this->setUpFrontendRootPage(
            self::ROOT_PAGE_ID,
            [
                'EXT:content_blocks/Tests/Functional/Frontend/Fixtures/frontend.typoscript',
            ]
        );
        $response = $this->executeFrontendSubRequest((new InternalRequest())->withPageId(self::ROOT_PAGE_ID));
        $html = (string)$response->getBody();

        self::assertStringContainsString('TypeName: record1', $html);
        self::assertStringContainsString('Title: Custom Record 1', $html);
    }

    #[Test]
    public function relationsAreResolvedForSelectMultipleForeignTable(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/foreign_table_select_multiple.csv');
        $this->setUpFrontendRootPage(
            self::ROOT_PAGE_ID,
            [
                'EXT:content_blocks/Tests/Functional/Frontend/Fixtures/frontend.typoscript',
            ]
        );
        $response = $this->executeFrontendSubRequest((new InternalRequest())->withPageId(self::ROOT_PAGE_ID));
        $html = (string)$response->getBody();

        self::assertStringContainsString('TypeName: record1', $html);
        self::assertStringContainsString('Title: Custom Record 1', $html);
        self::assertStringContainsString('Title: Custom Record 2', $html);
    }

    #[Test]
    public function relationsAreResolvedForTypeRelation(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/relation.csv');
        $this->setUpFrontendRootPage(
            self::ROOT_PAGE_ID,
            [
                'EXT:content_blocks/Tests/Functional/Frontend/Fixtures/frontend.typoscript',
            ]
        );
        $response = $this->executeFrontendSubRequest((new InternalRequest())->withPageId(self::ROOT_PAGE_ID));
        $html = (string)$response->getBody();

        self::assertStringContainsString('TypeName: record1', $html);
        self::assertStringContainsString('Title Relation: Custom Record 1', $html);
        self::assertStringContainsString('Title Relation: Custom Record 2', $html);
    }

    #[Test]
    public function vendorPrefixedFieldsCanBeAccessedByNormalIdentifier(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/vendor_prefix.csv');
        $this->setUpFrontendRootPage(
            self::ROOT_PAGE_ID,
            [
                'EXT:content_blocks/Tests/Functional/Frontend/Fixtures/frontend.typoscript',
            ]
        );
        $response = $this->executeFrontendSubRequest((new InternalRequest())->withPageId(self::ROOT_PAGE_ID));
        $html = (string)$response->getBody();

        self::assertStringContainsString('My Field: Simple Text', $html);
        self::assertStringContainsString('Full prefix: Text full prefix', $html);
        self::assertStringContainsString('Collection Text: lorem foo bar', $html);
    }

    #[Test]
    public function nestedContentIsAvailableAsContentBlockDataForCoreElements(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/relation_tt-content.csv');
        $this->setUpFrontendRootPage(
            self::ROOT_PAGE_ID,
            [
                'EXT:content_blocks/Tests/Functional/Frontend/Fixtures/frontend.typoscript',
            ]
        );
        $response = $this->executeFrontendSubRequest((new InternalRequest())->withPageId(self::ROOT_PAGE_ID));
        $html = (string)$response->getBody();

        self::assertStringContainsString('Child typeName: text', $html);
        self::assertStringContainsString('child header', $html);
        self::assertStringNotContainsString('has no rendering definition!', $html);
    }

    #[Test]
    public function mixedRelationIsResolvedForEachTable(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/mixed-relation.csv');
        $this->setUpFrontendRootPage(
            self::ROOT_PAGE_ID,
            [
                'EXT:content_blocks/Tests/Functional/Frontend/Fixtures/frontend.typoscript',
            ]
        );
        $response = $this->executeFrontendSubRequest((new InternalRequest())->withPageId(self::ROOT_PAGE_ID));
        $html = (string)$response->getBody();

        self::assertStringContainsString('Mixed Relation: pages', $html);
        self::assertStringContainsString('Mixed Relation: tt_content', $html);
    }

    #[Test]
    public function staticSelectRelationsResolved(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/select-static-relation.csv');
        $this->setUpFrontendRootPage(
            self::ROOT_PAGE_ID,
            [
                'EXT:content_blocks/Tests/Functional/Frontend/Fixtures/frontend.typoscript',
            ]
        );
        $response = $this->executeFrontendSubRequest((new InternalRequest())->withPageId(self::ROOT_PAGE_ID));
        $html = (string)$response->getBody();

        self::assertStringContainsString('Multi Select: one', $html);
        self::assertStringContainsString('Multi Select: two', $html);
        self::assertStringContainsString('Single Select: three', $html);
    }

    #[Test]
    public function circularRelationsResolved(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/circular_relation.csv');
        $this->setUpFrontendRootPage(
            self::ROOT_PAGE_ID,
            [
                'EXT:content_blocks/Tests/Functional/Frontend/Fixtures/frontend.typoscript',
            ]
        );
        $response = $this->executeFrontendSubRequest((new InternalRequest())->withPageId(self::ROOT_PAGE_ID));
        $html = (string)$response->getBody();

        self::assertStringContainsString('Circular relation uid: 1', $html);
        self::assertStringContainsString('Circular relation from _grids uid: 1', $html);
        self::assertStringContainsString('Circular select uid: 1', $html);
    }

    #[Test]
    public function categoryRelationResolved(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/categories.csv');
        $this->setUpFrontendRootPage(
            self::ROOT_PAGE_ID,
            [
                'EXT:content_blocks/Tests/Functional/Frontend/Fixtures/frontend.typoscript',
            ]
        );
        $response = $this->executeFrontendSubRequest((new InternalRequest())->withPageId(self::ROOT_PAGE_ID));
        $html = (string)$response->getBody();

        self::assertStringContainsString('Category table: sys_category', $html);
        self::assertStringContainsString('Category 1', $html);
        self::assertStringContainsString('Category 2', $html);
    }

    #[Test]
    public function passFieldIsResolved(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DataSet/pass.csv');
        $this->setUpFrontendRootPage(
            self::ROOT_PAGE_ID,
            [
                'EXT:content_blocks/Tests/Functional/Frontend/Fixtures/frontend.typoscript',
            ]
        );
        $response = $this->executeFrontendSubRequest((new InternalRequest())->withPageId(self::ROOT_PAGE_ID));
        $html = (string)$response->getBody();

        self::assertStringContainsString('pass: MyPassValue', $html);
    }

    #[Test]
    public function corePagesTypeIsResolved(): void
    {
        $this->setUpFrontendRootPage(
            self::ROOT_PAGE_ID,
            [
                'EXT:content_blocks/Tests/Functional/Frontend/Fixtures/frontend.typoscript',
            ]
        );
        $response = $this->executeFrontendSubRequest((new InternalRequest())->withPageId(self::ROOT_PAGE_ID));
        $html = (string)$response->getBody();

        self::assertStringContainsString('Page Title: ContentBlockFrontendTest', $html);
    }

    #[Test]
    public function customPageTypeIsResolved(): void
    {
        $this->setUpFrontendRootPage(
            self::ROOT_PAGE_ID,
            [
                'EXT:content_blocks/Tests/Functional/Frontend/Fixtures/frontend.typoscript',
            ]
        );
        $response = $this->executeFrontendSubRequest((new InternalRequest())->withPageId(999));
        $html = (string)$response->getBody();

        self::assertStringContainsString('Page Title: BlogPage', $html);
        self::assertStringContainsString('Blog Additional field: Text from additional field', $html);
    }
}
