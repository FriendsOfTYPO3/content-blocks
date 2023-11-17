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

namespace TYPO3\CMS\ContentBlocks\Definition\FlexForm;

/**
 * @internal Not part of TYPO3's public API.
 */
final class SectionDefinition implements \IteratorAggregate
{
    private string $identifier = '';
    /** @var ContainerDefinition[] */
    private array $container = [];
    private string $labelPath;

    /**
     * @return \Iterator<ContainerDefinition>
     */
    public function getIterator(): \Iterator
    {
        return new \ArrayIterator($this->container);
    }

    public function addContainer(ContainerDefinition $container): void
    {
        $this->container[] = $container;
    }

    public function getLabelPath(): string
    {
        return $this->labelPath;
    }

    public function setLabelPath(string $labelPath): void
    {
        $this->labelPath = $labelPath;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }
}
