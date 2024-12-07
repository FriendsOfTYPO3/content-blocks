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
use TYPO3\CMS\ContentBlocks\Loader\LoadedContentBlock;
use TYPO3\CMS\ContentBlocks\Schema\SimpleTcaSchemaFactory;

/**
 * @internal Not part of TYPO3's public API.
 */
final class ProcessingInput
{
    private bool $isRootTable;
    private ?string $typeField;
    private string|int $typeName;

    public function __construct(
        SimpleTcaSchemaFactory $simpleTcaSchemaFactory,
        public array $yaml,
        public LoadedContentBlock $contentBlock,
        public string $table,
        public string $rootTable,
        public LanguagePath $languagePath,
        public ContentType $contentType,
        public array $typeFieldPerTable = [],
        public array $tableDefinitionList = [],
    ) {
        $this->isRootTable = $this->table === $this->rootTable;
        $typeField = $this->yaml['typeField'] ?? null;
        $this->typeField = $typeField;
        if (!isset($this->typeField)) {
            if (isset($this->typeFieldPerTable[$this->table])) {
                $this->typeField = $this->typeFieldPerTable[$this->table];
            } else {
                $this->typeField = $this->getTypeFieldNative($simpleTcaSchemaFactory);
            }
        }
        $this->typeName = $this->resolveTypeName();
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

    private function getTypeFieldNative(SimpleTcaSchemaFactory $simpleTcaSchemaFactory): ?string
    {
        if (!$simpleTcaSchemaFactory->has($this->table)) {
            return null;
        }
        $tcaSchema = $simpleTcaSchemaFactory->get($this->table);
        $typeField = $tcaSchema->getTypeField();
        return $typeField?->getName();
    }

    private function resolveTypeName(): string|int
    {
        if (array_key_exists('typeName', $this->yaml)) {
            return $this->yaml['typeName'];
        }
        if ($this->typeField === null) {
            return '1';
        }
        // @todo typeName is missing, throw exception here?
    }
}
