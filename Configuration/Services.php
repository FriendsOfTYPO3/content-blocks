<?php

declare(strict_types=1);

namespace TYPO3\CMS\ContentBlocks;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use TYPO3\CMS\ContentBlocks\DependencyInjection\LoaderPass;

return function (ContainerConfigurator $container, ContainerBuilder $containerBuilder) {
    $containerBuilder->addCompilerPass(new LoaderPass('cb.loader'));
};
