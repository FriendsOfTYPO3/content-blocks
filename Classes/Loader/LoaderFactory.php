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

namespace TYPO3\CMS\ContentBlocks\Loader;

use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;

/**
 * @internal Not part of TYPO3's public API.
 */
class LoaderFactory
{
    /**
     * @var array<string, LoaderInterface>
     */
    protected array $loaders;

    public function addLoader(LoaderInterface $loader, string $identifier): void
    {
        $this->loaders[$identifier] = $loader;
    }

    public function create(): LoaderInterface
    {
        return $this->loaders['content-block'];
    }

    public function load(): TableDefinitionCollection
    {
        return $this->create()->load();
    }
}
