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

namespace TYPO3\CMS\ContentBlocks\Definition\Factory\Processing;

use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;
use TYPO3\CMS\ContentBlocks\Definition\Factory\LanguagePath;
use TYPO3\CMS\ContentBlocks\Definition\Factory\UniqueIdentifierCreator;
use TYPO3\CMS\ContentBlocks\Loader\LoadedContentBlock;

/**
 * @internal Not part of TYPO3's public API.
 */
final class ProcessingInput
{
    private bool $isRootTable;
    private ?string $typeField;
    private string|int $typeName;

    public function __construct(
        public array $yaml,
        public LoadedContentBlock $contentBlock,
        public string $table,
        public string $rootTable,
        public LanguagePath $languagePath,
        public ContentType $contentType,
        public array $tableDefinitionList = [],
    ) {
        $this->isRootTable = $this->table === $this->rootTable;
        $this->typeField = $yaml['typeField'] ?? $GLOBALS['TCA'][$this->table]['ctrl']['type'] ?? null;
        $this->typeName = $this->getTypeField() === null
            ? '1'
            : $yaml['typeName'] ?? UniqueIdentifierCreator::createContentTypeIdentifier($this->contentBlock);
    }

    public function isRootTable(): bool
    {
        return $this->isRootTable;
    }

    public function getTypeField(): ?string
    {
        return $this->typeField;
    }

    public function getTypeName(): string|int
    {
        return $this->typeName;
    }
}
