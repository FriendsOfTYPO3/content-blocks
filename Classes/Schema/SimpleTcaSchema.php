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

namespace TYPO3\CMS\ContentBlocks\Schema;

use TYPO3\CMS\ContentBlocks\Definition\Capability\SystemFieldPalettesInterface;
use TYPO3\CMS\ContentBlocks\Schema\Capability\FieldCapability;
use TYPO3\CMS\ContentBlocks\Schema\Capability\LanguageAwareSchemaCapability;
use TYPO3\CMS\ContentBlocks\Schema\Exception\UndefinedFieldException;
use TYPO3\CMS\ContentBlocks\Schema\Field\FieldCollection;
use TYPO3\CMS\ContentBlocks\Schema\Field\FieldTypeInterface;

/**
 * @internal Not part of TYPO3's public API.
 */
final class SimpleTcaSchema implements SystemFieldPalettesInterface
{
    public function __construct(
        protected readonly string $name,
        protected readonly FieldCollection $fields,
        protected readonly FieldCollection $systemFields,
        protected readonly array $schemaConfiguration,
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function hasField(string $fieldName): bool
    {
        return isset($this->fields[$fieldName]);
    }
    public function getField(string $fieldName): FieldTypeInterface
    {
        if (!$this->hasField($fieldName)) {
            throw new UndefinedFieldException('The field "' . $fieldName . '" is not defined for the TCA schema "' . $this->name . '".', 1661615151);
        }
        return $this->fields[$fieldName];
    }

    public function isLanguageAware(): bool
    {
        return ($this->schemaConfiguration['languageField'] ?? null)
            && isset($this->fields[$this->schemaConfiguration['languageField']])
            && ($this->schemaConfiguration['transOrigPointerField'] ?? null)
            && isset($this->fields[$this->schemaConfiguration['transOrigPointerField']]);
    }

    public function hasCapability(string $capability): bool
    {
        return match ($capability) {
            'editlock' => isset($this->schemaConfiguration['editlock']) && isset($this->fields[$this->schemaConfiguration['editlock']]),
            'internalDescription' => isset($this->schemaConfiguration['descriptionColumn']) && isset($this->fields[$this->schemaConfiguration['descriptionColumn']]),
            'language' => $this->isLanguageAware(),
            'restriction.disabled' => isset($this->systemFields['disabled']),
            'restriction.starttime' => isset($this->systemFields['starttime']),
            'restriction.endtime' => isset($this->systemFields['endtime']),
            'restriction.usergroup' => isset($this->systemFields['fe_group']),
            default => false,
        };
    }

    public function getCapability(string $capability): Capability\SchemaCapabilityInterface
    {
        $result = match ($capability) {
            'editlock' => new Capability\FieldCapability($this->fields[$this->schemaConfiguration['editlock']]),
            'internalDescription' => new Capability\FieldCapability($this->fields[$this->schemaConfiguration['descriptionColumn']]),
            'language' => $this->buildLanguageCapability(),
            'restriction.disabled' => new Capability\FieldCapability($this->systemFields['disabled']),
            'restriction.starttime' => new Capability\FieldCapability($this->systemFields['starttime']),
            'restriction.endtime' => new Capability\FieldCapability($this->systemFields['endtime']),
            'restriction.usergroup' => new Capability\FieldCapability($this->systemFields['fe_group']),
            default => null,
        };
        if ($result === null) {
            throw new \InvalidArgumentException('Invalid Capability', 1662580936);
        }
        return $result;
    }

    protected function buildLanguageCapability(): Capability\LanguageAwareSchemaCapability
    {
        $languageField = $this->fields[$this->schemaConfiguration['languageField']];
        $transOrigPointerField = $this->fields[$this->schemaConfiguration['transOrigPointerField']];
        $translationSourceField = isset($this->schemaConfiguration['translationSource']) ? $this->fields[$this->schemaConfiguration['translationSource']] : null;
        $diffSourceField = isset($this->schemaConfiguration['transOrigDiffSourceField']) ? $this->fields[$this->schemaConfiguration['transOrigDiffSourceField']] : null;
        return new Capability\LanguageAwareSchemaCapability(
            $languageField,
            $transOrigPointerField,
            $translationSourceField,
            $diffSourceField
        );
    }

    public function getTypeField(): ?FieldTypeInterface
    {
        if (isset($this->schemaConfiguration['type']) && isset($this->fields[$this->schemaConfiguration['type']])) {
            return $this->fields[$this->schemaConfiguration['type']];
        }
        return null;
    }

    public function hasAccessPalette(): bool
    {
        return $this->hasCapability('restriction.starttime')
            || $this->hasCapability('restriction.endtime')
            || $this->hasCapability('restriction.usergroup')
            || $this->hasCapability('editlock');
    }

    public function hasDisabledRestriction(): bool
    {
        return $this->hasCapability('restriction.disabled');
    }

    public function hasInternalDescription(): bool
    {
        return $this->hasCapability('internalDescription');
    }

    public function buildAccessShowItemTca(): string
    {
        if (!$this->hasAccessPalette()) {
            return '';
        }
        $access = [];
        if ($this->hasCapability('restriction.starttime')) {
            /** @var FieldCapability $startTimeFieldCapability */
            $startTimeFieldCapability = $this->getCapability('restriction.starttime');
            $access[] = $startTimeFieldCapability->getFieldName() . ';LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:starttime_formlabel';
        }
        if ($this->hasCapability('restriction.endtime')) {
            /** @var FieldCapability $endTimeFieldCapability */
            $endTimeFieldCapability = $this->getCapability('restriction.endtime');
            $access[] = $endTimeFieldCapability->getFieldName() . ';LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:endtime_formlabel';
        }
        if ($this->hasCapability('restriction.starttime') || $this->hasCapability('restriction.endtime')) {
            $access[] = '--linebreak--';
        }
        if ($this->hasCapability('restriction.usergroup')) {
            /** @var FieldCapability $userGroupCapability */
            $userGroupCapability = $this->getCapability('restriction.usergroup');
            $access[] = $userGroupCapability->getFieldName() . ';LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:fe_group_formlabel';
            $access[] = '--linebreak--';
        }
        if ($this->hasCapability('editlock')) {
            /** @var FieldCapability $editLockCapability */
            $editLockCapability = $this->getCapability('editlock');
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
        if (!$this->isLanguageAware()) {
            return '';
        }
        /** @var LanguageAwareSchemaCapability $languageCapability */
        $languageCapability = $this->getCapability('language');
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
        $disabledFieldCapability = $this->getCapability('restriction.disabled');
        $showItem = $disabledFieldCapability->getFieldName();
        return $showItem;
    }

    public function buildInternalDescriptionShowItemTca(): string
    {
        if (!$this->hasInternalDescription()) {
            return '';
        }
        /** @var FieldCapability $internalDescriptionFieldCapability */
        $internalDescriptionFieldCapability = $this->getCapability('internalDescription');
        $showItem = '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,' . $internalDescriptionFieldCapability->getFieldName();
        return $showItem;
    }
}
