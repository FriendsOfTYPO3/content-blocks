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

namespace TYPO3\CMS\ContentBlocks\DataProcessing;

/**
 * @internal Not part of TYPO3's public API.
 */
class RelationResolverSession
{
    /**
     * @var array<string, ResolvedRelation>
     */
    protected array $resolvedRelations = [];

    public function addRelation(string $identifier, ResolvedRelation $relation): void
    {
        $this->resolvedRelations[$identifier] = $relation;
    }

    public function hasRelation(string $identifier): bool
    {
        return array_key_exists($identifier, $this->resolvedRelations);
    }

    public function getRelation(string $identifier): ResolvedRelation
    {
        return $this->resolvedRelations[$identifier];
    }
}
