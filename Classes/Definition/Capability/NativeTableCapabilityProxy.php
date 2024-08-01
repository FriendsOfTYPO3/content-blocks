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

namespace TYPO3\CMS\ContentBlocks\Definition\Capability;

use TYPO3\CMS\Core\Schema\Capability\TcaSchemaCapability;
use TYPO3\CMS\Core\Schema\TcaSchema;

/**
 * @internal Not part of TYPO3's public API.
 */
final class NativeTableCapabilityProxy implements SystemFieldPalettesInterface
{
    public function __construct(
        private readonly TcaSchema $tcaSchema
    ) {}

    public function hasAccessPalette(): bool
    {
        return $this->tcaSchema->hasCapability(TcaSchemaCapability::RestrictionStartTime)
            || $this->tcaSchema->hasCapability(TcaSchemaCapability::RestrictionEndTime)
            || $this->tcaSchema->hasCapability(TcaSchemaCapability::RestrictionUserGroup)
            || $this->tcaSchema->hasCapability(TcaSchemaCapability::EditLock);
    }

    public function hasDisabledRestriction(): bool
    {
        return $this->tcaSchema->hasCapability(TcaSchemaCapability::RestrictionDisabledField);
    }

    public function hasInternalDescription(): bool
    {
        return $this->tcaSchema->hasCapability(TcaSchemaCapability::InternalDescription);
    }

    public function isLanguageAware(): bool
    {
        return $this->tcaSchema->isLanguageAware();
    }

    public function buildAccessShowItemTca(): string
    {
        if (!$this->hasAccessPalette()) {
            return '';
        }
        $access = [];
        if ($this->tcaSchema->hasCapability(TcaSchemaCapability::RestrictionStartTime)) {
            $startTimeFieldCapability = $this->tcaSchema->getCapability(TcaSchemaCapability::RestrictionStartTime);
            $access[] = $startTimeFieldCapability->getFieldName() . ';LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:starttime_formlabel';
        }
        if ($this->tcaSchema->hasCapability(TcaSchemaCapability::RestrictionEndTime)) {
            $endTimeFieldCapability = $this->tcaSchema->getCapability(TcaSchemaCapability::RestrictionEndTime);
            $access[] = $endTimeFieldCapability->getFieldName() . ';LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:endtime_formlabel';
        }
        if ($this->tcaSchema->hasCapability(TcaSchemaCapability::RestrictionStartTime) || $this->tcaSchema->hasCapability(TcaSchemaCapability::RestrictionEndTime)) {
            $access[] = '--linebreak--';
        }
        if ($this->tcaSchema->hasCapability(TcaSchemaCapability::RestrictionUserGroup)) {
            $userGroupCapability = $this->tcaSchema->getCapability(TcaSchemaCapability::RestrictionUserGroup);
            $access[] = $userGroupCapability->getFieldName() . ';LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:fe_group_formlabel';
            $access[] = '--linebreak--';
        }
        if ($this->tcaSchema->hasCapability(TcaSchemaCapability::EditLock)) {
            $editLockCapability = $this->tcaSchema->getCapability(TcaSchemaCapability::EditLock);
            $access[] = $editLockCapability->getFieldName();
        }
        $count = count($access);
        if ($count > 0) {
            $lastIndex = $count - 1;
            $lastItem = $access[$lastIndex];
            if ($lastItem === '--linebreak--') {
                array_pop($access);
            }
        }
        $accessTcaString = implode(',', $access);
        return $accessTcaString;
    }

    public function buildLanguageShowItemTca(): string
    {
        if (!$this->tcaSchema->isLanguageAware()) {
            return '';
        }
        $languageCapability = $this->tcaSchema->getCapability(TcaSchemaCapability::Language);
        $languageFieldName = $languageCapability->getLanguageField()->getName();
        $languageParentFieldName = $languageCapability->getTranslationOriginPointerField()->getName();
        $showItem = $languageFieldName . ',' . $languageParentFieldName;
        return $showItem;
    }

    public function buildHiddenShowItemTca(): string
    {
        if (!$this->hasDisabledRestriction()) {
            return '';
        }
        $disabledFieldCapability = $this->tcaSchema->getCapability(TcaSchemaCapability::RestrictionDisabledField);
        $showItem = $disabledFieldCapability->getFieldName();
        return $showItem;
    }

    public function buildInternalDescriptionShowItemTca(): string
    {
        if (!$this->hasInternalDescription()) {
            return '';
        }
        $internalDescriptionFieldCapability = $this->tcaSchema->getCapability(TcaSchemaCapability::InternalDescription);
        $showItem = '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,' . $internalDescriptionFieldCapability->getFieldName();
        return $showItem;
    }
}
