<?php

declare(strict_types=1);

namespace TYPO3\CMS\ContentBlocks;

use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use TYPO3\CMS\ContentBlocks\DependencyInjection\FieldTypeCompilerPass;
use TYPO3\CMS\ContentBlocks\FieldType\FieldType;

return static function (ContainerConfigurator $container, ContainerBuilder $containerBuilder) {
    $containerBuilder->registerAttributeForAutoconfiguration(
        FieldType::class,
        static function (ChildDefinition $definition, FieldType $attribute): void {
            $definition->addTag(FieldType::TAG_NAME, [
                'name' => $attribute->name,
                'tcaType' => $attribute->tcaType,
            ]);
        }
    );
    $containerBuilder->addCompilerPass(new FieldTypeCompilerPass());
};
