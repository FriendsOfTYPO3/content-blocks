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

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;
use TYPO3\CMS\ContentBlocks\Loader\LoadedContentBlock;

/**
 * @internal Not part of TYPO3's public API.
 */
#[Autoconfigure(public: true)]
final class ContentBlockRegistry
{
    /**
     * @var LoadedContentBlock[]
     */
    private array $contentBlocks = [];
    private array $typeNamesByTable = [];

    public function register(LoadedContentBlock $contentBlock): void
    {
        if ($this->hasContentBlock($contentBlock->getName())) {
            throw new \InvalidArgumentException(
                'The Content Block with the name "' . $contentBlock->getName() . '" exists more than once.'
                . ' Please choose another name.',
                1678474766
            );
        }
        $this->contentBlocks[$contentBlock->getName()] = $contentBlock;
        $this->registerTypeName($contentBlock);
    }

    public function hasContentBlock(string $name): bool
    {
        return array_key_exists($name, $this->contentBlocks);
    }

    public function getContentBlock(string $name): LoadedContentBlock
    {
        if (!$this->hasContentBlock($name)) {
            throw new \OutOfBoundsException('Content block with the name "' . $name . '" is not registered.', 1678478902);
        }
        return $this->contentBlocks[$name];
    }

    public function getContentBlockExtPath(string $name): string
    {
        return $this->getContentBlock($name)->getExtPath();
    }

    /**
     * @return LoadedContentBlock[]
     */
    public function getAll(): array
    {
        return $this->contentBlocks;
    }

    public function getFromRawRecord(string $table, array $record = []): ?LoadedContentBlock
    {
        if (array_key_exists($table, $this->typeNamesByTable) === false) {
            return null;
        }
        $typeField = $this->typeNamesByTable[$table]['typeField'];
        if ($typeField === null) {
            $typeName = '1';
        } else {
            if (array_key_exists($typeField, $record) === false) {
                return null;
            }
            $typeName = $record[$typeField];
        }
        if (array_key_exists($typeName, $this->typeNamesByTable[$table]['types']) === false) {
            return null;
        }
        $contentBlock = $this->typeNamesByTable[$table]['types'][$typeName];
        return $contentBlock;
    }

    private function registerTypeName(LoadedContentBlock $contentBlock): void
    {
        // If typeName is not set explicitly, then it is inferred from the name, which is unique.
        $contentType = $contentBlock->getContentType();
        $yaml = $contentBlock->getYaml();
        $typeField = $yaml['typeField'] ?? null;
        if ($typeField === null && $contentType !== ContentType::FILE_TYPE) {
            $typeName = '1';
        }
        $typeName ??= (string)$yaml['typeName'];

        // The typeName has to be unique per table. Get it from the YAML for Record Types.
        $table = $contentType->getTable() ?? $yaml['table'];
        if (!isset($this->typeNamesByTable[$table]['types'][$typeName])) {
            $this->typeNamesByTable[$table]['typeField'] ??= $typeField;
            $this->typeNamesByTable[$table]['types'][$typeName] = $contentBlock;
            return;
        }

        // Duplicate typeName detected. Fail hard.
        $tableInfo = '';
        if ($contentType === ContentType::RECORD_TYPE) {
            $tableInfo = ' for table "' . $table . '"';
        }
        throw new \InvalidArgumentException(
            'The ' . $contentType->getHumanReadable() . ' with the typeName "' . $typeName . '"'
            . $tableInfo . ' exists more than once. Please choose another typeName.',
            1701351270
        );
    }
}
