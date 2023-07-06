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

namespace TYPO3\CMS\ContentBlocks\Registry;

use TYPO3\CMS\ContentBlocks\Loader\LoadedContentBlockBasic;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * @internal Not part of TYPO3's public API.
 */
class ContentBlockBasicsRegistry implements SingletonInterface
{
    /**
     * @var LoadedContentBlockBasic[]
     */
    protected array $basics = [];

    public function register(LoadedContentBlockBasic $newBasic): void
    {
        $this->basics[$newBasic->getIdentifier()] = $newBasic;
    }

    public function hasBasic(string $identifier): bool
    {
        return array_key_exists($identifier, $this->basics);
    }

    public function getBasic(string $identifier): LoadedContentBlockBasic
    {
        if (!$this->hasBasic($identifier)) {
            throw new \OutOfBoundsException('ContentBlockBasic with the identifier "' . $identifier . '" is not registered.', 1688398604);
        }
        return $this->basics[$identifier];
    }
    
    /**
     * Checks whether a basic is registered and append its fields after the given array.
     * If the basic is not registered, the given array is returned.
     * 
     * @param array $fields        fields from a content block
     * @param string $identifier   identifier of the basic
     * @return array              fields with the basic's fields added or even the fields from the content block
     */
    public function addBasicsToFields(array $fields, string $identifier): array
    {
        if ($this->hasBasic($identifier)) {
            $fields = array_merge(
                $fields,
                $this->getBasic($identifier)->getFields()
            );
        }
        return $fields;
    }

    public function getAllBasics(): array
    {
        return $this->basics;
    }
}
