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

use TYPO3\CMS\Core\Domain\RawRecord;
use TYPO3\CMS\Core\Domain\Record;
use TYPO3\CMS\Core\Domain\Record\ComputedProperties;
use TYPO3\CMS\Core\Domain\Record\LanguageInfo;
use TYPO3\CMS\Core\Domain\Record\SystemProperties;
use TYPO3\CMS\Core\Domain\Record\VersionInfo;
use TYPO3\CMS\Core\Domain\RecordInterface;

/**
 * This class represents the `data` object inside the Fluid template for Content Blocks.
 *
 * Usage in Fluid
 * ==============
 *
 * To access own custom fields, use: `data.identifier`.
 * E.g.:
 * - {data.header}
 * - {data.collection_field.text_field}
 *
 * There are some special accessors, which are always available:
 * - {data.uid}
 * - {data.pid}
 * - {data.recordType}
 *
 * These special accessors are available, if the corresponding features are turned on
 * (Always true for Content Elements):
 *  - {data.languageId} (YAML: languageAware: true)
 *  - {data.systemProperties.createdAt} (YAML: trackCreationDate: true)
 *  - {data.systemProperties.lastUpdatedAt} (YAML: trackUpdateDate: true)
 *
 * These special accessors are available depending on the context:
 * - {data.computedProperties.localizedUid}
 * - {data.computedProperties.versionedUid}
 */
final class ContentBlockData implements RecordInterface
{
    public function __construct(
        protected ?Record $_record = null,
        protected string $_name = '',
        /** @var array<string, RelationGrid>|array<string, RenderedGridItem[]> */
        protected array $_grids = [],
        protected array $_processed = [],
    ) {}

    public function getUid(): int
    {
        return $this->_record->getUid();
    }

    public function getPid(): int
    {
        return $this->_record->getPid();
    }

    public function getFullType(): string
    {
        return $this->_record->getFullType();
    }

    public function getRecordType(): ?string
    {
        return $this->_record->getRecordType();
    }

    public function getMainType(): string
    {
        return $this->_record->getMainType();
    }

    public function toArray(): array
    {
        return $this->_record->getRawRecord()->toArray();
    }

    public function has(string $id): bool
    {
        if ($id === '_name') {
            return true;
        }
        if ($id === '_grids') {
            return true;
        }
        if (array_key_exists($id, $this->_processed)) {
            return true;
        }
        return $this->_record->has($id);
    }

    public function get(string $id): mixed
    {
        if ($id === '_name') {
            return $this->_name;
        }
        if ($id === '_grids') {
            return $this->_grids;
        }
        if (array_key_exists($id, $this->_processed)) {
            return $this->_processed[$id];
        }
        return $this->_record->get($id);
    }

    public function getVersionInfo(): ?VersionInfo
    {
        return $this->_record->getVersionInfo();
    }

    public function getLanguageInfo(): ?LanguageInfo
    {
        return $this->_record->getLanguageInfo();
    }

    public function getLanguageId(): ?int
    {
        return $this->_record->getLanguageId();
    }

    public function getSystemProperties(): ?SystemProperties
    {
        return $this->_record->getSystemProperties();
    }

    public function getComputedProperties(): ComputedProperties
    {
        return $this->_record->getComputedProperties();
    }

    public function getRawRecord(): RawRecord
    {
        return $this->_record->getRawRecord();
    }

    public function getOverlaidUid(): int
    {
        return $this->_record->getOverlaidUid();
    }

    public function get_Name(): string
    {
        return $this->_name;
    }

    public function get_Grids(): array
    {
        return $this->_grids;
    }

    public function override(ContentBlockData $contentBlockData): void
    {
        $this->_record = $contentBlockData->_record;
        $this->_name = $contentBlockData->_name;
        $this->_grids = $contentBlockData->_grids;
        $this->_processed = $contentBlockData->_processed;
    }
}
