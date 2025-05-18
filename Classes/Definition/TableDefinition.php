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

use TYPO3\CMS\ContentBlocks\Definition\Capability\TableDefinitionCapability;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentTypeDefinitionCollection;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentTypeInterface;

/**
 * @internal Not part of TYPO3's public API.
 */
final readonly class TableDefinition
{
    public function __construct(
        public string $table,
        public TableDefinitionCapability $capability,
        public ?string $typeField,
        public ContentType $contentType,
        public ContentTypeDefinitionCollection $contentTypeDefinitionCollection,
        public SqlColumnDefinitionCollection $sqlColumnDefinitionCollection,
        public TcaFieldDefinitionCollection $tcaFieldDefinitionCollection,
        public PaletteDefinitionCollection $paletteDefinitionCollection,
        public TcaFieldDefinitionCollection $parentReferences,
    ) {
        if ($table === '') {
            throw new \InvalidArgumentException('The name of the table must not be empty.', 1628672227);
        }
    }

    public function getDefaultTypeDefinition(): ContentTypeInterface
    {
        $typeDefinitionCollection = $this->contentTypeDefinitionCollection;
        if ($typeDefinitionCollection->hasType('1')) {
            $defaultTypeDefinition = $typeDefinitionCollection->getType('1');
        } else {
            $defaultTypeDefinition = $typeDefinitionCollection->getFirst();
        }
        return $defaultTypeDefinition;
    }

    public function hasTypeField(): bool
    {
        return $this->typeField !== null;
    }

    public function hasParentReferences(): bool
    {
        return $this->parentReferences->count() > 0;
    }
}
