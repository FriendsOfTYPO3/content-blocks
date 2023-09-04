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
    private bool $languageAware = true;
    private bool $workspaceAware = true;
    private bool $ancestorReferenceField = true;
    private bool $disabledRestriction = true;
    private bool $startTimeRestriction = true;
    private bool $endTimeRestriction = true;
    private bool $userGroupRestriction = true;
    private bool $editLocking = true;

    public static function createFromArray(array $definition): TableDefinitionCapability
    {
        $capability = new TableDefinitionCapability();
        $capability->languageAware = $definition['languageAware'] ?? $capability->languageAware;
        $capability->workspaceAware = $definition['workspaceAware'] ?? $capability->workspaceAware;
        $capability->ancestorReferenceField = $definition['ancestorReferenceField'] ?? $capability->ancestorReferenceField;
        $capability->disabledRestriction = $definition['restriction']['disabled'] ?? $capability->disabledRestriction;
        $capability->startTimeRestriction = $definition['restriction']['starttime'] ?? $capability->startTimeRestriction;
        $capability->endTimeRestriction = $definition['restriction']['endtime'] ?? $capability->endTimeRestriction;
        $capability->userGroupRestriction = $definition['restriction']['usergroup'] ?? $capability->userGroupRestriction;
        $capability->editLocking = $definition['editLocking'] ?? $capability->editLocking;
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

    public function hasAncestorReferenceField(): bool
    {
        return $this->ancestorReferenceField;
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
