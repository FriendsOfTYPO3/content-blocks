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

namespace TYPO3\CMS\ContentBlocks\Tests\Unit\Registry;

use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\ContentBlocks\FieldType\BaseFieldTypeRegistryFactory;
use TYPO3\CMS\ContentBlocks\Loader\LoadedContentBlock;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\ContentBlocks\Schema\FieldTypeResolver;
use TYPO3\CMS\ContentBlocks\Schema\SimpleTcaSchemaFactory;
use TYPO3\CMS\ContentBlocks\Tests\Unit\Fixtures\FieldTypeRegistryTestFactory;
use TYPO3\CMS\Core\Cache\Frontend\NullFrontend;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class ContentBlockRegistryTest extends UnitTestCase
{
    #[Test]
    public function duplicateContentBlockThrowsException(): void
    {
        $loadedContentBlockA = LoadedContentBlock::fromArray([
            'name' => 'example/a',
            'yaml' => [
                'table' => 'tt_content',
                'typeName' => 'example_a',
            ],
        ]);
        $loadedContentBlockB = LoadedContentBlock::fromArray([
            'name' => 'example/a',
            'yaml' => [
                'table' => 'tt_content',
                'typeName' => 'example_a',
            ],
        ]);

        $fieldTypeRegistry = FieldTypeRegistryTestFactory::create();
        $baseFieldTypeRegistry = new BaseFieldTypeRegistryFactory($fieldTypeRegistry);
        $fieldTypeResolver = new FieldTypeResolver($baseFieldTypeRegistry->create());
        $packageManager = $this->createMock(PackageManager::class);
        $packageManager->method('getActivePackages')->willReturn([]);
        $simpleTcaSchemaFactory = new SimpleTcaSchemaFactory(new NullFrontend('test'), $fieldTypeResolver, $packageManager);
        $contentBlockRegistry = new ContentBlockRegistry($simpleTcaSchemaFactory);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1678474766);

        $contentBlockRegistry->register($loadedContentBlockA);
        $contentBlockRegistry->register($loadedContentBlockB);
    }

    #[Test]
    public function duplicateTypeNameForContentElementThrowsException(): void
    {
        $loadedContentBlockA = LoadedContentBlock::fromArray([
            'name' => 'example/a',
            'yaml' => [
                'typeField' => 'CType',
                'typeName' => 'example',
                'table' => 'tt_content',
            ],
        ]);
        $loadedContentBlockB = LoadedContentBlock::fromArray([
            'name' => 'example/b',
            'yaml' => [
                'typeField' => 'CType',
                'typeName' => 'example',
                'table' => 'tt_content',
            ],
        ]);

        $fieldTypeRegistry = FieldTypeRegistryTestFactory::create();
        $baseFieldTypeRegistry = new BaseFieldTypeRegistryFactory($fieldTypeRegistry);
        $fieldTypeResolver = new FieldTypeResolver($baseFieldTypeRegistry->create());
        $packageManager = $this->createMock(PackageManager::class);
        $packageManager->method('getActivePackages')->willReturn([]);
        $simpleTcaSchemaFactory = new SimpleTcaSchemaFactory(new NullFrontend('test'), $fieldTypeResolver, $packageManager);
        $contentBlockRegistry = new ContentBlockRegistry($simpleTcaSchemaFactory);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1701351270);

        $contentBlockRegistry->register($loadedContentBlockA);
        $contentBlockRegistry->register($loadedContentBlockB);
    }

    #[Test]
    public function duplicateTypeNameForPageTypeThrowsException(): void
    {
        $loadedContentBlockA = LoadedContentBlock::fromArray([
            'name' => 'example/a',
            'yaml' => [
                'typeField' => 'doktype',
                'typeName' => '123',
                'table' => 'pages',
            ],
        ]);
        $loadedContentBlockB = LoadedContentBlock::fromArray([
            'name' => 'example/b',
            'yaml' => [
                'typeField' => 'doktype',
                'typeName' => '123',
                'table' => 'pages',
            ],
        ]);

        $fieldTypeRegistry = FieldTypeRegistryTestFactory::create();
        $baseFieldTypeRegistry = new BaseFieldTypeRegistryFactory($fieldTypeRegistry);
        $fieldTypeResolver = new FieldTypeResolver($baseFieldTypeRegistry->create());
        $packageManager = $this->createMock(PackageManager::class);
        $packageManager->method('getActivePackages')->willReturn([]);
        $simpleTcaSchemaFactory = new SimpleTcaSchemaFactory(new NullFrontend('test'), $fieldTypeResolver, $packageManager);
        $contentBlockRegistry = new ContentBlockRegistry($simpleTcaSchemaFactory);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1701351270);

        $contentBlockRegistry->register($loadedContentBlockA);
        $contentBlockRegistry->register($loadedContentBlockB);
    }

    #[Test]
    public function duplicateTypeNameForRecordTypeWithSameTableThrowsException(): void
    {
        $loadedContentBlockA = LoadedContentBlock::fromArray([
            'name' => 'example/a',
            'yaml' => [
                'typeName' => '123',
                'table' => 'my_record',
            ],
        ]);
        $loadedContentBlockB = LoadedContentBlock::fromArray([
            'name' => 'example/b',
            'yaml' => [
                'typeName' => '123',
                'table' => 'my_record',
            ],
        ]);

        $fieldTypeRegistry = FieldTypeRegistryTestFactory::create();
        $baseFieldTypeRegistry = new BaseFieldTypeRegistryFactory($fieldTypeRegistry);
        $fieldTypeResolver = new FieldTypeResolver($baseFieldTypeRegistry->create());
        $packageManager = $this->createMock(PackageManager::class);
        $packageManager->method('getActivePackages')->willReturn([]);
        $simpleTcaSchemaFactory = new SimpleTcaSchemaFactory(new NullFrontend('test'), $fieldTypeResolver, $packageManager);
        $contentBlockRegistry = new ContentBlockRegistry($simpleTcaSchemaFactory);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1701351270);

        $contentBlockRegistry->register($loadedContentBlockA);
        $contentBlockRegistry->register($loadedContentBlockB);
    }

    #[Test]
    public function duplicateTypeNameForRecordTypeDifferentTableIsAllowed(): void
    {
        $loadedContentBlockA = LoadedContentBlock::fromArray([
            'name' => 'example/a',
            'yaml' => [
                'typeName' => '123',
                'table' => 'my_record',
            ],
        ]);
        $loadedContentBlockB = LoadedContentBlock::fromArray([
            'name' => 'example/b',
            'yaml' => [
                'typeName' => '123',
                'table' => 'my_other_record',
            ],
        ]);

        $fieldTypeRegistry = FieldTypeRegistryTestFactory::create();
        $baseFieldTypeRegistry = new BaseFieldTypeRegistryFactory($fieldTypeRegistry);
        $fieldTypeResolver = new FieldTypeResolver($baseFieldTypeRegistry->create());
        $packageManager = $this->createMock(PackageManager::class);
        $packageManager->method('getActivePackages')->willReturn([]);
        $simpleTcaSchemaFactory = new SimpleTcaSchemaFactory(new NullFrontend('test'), $fieldTypeResolver, $packageManager);
        $contentBlockRegistry = new ContentBlockRegistry($simpleTcaSchemaFactory);

        $contentBlockRegistry->register($loadedContentBlockA);
        $contentBlockRegistry->register($loadedContentBlockB);
    }
}
