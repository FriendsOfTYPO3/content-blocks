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

use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;
use TYPO3\CMS\ContentBlocks\Loader\LoadedContentBlock;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * @internal Not part of TYPO3's public API.
 */
class ContentBlockRegistry implements SingletonInterface
{
    /**
     * @var LoadedContentBlock[]
     */
    protected array $contentBlocks = [];
    protected array $typeNamesByTable = [];

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

    protected function registerTypeName(LoadedContentBlock $contentBlock): void
    {
        // If typeName is not set explicitly, then it is inferred from the name, which is unique.
        $yaml = $contentBlock->getYaml();
        if (!array_key_exists('typeName', $yaml)) {
            return;
        }

        // The typeName has to be unique per table. Get it from the YAML for Record Types.
        $contentType = $contentBlock->getContentType();
        $typeName = (string)$yaml['typeName'];
        $table = $contentType->getTable() ?? $yaml['table'];
        if (!isset($this->typeNamesByTable[$table][$typeName])) {
            $this->typeNamesByTable[$table][$typeName] = $typeName;
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

    public function flush(): void
    {
        $this->contentBlocks = [];
        $this->typeNamesByTable = [];
    }
}
