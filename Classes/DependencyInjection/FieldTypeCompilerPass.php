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

namespace TYPO3\CMS\ContentBlocks\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use TYPO3\CMS\ContentBlocks\FieldType\FieldType;

/**
 * @internal
 */
final class FieldTypeCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $taggedServices = $container->findTaggedServiceIds(FieldType::TAG_NAME);
        foreach ($taggedServices as $id => $tags) {
            $definition = $container->findDefinition($id);
            foreach ($tags as $attributes) {
                $definition->addMethodCall('setName', [$attributes['name']]);
                $definition->addMethodCall('setTcaType', [$attributes['tcaType']]);
                $definition->addMethodCall('setSearchable', [$attributes['searchable']]);
            }
        }
    }
}
