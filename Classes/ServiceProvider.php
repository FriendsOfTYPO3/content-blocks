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
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;
use TYPO3\CMS\ContentBlocks\Definition\Factory\TableDefinitionCollectionFactory;
use TYPO3\CMS\ContentBlocks\Generator\TypoScriptGenerator;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\Core\DataHandling\PageDoktypeRegistry;
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
            'content-block-icons' => [ static::class, 'getContentBlockIcons' ],
            'content-block-page-types' => [ static::class, 'getContentBlockPageTypes' ],
            'content-block-typoscript' => [ static::class, 'getContentBlockTypoScript' ],
            TypoScriptGenerator::class => [ static::class, 'getTypoScriptGenerator' ],
        ];
    }

    public function getExtensions(): array
    {
        return [
            IconRegistry::class => [ static::class, 'configureIconRegistry' ],
            PageDoktypeRegistry::class => [static::class, 'configurePageTypes' ],
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

    public static function getContentBlockIcons(ContainerInterface $container): \ArrayObject
    {
        $arrayObject = new \ArrayObject();
        $tableDefinitionCollectionFactory = $container->get(TableDefinitionCollectionFactory::class);
        $tableDefinitionCollection = $tableDefinitionCollectionFactory->create();
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
        $tableDefinitionCollectionFactory = $container->get(TableDefinitionCollectionFactory::class);
        $tableDefinitionCollection = $tableDefinitionCollectionFactory->create();
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
        $cache = $container->get('cache.core');
        $typoScriptFromCache = $cache->require('TypoScript_ContentBlocks');
        if ($typoScriptFromCache !== false) {
            $arrayObject->exchangeArray($typoScriptFromCache);
            return $arrayObject;
        }

        $tableDefinitionCollectionFactory = $container->get(TableDefinitionCollectionFactory::class);
        $contentBlockRegistry = $container->get(ContentBlockRegistry::class);
        $tableDefinitionCollection = $tableDefinitionCollectionFactory->create();
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

    public static function configureIconRegistry(ContainerInterface $container, IconRegistry $iconRegistry): IconRegistry
    {
        $cache = $container->get('cache.core');

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
}
