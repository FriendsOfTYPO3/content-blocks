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

use TYPO3\CMS\ContentBlocks\FieldConfiguration\FieldType;

/**
 * @internal Not part of TYPO3's public API.
 */
final class ProcessedFieldsResult
{
    public array $tableDefinitionList = [];
    public ProcessedTableDefinition $tableDefinition;
    public ProcessedContentType $contentType;
    /** @var string[] */
    public array $uniqueFieldIdentifiers = [];
    /** @var string[] */
    public array $uniquePaletteIdentifiers = [];
    /** @var string[] */
    public array $uniqueTabIdentifiers = [];
    /** @var array<string, string> */
    public array $identifierToUniqueMap = [];

    // Below are temporary properties for the scope of a root field.
    public string $identifier;
    public string $uniqueIdentifier;
    public FieldType $fieldType;
    public array $tcaFieldDefinition;

    public function __construct(ProcessingInput $input)
    {
        $this->tableDefinition = new ProcessedTableDefinition();
        $this->contentType = new ProcessedContentType();
        $this->tableDefinitionList = $input->tableDefinitionList;
        $this->contentType->contentBlock = $input->contentBlock;
        $this->contentType->typeName = $input->getTypeName();
        $this->contentType->table = $input->table;
        $this->tableDefinition->typeField = $input->getTypeField();
        $this->tableDefinition->raw = $input->yaml;
        $this->tableDefinition->contentType = $input->contentType;
    }

    public function resetTemporaryState(): void
    {
        unset(
            $this->identifier,
            $this->uniqueIdentifier,
            $this->fieldType,
            $this->tcaFieldDefinition,
        );
    }
}
