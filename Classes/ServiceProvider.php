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
use TYPO3\CMS\ContentBlocks\Backend\Layout\HideContentElementChildrenEventListener;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentElementDefinition;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\PageTypeDefinition;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\ContentBlocks\Generator\PageTsConfigGenerator;
use TYPO3\CMS\ContentBlocks\Generator\TypoScriptGenerator;
use TYPO3\CMS\ContentBlocks\Generator\UserTsConfigGenerator;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\ContentBlocks\Registry\LanguageFileRegistry;
use TYPO3\CMS\ContentBlocks\UserFunction\ContentWhere;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\Core\Cache\Event\CacheWarmupEvent;
use TYPO3\CMS\Core\DataHandling\PageDoktypeRegistry;
use TYPO3\CMS\Core\EventDispatcher\ListenerProvider;
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Package\AbstractServiceProvider;

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
            'content-block-icons' => static::getContentBlockIcons(...),
            'content-block-page-types' => static::getContentBlockPageTypes(...),
            'content-block-typoscript' => static::getContentBlockTypoScript(...),
            'content-block-user-tsconfig' => static::getContentBlockUserTsConfig(...),
            'content-block-page-tsconfig' => static::getContentBlockPageTsConfig(...),
            'content-block-parentFieldNames' => static::getContentBlockParentFieldNames(...),
            'content-blocks.warmer' => static::getContentBlocksWarmer(...),
            TypoScriptGenerator::class => static::getTypoScriptGenerator(...),
            UserTsConfigGenerator::class => static::getUserTsConfigGenerator(...),
            PageTsConfigGenerator::class => static::getPageTsConfigGenerator(...),
            HideContentElementChildrenEventListener::class => static::getHideContentElementChildrenEventListener(...),
            ContentWhere::class => static::getContentWhere(...),
        ];
    }

    public function getExtensions(): array
    {
        return [
            IconRegistry::class => static::configureIconRegistry(...),
            PageDoktypeRegistry::class => static::configurePageTypes(...),
            ListenerProvider::class => static::addEventListeners(...),
        ] + parent::getExtensions();
    }

    public static function getTypoScriptGenerator(ContainerInterface $container): TypoScriptGenerator
    {
        $arrayObject = $container->get('content-block-typoscript');
        $concatenatedTypoScript = implode(LF, $arrayObject->getArrayCopy());
        return self::new(
            $container,
            TypoScriptGenerator::class,
            [
                $concatenatedTypoScript,
            ]
        );
    }

    public static function getUserTsConfigGenerator(ContainerInterface $container): UserTsConfigGenerator
    {
        $arrayObject = $container->get('content-block-user-tsconfig');
        $concatenatedTypoScript = implode(LF, $arrayObject->getArrayCopy());
        return self::new(
            $container,
            UserTsConfigGenerator::class,
            [
                $concatenatedTypoScript,
            ]
        );
    }

    public static function getPageTsConfigGenerator(ContainerInterface $container): PageTsConfigGenerator
    {
        $arrayObject = $container->get('content-block-page-tsconfig');
        $concatenatedTypoScript = implode(LF, $arrayObject->getArrayCopy());
        return self::new(
            $container,
            PageTsConfigGenerator::class,
            [
                $concatenatedTypoScript,
            ]
        );
    }

    public static function getHideContentElementChildrenEventListener(ContainerInterface $container): HideContentElementChildrenEventListener
    {
        $arrayObject = $container->get('content-block-parentFieldNames');
        $parentFieldNames = $arrayObject->getArrayCopy();
        return self::new(
            $container,
            HideContentElementChildrenEventListener::class,
            [
                $parentFieldNames,
            ]
        );
    }

    public static function getContentWhere(ContainerInterface $container): ContentWhere
    {
        $arrayObject = $container->get('content-block-parentFieldNames');
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
        $typoScriptFromCache = $cache->require('TypoScript_ContentBlocks');
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
        $cache->set('TypoScript_ContentBlocks', 'return ' . var_export($arrayObject->getArrayCopy(), true) . ';');
        return $arrayObject;
    }

    public static function getContentBlockUserTsConfig(ContainerInterface $container): \ArrayObject
    {
        $arrayObject = new \ArrayObject();
        $cache = $container->get('cache.content_blocks_code');
        $typoScriptFromCache = $cache->require('UserTsConfig_ContentBlocks');
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

        $cache->set('UserTsConfig_ContentBlocks', 'return ' . var_export($arrayObject->getArrayCopy(), true) . ';');
        return $arrayObject;
    }

    public static function getContentBlockPageTsConfig(ContainerInterface $container): \ArrayObject
    {
        $arrayObject = new \ArrayObject();
        $cache = $container->get('cache.content_blocks_code');
        $typoScriptFromCache = $cache->require('PageTsConfig_ContentBlocks');
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

        $cache->set('PageTsConfig_ContentBlocks', 'return ' . var_export($arrayObject->getArrayCopy(), true) . ';');
        return $arrayObject;
    }

    public static function getContentBlockParentFieldNames(ContainerInterface $container): \ArrayObject
    {
        $arrayObject = new \ArrayObject();
        $cache = $container->get('cache.content_blocks_code');
        $typoScriptFromCache = $cache->require('ParentFieldNames_ContentBlocks');
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
        $cache->set('ParentFieldNames_ContentBlocks', 'return ' . var_export($arrayObject->getArrayCopy(), true) . ';');
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

    public static function configureIconRegistry(ContainerInterface $container, IconRegistry $iconRegistry): IconRegistry
    {
        $cache = $container->get('cache.content_blocks_code');

        $iconsFromPackages = $cache->require('Icons_ContentBlocks');
        if ($iconsFromPackages === false) {
            $iconsFromPackages = $container->get('content-block-icons')->getArrayCopy();
            $cache->set('Icons_ContentBlocks', 'return ' . var_export($iconsFromPackages, true) . ';');
        }

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
    }

    public static function configurePageTypes(ContainerInterface $container, PageDoktypeRegistry $pageDoktypeRegistry): PageDoktypeRegistry
    {
        // Early core cache is required here, as PageDokTypeRegistry is instantiated inExtensionManagementUtility::loadBaseTca
        $cache = $container->get('cache.core');
        $pageTypesFromContentBlocks = $cache->require('PageTypes_ContentBlocks');
        if ($pageTypesFromContentBlocks === false) {
            $pageTypesFromContentBlocks = $container->get('content-block-page-types')->getArrayCopy();
            $cache->set('PageTypes_ContentBlocks', 'return ' . var_export($pageTypesFromContentBlocks, true) . ';');
        }
        foreach ($pageTypesFromContentBlocks as $pageType) {
            $pageDoktypeRegistry->add($pageType, []);
        }
        return $pageDoktypeRegistry;
    }

    public static function addEventListeners(ContainerInterface $container, ListenerProvider $listenerProvider): ListenerProvider
    {
        $listenerProvider->addListener(CacheWarmupEvent::class, 'content-blocks.warmer');

        return $listenerProvider;
    }
}
