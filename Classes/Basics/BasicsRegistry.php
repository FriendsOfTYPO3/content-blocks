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

namespace TYPO3\CMS\ContentBlocks\Basics;

/**
 * @internal Not part of TYPO3's public API.
 */
final class BasicsRegistry
{
    /**
     * @var LoadedBasic[]
     */
    protected array $basics = [];

    public function register(LoadedBasic $basic): void
    {
        if ($this->hasBasic($basic->getIdentifier())) {
            throw new \RuntimeException(
                'The Content Block Basic "' . $basic->getIdentifier() . '" already exists. Please choose another identifier.',
                1701279535
            );
        }
        $this->basics[$basic->getIdentifier()] = $basic;
    }

    public function hasBasic(string $identifier): bool
    {
        return array_key_exists($identifier, $this->basics);
    }

    public function getBasic(string $identifier): LoadedBasic
    {
        if (!$this->hasBasic($identifier)) {
            throw new \OutOfBoundsException('Basic with identifier "' . $identifier . '" is not registered.', 1688398604);
        }
        return $this->basics[$identifier];
    }

    public function getAllBasics(): array
    {
        return $this->basics;
    }
}
