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

/**
 * @internal Not part of TYPO3's public API.
 */
final class TableDefinitionCapability
{
    private RootLevelCapability $rootLevelCapability;
    private bool $languageAware = true;
    private bool $workspaceAware = true;
    private bool $disabledRestriction = true;
    private bool $startTimeRestriction = true;
    private bool $endTimeRestriction = true;
    private bool $userGroupRestriction = true;
    private bool $editLocking = true;
    private bool $softDelete = true;
    private bool $trackCreationDate = true;
    private bool $trackUpdateDate = true;
    private bool $sortable = true;
    private bool $trackAncestorReference = true;
    private bool $internalDescription = false;
    private string $sortField = '';
    private string $useAsLabel = '';

    public static function createFromArray(array $definition): TableDefinitionCapability
    {
        $capability = new TableDefinitionCapability();
        $capability->languageAware = (bool)($definition['languageAware'] ?? $capability->languageAware);
        $capability->workspaceAware = (bool)($definition['workspaceAware'] ?? $capability->workspaceAware);
        $capability->trackAncestorReference = (bool)($definition['trackAncestorReference'] ?? $capability->trackAncestorReference);
        $capability->disabledRestriction = (bool)($definition['restriction']['disabled'] ?? $capability->disabledRestriction);
        $capability->startTimeRestriction = (bool)($definition['restriction']['startTime'] ?? $capability->startTimeRestriction);
        $capability->endTimeRestriction = (bool)($definition['restriction']['endTime'] ?? $capability->endTimeRestriction);
        $capability->userGroupRestriction = (bool)($definition['restriction']['userGroup'] ?? $capability->userGroupRestriction);
        $capability->editLocking = (bool)($definition['editLocking'] ?? $capability->editLocking);
        $capability->softDelete = (bool)($definition['softDelete'] ?? $capability->softDelete);
        $capability->trackCreationDate = (bool)($definition['trackCreationDate'] ?? $capability->trackCreationDate);
        $capability->trackUpdateDate = (bool)($definition['trackUpdateDate'] ?? $capability->trackUpdateDate);
        $capability->sortable = (bool)($definition['sortable'] ?? $capability->sortable);
        $capability->sortField = (string)($definition['sortField'] ?? $capability->sortField);
        $capability->useAsLabel = (string)($definition['useAsLabel'] ?? $capability->useAsLabel);
        $capability->internalDescription = (bool)($definition['internalDescription'] ?? $capability->internalDescription);
        $capability->rootLevelCapability = RootLevelCapability::createFromArray($definition);
        return $capability;
    }

    public function isLanguageAware(): bool
    {
        return $this->languageAware;
    }

    public function isWorkspaceAware(): bool
    {
        return $this->workspaceAware;
    }

    public function shallTrackAncestorReference(): bool
    {
        return $this->trackAncestorReference;
    }

    public function hasDisabledRestriction(): bool
    {
        return $this->disabledRestriction;
    }

    public function hasStartTimeRestriction(): bool
    {
        return $this->startTimeRestriction;
    }

    public function hasEndTimeRestriction(): bool
    {
        return $this->endTimeRestriction;
    }

    public function hasUserGroupRestriction(): bool
    {
        return $this->userGroupRestriction;
    }

    public function isEditLockingEnabled(): bool
    {
        return $this->editLocking;
    }

    public function hasSoftDelete(): bool
    {
        return $this->softDelete;
    }

    public function shallTrackCreationDate(): bool
    {
        return $this->trackCreationDate;
    }

    public function shallTrackUpdateDate(): bool
    {
        return $this->trackUpdateDate;
    }

    public function isSortable(): bool
    {
        return $this->sortable;
    }

    public function hasSortField(): bool
    {
        return $this->sortField !== '';
    }

    public function getSortField(): string
    {
        return $this->sortField;
    }

    public function hasUseAsLabel(): bool
    {
        return $this->useAsLabel !== '';
    }

    public function getUseAsLabel(): string
    {
        return $this->useAsLabel;
    }

    public function hasInternalDescription(): bool
    {
        return $this->internalDescription;
    }

    public function getRootLevelCapability(): RootLevelCapability
    {
        return $this->rootLevelCapability;
    }

    public function getRestrictionsTca(): array
    {
        $restrictions = [];
        if ($this->hasDisabledRestriction()) {
            $restrictions['disabled'] = 'hidden';
        }
        if ($this->hasStartTimeRestriction()) {
            $restrictions['starttime'] = 'starttime';
        }
        if ($this->hasEndTimeRestriction()) {
            $restrictions['endtime'] = 'endtime';
        }
        if ($this->hasUserGroupRestriction()) {
            $restrictions['fe_group'] = 'fe_group';
        }
        return $restrictions;
    }

    public function getAccessShowItemTca(): string
    {
        if (!$this->hasAccessPalette()) {
            return '';
        }
        $access = [];
        if ($this->hasStartTimeRestriction()) {
            $access[] = 'starttime;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:starttime_formlabel';
        }
        if ($this->hasEndTimeRestriction()) {
            $access[] = 'endtime;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:endtime_formlabel';
        }
        if ($this->hasStartTimeRestriction() || $this->hasEndTimeRestriction()) {
            $access[] = '--linebreak--';
        }
        if ($this->hasUserGroupRestriction()) {
            $access[] = 'fe_group;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:fe_group_formlabel';
            $access[] = '--linebreak--';
        }
        if ($this->isEditLockingEnabled()) {
            $access[] = 'editlock';
        }
        $accessTcaString = implode(',', $access);
        return $accessTcaString;
    }

    public function hasAccessPalette(): bool
    {
        return $this->hasStartTimeRestriction() || $this->hasEndTimeRestriction() || $this->hasUserGroupRestriction() || $this->isEditLockingEnabled();
    }
}
