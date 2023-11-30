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
final class LoadedBasic
{
    public function __construct(
        private readonly string $hostExtension,
        private readonly string $identifier,
        private readonly array $fields,
    ) {}

    public static function fromArray(array $array, string $hostExtension): LoadedBasic
    {
        return new self(
            hostExtension: $hostExtension,
            identifier: (string)($array['identifier'] ?? ''),
            fields: (array)($array['fields'] ?? [])
        );
    }

    public function toArray(): array
    {
        return [
            'identifier' => $this->identifier,
            'fields' => $this->fields,
        ];
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function getHostExtension(): string
    {
        return $this->hostExtension;
    }
}
