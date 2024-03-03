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

use TYPO3\CMS\ContentBlocks\Schema\Capability\FieldCapability;
use TYPO3\CMS\ContentBlocks\Schema\Capability\LanguageAwareSchemaCapability;
use TYPO3\CMS\ContentBlocks\Schema\SimpleTcaSchema;

/**
 * @internal Not part of TYPO3's public API.
 */
final class NativeTableCapabilityProxy implements SystemFieldPalettesInterface
{
    public function __construct(
        private readonly SimpleTcaSchema $tcaSchema
    ) {}

    public function hasAccessPalette(): bool
    {
        return $this->tcaSchema->hasCapability('restriction.starttime')
            || $this->tcaSchema->hasCapability('restriction.endtime')
            || $this->tcaSchema->hasCapability('restriction.usergroup')
            || $this->tcaSchema->hasCapability('editlock');
    }

    public function hasDisabledRestriction(): bool
    {
        return $this->tcaSchema->hasCapability('restriction.disabled');
    }

    public function hasInternalDescription(): bool
    {
        return $this->tcaSchema->hasCapability('internalDescription');
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
        if ($this->tcaSchema->hasCapability('restriction.starttime')) {
            /** @var FieldCapability $startTimeFieldCapability */
            $startTimeFieldCapability = $this->tcaSchema->getCapability('restriction.starttime');
            $access[] = $startTimeFieldCapability->getFieldName() . ';LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:starttime_formlabel';
        }
        if ($this->tcaSchema->hasCapability('restriction.endtime')) {
            /** @var FieldCapability $endTimeFieldCapability */
            $endTimeFieldCapability = $this->tcaSchema->getCapability('restriction.endtime');
            $access[] = $endTimeFieldCapability->getFieldName() . ';LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:endtime_formlabel';
        }
        if ($this->tcaSchema->hasCapability('restriction.starttime') || $this->tcaSchema->hasCapability('restriction.endtime')) {
            $access[] = '--linebreak--';
        }
        if ($this->tcaSchema->hasCapability('restriction.usergroup')) {
            /** @var FieldCapability $userGroupCapability */
            $userGroupCapability = $this->tcaSchema->getCapability('restriction.usergroup');
            $access[] = $userGroupCapability->getFieldName() . ';LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:fe_group_formlabel';
            $access[] = '--linebreak--';
        }
        if ($this->tcaSchema->hasCapability('editlock')) {
            /** @var FieldCapability $editLockCapability */
            $editLockCapability = $this->tcaSchema->getCapability('editlock');
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
        /** @var LanguageAwareSchemaCapability $languageCapability */
        $languageCapability = $this->tcaSchema->getCapability('language');
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
        /** @var FieldCapability $disabledFieldCapability */
        $disabledFieldCapability = $this->tcaSchema->getCapability('restriction.disabled');
        $showItem = $disabledFieldCapability->getFieldName();
        return $showItem;
    }

    public function buildInternalDescriptionShowItemTca(): string
    {
        if (!$this->hasInternalDescription()) {
            return '';
        }
        /** @var FieldCapability $internalDescriptionFieldCapability */
        $internalDescriptionFieldCapability = $this->tcaSchema->getCapability('internalDescription');
        $showItem = '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,' . $internalDescriptionFieldCapability->getFieldName();
        return $showItem;
    }
}
