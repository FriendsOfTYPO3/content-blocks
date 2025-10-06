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

use Doctrine\DBAL\ArrayParameterType;
use Psr\Container\ContainerInterface;
use TYPO3\CMS\Backend\Controller\Event\AfterRecordSummaryForLocalizationEvent;
use TYPO3\CMS\Backend\View\Event\ModifyDatabaseQueryForContentEvent;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentTypeInterface;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\PageTypeDefinition;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\ContentBlocks\Generator\TcaGenerator;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\ContentBlocks\UserFunction\ContentWhere;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\Core\Configuration\Event\BeforeTcaOverridesEvent;
use TYPO3\CMS\Core\Core\Event\BootCompletedEvent;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\DataHandling\PageDoktypeRegistry;
use TYPO3\CMS\Core\EventDispatcher\ListenerProvider;
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Package\AbstractServiceProvider;
use TYPO3\CMS\Core\TypoScript\IncludeTree\Event\BeforeLoadedUserTsConfigEvent;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Event\AfterContentHasBeenFetchedEvent;

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
        return 'friendsoftypo3/content-blocks';
    }

    public function getFactories(): array
    {
        return [
            ContentWhere::class => static::getContentWhere(...),
            'content-blocks.icons' => static::getContentBlockIcons(...),
            'content-blocks.page-types' => static::getContentBlockPageTypes(...),
            'content-blocks.typoscript' => static::getContentBlockTypoScript(...),
            'content-blocks.user-tsconfig' => static::getContentBlockUserTsConfig(...),
            'content-blocks.parent-field-names' => static::getContentBlockParentFieldNames(...),
            'content-blocks.add-typoscript' => static::addTypoScript(...),
            'content-blocks.add-user-tsconfig' => static::addUserTsConfig(...),
            'content-blocks.register-icons' => static::configureIconRegistry(...),
            'content-blocks.hide-content-element-children' => static::hideContentElementChildren(...),
            'content-blocks.hide-content-element-children-page-content-fetching' => static::hideContentElementChildrenPageContentFetching(...),
            'content-blocks.record-summary-for-localization' => static::recordSummaryForLocalization(...),
            'content-blocks.tca' => static::tca(...),
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
        // @todo Extend IconFactory in TYPO3 v14 again and remove BootCompletedEvent hook.
        // @todo See commit: 1db5b5c8
        $arrayObject = new \ArrayObject();
        $cache = $container->get('cache.core');
        $iconsFromCache = $cache->require('ContentBlocks_Icons');
        if ($iconsFromCache !== false) {
            $arrayObject->exchangeArray($iconsFromCache);
            return $arrayObject;
        }
        /** @var TableDefinitionCollection $tableDefinitionCollection */
        $tableDefinitionCollection = $container->get(TableDefinitionCollection::class);
        foreach ($tableDefinitionCollection as $tableDefinition) {
            /** @var ContentTypeInterface $typeDefinition */
            foreach ($tableDefinition->contentTypeDefinitionCollection as $typeDefinition) {
                $icon = $typeDefinition->getTypeIcon();
                $iconConfig = [];
                if ($icon->initialized) {
                    $iconConfig = [
                        $icon->iconIdentifier => [
                            'source' => $icon->iconPath,
                            'provider' => $icon->iconProvider,
                        ],
                    ];
                }
                if ($typeDefinition instanceof PageTypeDefinition) {
                    if ($typeDefinition->getPageIconSet()->iconHideInMenu->initialized) {
                        $hideInMenuIcon = $typeDefinition->getPageIconSet()->iconHideInMenu;
                        $iconConfig[$hideInMenuIcon->iconIdentifier] = [
                            'source' => $hideInMenuIcon->iconPath,
                        ];
                    }
                    if ($typeDefinition->getPageIconSet()->iconRoot->initialized) {
                        $rootIcon = $typeDefinition->getPageIconSet()->iconRoot;
                        $iconConfig[$rootIcon->iconIdentifier] = [
                            'source' => $rootIcon->iconPath,
                        ];
                    }
                }
                if ($iconConfig === []) {
                    continue;
                }
                $arrayObject->exchangeArray(array_merge($arrayObject->getArrayCopy(), $iconConfig));
            }
        }
        $cache->set('ContentBlocks_Icons', 'return ' . var_export($arrayObject->getArrayCopy(), true) . ';');
        return $arrayObject;
    }

    public static function getContentBlockPageTypes(ContainerInterface $container): \ArrayObject
    {
        $arrayObject = new \ArrayObject();
        /** @var TableDefinitionCollection $tableDefinitionCollection */
        $tableDefinitionCollection = $container->get(TableDefinitionCollection::class);
        if (!$tableDefinitionCollection->hasTable(ContentType::PAGE_TYPE->getTable())) {
            return $arrayObject;
        }
        $tableDefinition = $tableDefinitionCollection->getTable(ContentType::PAGE_TYPE->getTable());
        foreach ($tableDefinition->contentTypeDefinitionCollection as $typeDefinition) {
            $arrayObject->append($typeDefinition->getTypeName());
        }
        return $arrayObject;
    }

    public static function getContentBlockTypoScript(ContainerInterface $container): \ArrayObject
    {
        $arrayObject = new \ArrayObject();
        $cache = $container->get('cache.core');
        $typoScriptFromCache = $cache->require('ContentBlocks_Typoscript');
        if ($typoScriptFromCache !== false) {
            $arrayObject->exchangeArray($typoScriptFromCache);
            return $arrayObject;
        }

        $contentBlockRegistry = $container->get(ContentBlockRegistry::class);
        /** @var TableDefinitionCollection $tableDefinitionCollection */
        $tableDefinitionCollection = $container->get(TableDefinitionCollection::class);
        foreach ($tableDefinitionCollection as $tableDefinition) {
            foreach ($tableDefinition->contentTypeDefinitionCollection as $typeDefinition) {
                if ($tableDefinition->contentType === ContentType::CONTENT_ELEMENT) {
                    $extPath = $contentBlockRegistry->getContentBlockExtPath($typeDefinition->getName());
                    $extPrivatePath = $extPath . '/' . ContentBlockPathUtility::getTemplatesFolder();
                    $templateFileName = ContentBlockPathUtility::getFrontendTemplateFileName();
                    $extFilePath = $extPrivatePath . '/' . $templateFileName;
                    $absolutePath = GeneralUtility::getFileAbsFileName($extFilePath);
                    if (!file_exists($absolutePath)) {
                        continue;
                    }

                    $importStatement = '';
                    if (file_exists(GeneralUtility::getFileAbsFileName($extPath . '/setup.typoscript'))) {
                        $importStatement = '@import "' . $extPath . '/setup.typoscript"';
                    }


                    $typoScript = <<<HEREDOC
tt_content.{$typeDefinition->getTypeName()} =< lib.contentBlock
tt_content.{$typeDefinition->getTypeName()} {
    file = $extFilePath
    partialRootPaths {
        20 = $extPrivatePath/partials/
    }
    layoutRootPaths {
        20 = $extPrivatePath/layouts/
    }
}
$importStatement
HEREDOC;
                    $arrayObject->append($typoScript);
                }
            }
        }
        $cache->set('ContentBlocks_Typoscript', 'return ' . var_export($arrayObject->getArrayCopy(), true) . ';');
        return $arrayObject;
    }

    public static function getContentBlockUserTsConfig(ContainerInterface $container): \ArrayObject
    {
        $arrayObject = new \ArrayObject();
        $cache = $container->get('cache.core');
        $typoScriptFromCache = $cache->require('ContentBlocks_UserTsConfig');
        if ($typoScriptFromCache !== false) {
            $arrayObject->exchangeArray($typoScriptFromCache);
            return $arrayObject;
        }

        /** @var TableDefinitionCollection $tableDefinitionCollection */
        $tableDefinitionCollection = $container->get(TableDefinitionCollection::class);
        foreach ($tableDefinitionCollection as $tableDefinition) {
            foreach ($tableDefinition->contentTypeDefinitionCollection as $typeDefinition) {
                if ($typeDefinition instanceof PageTypeDefinition) {
                    $options = 'options.pageTree.doktypesToShowInNewPageDragArea := addToList(' . $typeDefinition->getTypeName() . ')';
                    $arrayObject->append($options);
                }
            }
        }

        $cache->set('ContentBlocks_UserTsConfig', 'return ' . var_export($arrayObject->getArrayCopy(), true) . ';');
        return $arrayObject;
    }

    public static function getContentBlockParentFieldNames(ContainerInterface $container): \ArrayObject
    {
        $arrayObject = new \ArrayObject();
        $cache = $container->get('cache.core');
        $typoScriptFromCache = $cache->require('ContentBlocks_ParentFieldNames');
        if ($typoScriptFromCache !== false) {
            $arrayObject->exchangeArray($typoScriptFromCache);
            return $arrayObject;
        }

        /** @var TableDefinitionCollection $tableDefinitionCollection */
        $tableDefinitionCollection = $container->get(TableDefinitionCollection::class);
        $contentElementTable = ContentType::CONTENT_ELEMENT->getTable();
        if ($tableDefinitionCollection->hasTable($contentElementTable)) {
            $fieldNames = [];
            $contentElementTableDefinition = $tableDefinitionCollection->getTable($contentElementTable);
            foreach ($contentElementTableDefinition->parentReferences as $parentReference) {
                $fieldConfiguration = $parentReference->getTca()['config'] ?? [];
                if (($fieldConfiguration['foreign_table'] ?? '') === $contentElementTable) {
                    $foreignField = $fieldConfiguration['foreign_field'];
                    $fieldNames[$foreignField] = $foreignField;
                }
            }
            $arrayObject->exchangeArray(array_values($fieldNames));
        }
        $cache->set('ContentBlocks_ParentFieldNames', 'return ' . var_export($arrayObject->getArrayCopy(), true) . ';');
        return $arrayObject;
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
        return static function (BeforeLoadedUserTsConfigEvent $event) use ($container) {
            $arrayObject = $container->get('content-blocks.user-tsconfig');
            $concatenatedTypoScript = implode(LF, $arrayObject->getArrayCopy());
            $event->addTsConfig($concatenatedTypoScript);
        };
    }

    public static function tca(ContainerInterface $container): \Closure
    {
        return static function (BeforeTcaOverridesEvent $event) use ($container) {
            $tcaGenerator = $container->get(TcaGenerator::class);
            $tcaGenerator($event);
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

    public static function hideContentElementChildrenPageContentFetching(ContainerInterface $container): \Closure
    {
        return static function (AfterContentHasBeenFetchedEvent $event) use ($container) {
            $parentFieldNames = $container->get('content-blocks.parent-field-names');
            foreach ($event->groupedContent as $columnIdentifier => $column) {
                foreach ($column['records'] ?? [] as $key => $record) {
                    $rawRecord = $record->getRawRecord();
                    if ($rawRecord === null) {
                        continue;
                    }
                    foreach ($parentFieldNames as $fieldName) {
                        if ($rawRecord->has($fieldName) && (int)($rawRecord->get($fieldName) ?? 0) > 0) {
                            unset($event->groupedContent[$columnIdentifier]['records'][$key]);
                            break;
                        }
                    }
                }
            }
        };
    }

    /**
     * This removes all nested Content Element children from the translation wizard.
     */
    public static function recordSummaryForLocalization(ContainerInterface $container): \Closure
    {
        return static function (AfterRecordSummaryForLocalizationEvent $event) use ($container) {
            $recordsPerColumn = $event->getRecords();
            $arrayObject = $container->get('content-blocks.parent-field-names');
            $parentFieldNames = $arrayObject->getArrayCopy();
            $allUids = [];
            foreach ($recordsPerColumn as $records) {
                foreach ($records as $record) {
                    $allUids[] = (int)$record['uid'];
                }
            }
            $queryBuilder = $container->get(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
            $queryBuilder
                ->getRestrictions()
                ->removeAll()
                ->add(GeneralUtility::makeInstance(DeletedRestriction::class));
            $queryBuilder
                ->select('uid')
                ->from('tt_content')
                ->where(
                    $queryBuilder->expr()->in('uid', $queryBuilder->createNamedParameter($allUids, ArrayParameterType::INTEGER)),
                );
            foreach ($parentFieldNames as $parentFieldName) {
                $queryBuilder->andWhere($queryBuilder->expr()->eq($parentFieldName, $queryBuilder->createNamedParameter(0, Connection::PARAM_INT)));
            }
            $rootUids = $queryBuilder->executeQuery()->fetchFirstColumn();
            // Map uids to integer values.
            $rootUids = array_map(fn(int|string $uid): int => (int)$uid, $rootUids);
            foreach ($recordsPerColumn as $column => $records) {
                foreach ($records as $key => $record) {
                    $uid = (int)$record['uid'];
                    if (!in_array($uid, $rootUids, true)) {
                        unset($recordsPerColumn[$column][$key]);
                    }
                }
                // Ensure this is a list. Apparently this is important for the wizard to work correctly.
                $recordsPerColumn[$column] = array_values($recordsPerColumn[$column]);
            }
            $event->setRecords($recordsPerColumn);
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
        };
    }

    public static function configurePageTypes(ContainerInterface $container, PageDoktypeRegistry $pageDoktypeRegistry): PageDoktypeRegistry
    {
        // Early core cache is required here, as PageDokTypeRegistry is instantiated in ExtensionManagementUtility::loadBaseTca
        $cache = $container->get('cache.core');
        $pageTypesFromContentBlocks = $cache->require('ContentBlocks_PageTypes');
        if ($pageTypesFromContentBlocks === false) {
            $pageTypesFromContentBlocks = $container->get('content-blocks.page-types')->getArrayCopy();
            $cache->set('ContentBlocks_PageTypes', 'return ' . var_export($pageTypesFromContentBlocks, true) . ';');
        }
        foreach ($pageTypesFromContentBlocks as $pageType) {
            $pageDoktypeRegistry->add((int)$pageType, []);
        }
        return $pageDoktypeRegistry;
    }

    public static function addEventListeners(ContainerInterface $container, ListenerProvider $listenerProvider): ListenerProvider
    {
        $listenerProvider->addListener(BootCompletedEvent::class, 'content-blocks.add-typoscript');
        $listenerProvider->addListener(BootCompletedEvent::class, 'content-blocks.register-icons');
        $listenerProvider->addListener(BeforeLoadedUserTsConfigEvent::class, 'content-blocks.add-user-tsconfig');
        $listenerProvider->addListener(ModifyDatabaseQueryForContentEvent::class, 'content-blocks.hide-content-element-children');
        $listenerProvider->addListener(AfterContentHasBeenFetchedEvent::class, 'content-blocks.hide-content-element-children-page-content-fetching');
        $listenerProvider->addListener(AfterRecordSummaryForLocalizationEvent::class, 'content-blocks.record-summary-for-localization');
        $listenerProvider->addListener(BeforeTcaOverridesEvent::class, 'content-blocks.tca');
        return $listenerProvider;
    }
}
