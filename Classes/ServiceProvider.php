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
use TYPO3\CMS\ContentBlocks\Definition\Factory\TableDefinitionCollectionFactory;
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
        ];
    }

    public function getExtensions(): array
    {
        return [
            IconRegistry::class => [ static::class, 'configureIconRegistry' ],
        ] + parent::getExtensions();
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
}
