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

namespace TYPO3\CMS\ContentBlocks\Tests\Unit\Domain\Repository;

use TYPO3\CMS\ContentBlocks\Definition\TableDefinition;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\ContentBlocks\Domain\Model\ContentBlockConfiguration;
use TYPO3\CMS\ContentBlocks\Domain\Repository\ContentBlockBuilder;
use TYPO3\CMS\ContentBlocks\Factory\ContentBlockConfigurationFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class ContentBlockConfigurationRepositoryTest extends UnitTestCase
{
    public function checkContentBlockConfigurationRepositoryDataProvider(): iterable
    {
        yield 'Check create ContentBlock methode.' => [
            'contentBlock' => [
                'translations' => [
                    'default' => '<?xml version="1.0"?>' . "\n",
                    'de' => '<?xml version="1.0"?>' . "\n" . '<xliff version="1.0">' . "\n",
                ],
                'composerJson' => [
                    "name" => "typo3-contentblocks/example-local",
                    "description" => "Content block providing examples for all field types.",
                    "type" => "typo3-contentblock",
                ],
                'yaml' => [
                    'group' => 'common',
                    'fields' =>
                        [
                            0 =>
                                [
                                    'identifier' => 'text',
                                    'type' => 'Text',
                                    'languagePath' => 'test-path-for-text.xlf:test',
                                    'properties' =>
                                        [
                                            'autocomplete' => true,
                                            'default' => 'Default value',
                                            'max' => 15,
                                            'placeholder' => 'Placeholder text',
                                            'size' => 20,
                                            'required' => false,
                                            'trim' => true,
                                        ],
                                    '_path' =>
                                        [
                                            0 => 'text',
                                        ],
                                    '_identifier' => 'text',
                                ],
                            1 =>
                                [
                                    'identifier' => 'textarea',
                                    'type' => 'Textarea',
                                    'languagePath' => 'test-path-for-textarea.xlf:test',
                                    'properties' =>
                                        [
                                            'cols' => 40,
                                            'default' => 'Default value',
                                            'enableRichtext' => true,
                                            'max' => 150,
                                            'placeholder' => 'Placeholder text',
                                            'richtextConfiguration' => 'default',
                                            'rows' => 15,
                                            'required' => false,
                                            'trim' => true,
                                        ],
                                    '_path' =>
                                        [
                                            0 => 'textarea',
                                        ],
                                    '_identifier' => 'textarea',
                                ],
                            2 =>
                                [
                                    'identifier' => 'email',
                                    'type' => 'Email',
                                    'languagePath' => 'test-path-for-email.xlf:test',
                                    'properties' =>
                                        [
                                            'autocomplete' => true,
                                            'default' => 'developer@localhost',
                                            'placeholder' => 'Placeholder text',
                                            'size' => 20,
                                            'required' => true,
                                            'trim' => true,
                                        ],
                                    '_path' =>
                                        [
                                            0 => 'email',
                                        ],
                                    '_identifier' => 'email',
                                ],
                        ],
                ],
            ],
            'expected' => [],
        ];
    }

    /**
     * ContentBlockConfiguration Test
     *
     * @test
     * @dataProvider checkContentBlockConfigurationRepositoryDataProvider
     */
    public function checkContentBlockConfigurationRepository(array $contentBlock, array $expected)
    {
        $this->resetSingletonInstances = true;

        /** @var ContentBlockBuilder */
        $contentBlockConfigurationRepository = GeneralUtility::makeInstance(ContentBlockBuilder::class);

        /** @var ContentBlockConfigurationFactory $factory */
        $factory = new ContentBlockConfigurationFactory();

        /** @param ContentBlockConfiguration */
        $contentBlockConf = $factory->createFromArray($contentBlock);

        // $contentBlockConfigurationRepository->create($contentBlockConf);
        // self::assertSame($expected['create'], $contentBlockConfiguration->toArray());
    }

    /**
     * check findAllMethod
     * @test
     */
    public function checkContentBlockConfigurationRepositoryFindAll()
    {
        $this->resetSingletonInstances = true;

        /** @var ContentBlockBuilder $contentBlockConfigurationRepository */
        $contentBlockConfigurationRepository = GeneralUtility::makeInstance(ContentBlockBuilder::class);

        /** @var TableDefinitionCollection $contentBlocksList */
        $contentBlocksList = $contentBlockConfigurationRepository->findAll();

        self::assertSame(true, $contentBlocksList instanceof TableDefinitionCollection);
        self::assertSame(true, $contentBlocksList->getTable('tt_content') instanceof TableDefinition);
    }
}
