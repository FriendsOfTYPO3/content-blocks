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

namespace TYPO3\CMS\ContentBlocks\Definition;

use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentTypeDefinitionCollection;
use TYPO3\CMS\ContentBlocks\FieldType\FieldTypeInterface;

/**
 * @internal Not part of TYPO3's public API.
 */
final readonly class TcaFieldDefinition
{
    public function __construct(
        public ContentType $parentContentType,
        public string $identifier,
        public string $uniqueIdentifier,
        public string $labelPath,
        public string $descriptionPath,
        public bool $useExistingField,
        public FieldTypeInterface $fieldType,
        public ?ContentTypeDefinitionCollection $typeOverrides = null,
    ) {
        if ($uniqueIdentifier === '') {
            throw new \InvalidArgumentException('The identifier for a TcaFieldDefinition must not be empty.', 1629277138);
        }
    }

    public function getTca(): array
    {
        return $this->fieldType->getTca();
    }
}
