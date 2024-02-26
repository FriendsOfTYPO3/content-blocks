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

namespace TYPO3\CMS\ContentBlocks;

use Psr\Container\ContainerInterface;
use TYPO3\CMS\Backend\View\Event\ModifyDatabaseQueryForContentEvent;
use TYPO3\CMS\ContentBlocks\Cache\InitializeContentBlockCache;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentElementDefinition;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\PageTypeDefinition;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\ContentBlocks\Registry\LanguageFileRegistry;
use TYPO3\CMS\ContentBlocks\UserFunction\ContentWhere;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\Core\Cache\Event\CacheWarmupEvent;
use TYPO3\CMS\Core\Core\Event\BootCompletedEvent;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\DataHandling\PageDoktypeRegistry;
use TYPO3\CMS\Core\EventDispatcher\ListenerProvider;
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Package\AbstractServiceProvider;
use TYPO3\CMS\Core\TypoScript\IncludeTree\Event\ModifyLoadedPageTsConfigEvent;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * @internal
 */
class ServiceProvider extends AbstractServiceProvider
{
    protected static function getPackagePath(): string
    {
        return __DIR__ . '/../';
    }

    protected static function getPackageName(): string
    {
        return 'typo3/cms-content-blocks';
    }

    public function getFactories(): array
    {
        return [
            ContentWhere::class => static::getContentWhere(...),
            'content-blocks.icons' => static::getContentBlockIcons(...),
            'content-blocks.page-types' => static::getContentBlockPageTypes(...),
            'content-blocks.typoscript' => static::getContentBlockTypoScript(...),
            'content-blocks.user-tsconfig' => static::getContentBlockUserTsConfig(...),
            'content-blocks.page-tsconfig' => static::getContentBlockPageTsConfig(...),
            'content-blocks.parent-field-names' => static::getContentBlockParentFieldNames(...),
            'content-blocks.warmer' => static::getContentBlocksWarmer(...),
            'content-blocks.add-typoscript' => static::addTypoScript(...),
            'content-blocks.add-user-tsconfig' => static::addUserTsConfig(...),
            'content-blocks.add-page-tsconfig' => static::addPageTsConfig(...),
            'content-blocks.add-icons' => static::configureIconRegistry(...),
            'content-blocks.hide-content-element-children' => static::hideContentElementChildren(...),
        ];
    }

    public function getExtensions(): array
    {
        return [
            PageDoktypeRegistry::class => static::configurePageTypes(...),
            ListenerProvider::class => static::addEventListeners(...),
        ] + parent::getExtensions();
    }

    public static function getContentWhere(ContainerInterface $container): ContentWhere
    {
        $arrayObject = $container->get('content-blocks.parent-field-names');
        $parentFieldNames = $arrayObject->getArrayCopy();
        return self::new(
            $container,
            ContentWhere::class,
            [
                $parentFieldNames,
            ]
        );
    }

    public static function getContentBlockIcons(ContainerInterface $container): \ArrayObject
    {
        $arrayObject = new \ArrayObject();
        $cache = $container->get('cache.content_blocks_code');
        $iconsFromPackages = $cache->require('icons');
        if ($iconsFromPackages !== false) {
            $arrayObject->exchangeArray($iconsFromPackages);
            return $arrayObject;
        }
        $tableDefinitionCollection = $container->get(TableDefinitionCollection::class);
        foreach ($tableDefinitionCollection as $tableDefinition) {
            foreach ($tableDefinition->getContentTypeDefinitionCollection() ?? [] as $typeDefinition) {
                $iconConfig = [
                    $typeDefinition->getTypeIconIdentifier() => [
                        'source' => $typeDefinition->getTypeIconPath(),
                        'provider' => $typeDefinition->getIconProviderClassName(),
                    ],
                ];
                $arrayObject->exchangeArray(array_merge($arrayObject->getArrayCopy(), $iconConfig));
            }
        }
        $cache->set('icons', 'return ' . var_export($arrayObject->getArrayCopy(), true) . ';');
        return $arrayObject;
    }

    public static function getContentBlockPageTypes(ContainerInterface $container): \ArrayObject
    {
        $arrayObject = new \ArrayObject();
        $tableDefinitionCollection = $container->get(TableDefinitionCollection::class);
        if (!$tableDefinitionCollection->hasTable(ContentType::PAGE_TYPE->getTable())) {
            return $arrayObject;
        }
        $tableDefinition = $tableDefinitionCollection->getTable(ContentType::PAGE_TYPE->getTable());
        foreach ($tableDefinition->getContentTypeDefinitionCollection() ?? [] as $typeDefinition) {
            $arrayObject->append($typeDefinition->getTypeName());
        }
        return $arrayObject;
    }

    public static function getContentBlockTypoScript(ContainerInterface $container): \ArrayObject
    {
        $arrayObject = new \ArrayObject();
        $cache = $container->get('cache.content_blocks_code');
        $typoScriptFromCache = $cache->require('typoscript');
        if ($typoScriptFromCache !== false) {
            $arrayObject->exchangeArray($typoScriptFromCache);
            return $arrayObject;
        }

        $contentBlockRegistry = $container->get(ContentBlockRegistry::class);
        $tableDefinitionCollection = $container->get(TableDefinitionCollection::class);
        foreach ($tableDefinitionCollection as $tableDefinition) {
            foreach ($tableDefinition->getContentTypeDefinitionCollection() ?? [] as $typeDefinition) {
                if ($tableDefinition->getContentType() === ContentType::CONTENT_ELEMENT) {
                    $extPath = $contentBlockRegistry->getContentBlockExtPath($typeDefinition->getName());
                    $privatePath = $extPath . '/' . ContentBlockPathUtility::getPrivateFolder();
                    $template = ContentBlockPathUtility::getFrontendTemplateFileNameWithoutExtension();
                    $typoScript = <<<HEREDOC
tt_content.{$typeDefinition->getTypeName()} =< lib.contentBlock
tt_content.{$typeDefinition->getTypeName()} {
    templateName = {$template}
    templateRootPaths {
        20 = $privatePath/
    }
    partialRootPaths {
        20 = $privatePath/Partials/
    }
    layoutRootPaths {
        20 = $privatePath/Layouts/
    }
}
HEREDOC;
                    $arrayObject->append($typoScript);
                }
            }
        }
        $cache->set('typoscript', 'return ' . var_export($arrayObject->getArrayCopy(), true) . ';');
        return $arrayObject;
    }

    public static function getContentBlockUserTsConfig(ContainerInterface $container): \ArrayObject
    {
        $arrayObject = new \ArrayObject();
        $cache = $container->get('cache.content_blocks_code');
        $typoScriptFromCache = $cache->require('user-tsconfig');
        if ($typoScriptFromCache !== false) {
            $arrayObject->exchangeArray($typoScriptFromCache);
            return $arrayObject;
        }

        $tableDefinitionCollection = $container->get(TableDefinitionCollection::class);
        foreach ($tableDefinitionCollection as $tableDefinition) {
            foreach ($tableDefinition->getContentTypeDefinitionCollection() ?? [] as $typeDefinition) {
                if ($typeDefinition instanceof PageTypeDefinition) {
                    $options = 'options.pageTree.doktypesToShowInNewPageDragArea := addToList(' . $typeDefinition->getTypeName() . ')';
                    $arrayObject->append($options);
                }
            }
        }

        $cache->set('user-tsconfig', 'return ' . var_export($arrayObject->getArrayCopy(), true) . ';');
        return $arrayObject;
    }

    public static function getContentBlockPageTsConfig(ContainerInterface $container): \ArrayObject
    {
        $arrayObject = new \ArrayObject();
        $cache = $container->get('cache.content_blocks_code');
        $typoScriptFromCache = $cache->require('page-tsconfig');
        if ($typoScriptFromCache !== false) {
            $arrayObject->exchangeArray($typoScriptFromCache);
            return $arrayObject;
        }

        $languageFileRegistry = $container->get(LanguageFileRegistry::class);
        $tableDefinitionCollection = $container->get(TableDefinitionCollection::class);
        foreach ($tableDefinitionCollection as $tableDefinition) {
            foreach ($tableDefinition->getContentTypeDefinitionCollection() ?? [] as $typeDefinition) {
                if ($typeDefinition instanceof ContentElementDefinition) {
                    $languagePathTitle = $typeDefinition->getLanguagePathTitle();
                    if ($languageFileRegistry->isset($typeDefinition->getName(), $languagePathTitle)) {
                        $title = $languagePathTitle;
                    } else {
                        $title = $typeDefinition->getTitle();
                    }
                    $languagePathDescription = $typeDefinition->getLanguagePathDescription();
                    if ($languageFileRegistry->isset($typeDefinition->getName(), $languagePathDescription)) {
                        $description = $languagePathDescription;
                    } else {
                        $description = $typeDefinition->getDescription();
                    }
                    $group = $typeDefinition->getGroup();
                    $typeName = $typeDefinition->getTypeName();
                    $iconIdentifier = $typeDefinition->getTypeIconIdentifier();
                    $saveAndClose = $typeDefinition->hasSaveAndClose() ? '1' : '0';
                    $pageTsConfig = <<<HEREDOC
mod.wizards.newContentElement.wizardItems.$group {
    elements {
        $typeName {
            iconIdentifier = $iconIdentifier
            title = $title
            description = $description
            saveAndClose = $saveAndClose
            tt_content_defValues {
                CType = $typeName
            }
        }
    }
    show := addToList($typeName)
}
HEREDOC;
                    $arrayObject->append($pageTsConfig);
                }
            }
        }

        $cache->set('page-tsconfig', 'return ' . var_export($arrayObject->getArrayCopy(), true) . ';');
        return $arrayObject;
    }

    public static function getContentBlockParentFieldNames(ContainerInterface $container): \ArrayObject
    {
        $arrayObject = new \ArrayObject();
        $cache = $container->get('cache.content_blocks_code');
        $typoScriptFromCache = $cache->require('parent-field-names');
        if ($typoScriptFromCache !== false) {
            $arrayObject->exchangeArray($typoScriptFromCache);
            return $arrayObject;
        }

        $tableDefinitionCollection = $container->get(TableDefinitionCollection::class);
        $contentElementTable = ContentType::CONTENT_ELEMENT->getTable();
        if ($tableDefinitionCollection->hasTable($contentElementTable)) {
            $fieldNames = [];
            $contentElementTableDefinition = $tableDefinitionCollection->getTable($contentElementTable);
            foreach ($contentElementTableDefinition->getParentReferences() ?? [] as $parentReference) {
                $fieldConfiguration = $parentReference->getFieldConfiguration()->getTca()['config'] ?? [];
                if (($fieldConfiguration['foreign_table'] ?? '') === $contentElementTable) {
                    $foreignField = $fieldConfiguration['foreign_field'];
                    $fieldNames[$foreignField] = $foreignField;
                }
            }
            $arrayObject->exchangeArray(array_values($fieldNames));
        }
        $cache->set('parent-field-names', 'return ' . var_export($arrayObject->getArrayCopy(), true) . ';');
        return $arrayObject;
    }

    /**
     * Usually it shouldn't be necessary to explicitly require warmup of Content Block caches here,
     * as one of the code generators will trigger the generation. This is merely kept as fallback
     * in case the events vanish for some reason.
     */
    public static function getContentBlocksWarmer(ContainerInterface $container): \Closure
    {
        return static function (CacheWarmupEvent $event) use ($container) {
            if ($event->hasGroup('system')) {
                // Create caches
                $container->get(TableDefinitionCollection::class);
            }
        };
    }

    public static function addTypoScript(ContainerInterface $container): \Closure
    {
        return static function (BootCompletedEvent $event) use ($container) {
            $arrayObject = $container->get('content-blocks.typoscript');
            $concatenatedTypoScript = implode(LF, $arrayObject->getArrayCopy());
            ExtensionManagementUtility::addTypoScriptSetup($concatenatedTypoScript);
        };
    }

    public static function addUserTsConfig(ContainerInterface $container): \Closure
    {
        return static function (BootCompletedEvent $event) use ($container) {
            $arrayObject = $container->get('content-blocks.user-tsconfig');
            $concatenatedTypoScript = implode(LF, $arrayObject->getArrayCopy());
            ExtensionManagementUtility::addUserTSConfig($concatenatedTypoScript);
        };
    }

    public static function addPageTsConfig(ContainerInterface $container): \Closure
    {
        return static function (ModifyLoadedPageTsConfigEvent $event) use ($container) {
            $arrayObject = $container->get('content-blocks.page-tsconfig');
            $concatenatedTypoScript = implode(LF, $arrayObject->getArrayCopy());
            $event->addTsConfig($concatenatedTypoScript);
        };
    }

    public static function hideContentElementChildren(ContainerInterface $container): \Closure
    {
        return static function (ModifyDatabaseQueryForContentEvent $event) use ($container) {
            $arrayObject = $container->get('content-blocks.parent-field-names');
            $parentFieldNames = $arrayObject->getArrayCopy();
            $queryBuilder = $event->getQueryBuilder();
            foreach ($parentFieldNames as $fieldName) {
                $queryBuilder->andWhere(
                    $queryBuilder->expr()->eq(
                        $fieldName,
                        $queryBuilder->createNamedParameter(0, Connection::PARAM_INT)
                    )
                );
            }
            $event->setQueryBuilder($queryBuilder);
        };
    }

    public static function configureIconRegistry(ContainerInterface $container): \Closure
    {
        return static function (BootCompletedEvent $event) use ($container) {
            $iconRegistry = $container->get(IconRegistry::class);
            $iconsFromPackages = $container->get('content-blocks.icons');
            foreach ($iconsFromPackages as $icon => $options) {
                $provider = $options['provider'] ?? null;
                unset($options['provider']);
                $options ??= [];
                if ($provider === null && ($options['source'] ?? false)) {
                    $provider = $iconRegistry->detectIconProvider($options['source']);
                }
                if ($provider === null) {
                    continue;
                }
                $iconRegistry->registerIcon($icon, $provider, $options);
            }
            return $iconRegistry;
        };
    }

    public static function configurePageTypes(ContainerInterface $container, PageDoktypeRegistry $pageDoktypeRegistry): PageDoktypeRegistry
    {
        // Early core cache is required here, as PageDokTypeRegistry is instantiated in ExtensionManagementUtility::loadBaseTca
        $cache = $container->get('cache.core');
        $pageTypesFromContentBlocks = $cache->require('page-types');
        if ($pageTypesFromContentBlocks === false) {
            $pageTypesFromContentBlocks = $container->get('content-blocks.page-types')->getArrayCopy();
            $cache->set('page-types', 'return ' . var_export($pageTypesFromContentBlocks, true) . ';');
        }
        foreach ($pageTypesFromContentBlocks as $pageType) {
            $pageDoktypeRegistry->add($pageType, []);
        }
        return $pageDoktypeRegistry;
    }

    public static function addEventListeners(ContainerInterface $container, ListenerProvider $listenerProvider): ListenerProvider
    {
        $listenerProvider->addListener(CacheWarmupEvent::class, 'content-blocks.warmer');
        $listenerProvider->addListener(BootCompletedEvent::class, InitializeContentBlockCache::class);
        $listenerProvider->addListener(BootCompletedEvent::class, 'content-blocks.add-typoscript');
        // @todo Use BeforeLoadedUserTsConfigEvent in v13
        $listenerProvider->addListener(BootCompletedEvent::class, 'content-blocks.add-user-tsconfig');
        // @todo Use BeforeLoadedPageTsConfigEvent in v13
        $listenerProvider->addListener(ModifyLoadedPageTsConfigEvent::class, 'content-blocks.add-page-tsconfig');
        $listenerProvider->addListener(BootCompletedEvent::class, 'content-blocks.add-icons');
        $listenerProvider->addListener(ModifyDatabaseQueryForContentEvent::class, 'content-blocks.hide-content-element-children');
        return $listenerProvider;
    }
}
