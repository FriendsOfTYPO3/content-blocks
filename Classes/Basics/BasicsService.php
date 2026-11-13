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

namespace TYPO3\CMS\ContentBlocks\Basics;

/**
 * Contains main logic for Basic replacement. Basics are simply added/replaced
 * in the config.yaml fields array. Basics are used either globally
 * in the `basics` array (appended at the end of fields) or locally as the
 * field Type `Basic` (in place replacement). Nesting of Basics is allowed for
 * a maximum nesting level of 99.
 *
 * @internal Not part of TYPO3's public API.
 */
readonly class BasicsService
{
    /**
     * Options, which can be changed per Content Block with `basicsOverrides`.
     */
    private const OVERRIDABLE_OPTIONS = [
        'label',
        'description',
        'default',
        'required',
        'readOnly',
        'minitems',
        'maxitems',
    ];

    public function applyBasics(BasicsRegistry $basicsRegistry, array $yaml): array
    {
        if (is_array($yaml['basics'] ?? null)) {
            foreach ($yaml['basics'] as $basics) {
                $yaml['fields'] = $this->addBasicsToFields(
                    $basicsRegistry,
                    $yaml['fields'] ?? [],
                    $basics
                );
            }
        }
        $yaml['fields'] = $this->applyBasicsToSubFields($basicsRegistry, $yaml['fields'] ?? []);
        if (is_array($yaml['basicsOverrides'] ?? null)) {
            $yaml['fields'] = $this->applyBasicsOverrides(
                $yaml['fields'],
                $yaml['basicsOverrides'],
                (string)($yaml['name'] ?? '')
            );
        }
        return $yaml;
    }

    /**
     * Overrides single options of fields after all Basics are applied. This is
     * meant for fields added by Basics, which can't be changed otherwise. Only
     * fields on root level or inside a root level Palette can be targeted, so
     * the overrides are a flat map keyed by field identifier.
     *
     * @param array<string|int, mixed> $basicsOverrides
     */
    protected function applyBasicsOverrides(array $fields, array $basicsOverrides, string $name): array
    {
        foreach ($basicsOverrides as $identifier => $options) {
            $identifier = (string)$identifier;
            $this->validateBasicsOverride($fields, $identifier, $options, $name);
            $fields = $this->overrideField($fields, $identifier, $options);
        }

        return $fields;
    }

    protected function validateBasicsOverride(array $fields, string $identifier, mixed $options, string $name): void
    {
        if (is_array($options) === false) {
            throw new \InvalidArgumentException(
                'The basicsOverrides entry "' . $identifier . '" in Content Block "' . $name . '" must be a map of options.',
                1794570535
            );
        }
        $unsupportedOptions = array_diff(array_keys($options), self::OVERRIDABLE_OPTIONS);
        if ($unsupportedOptions !== []) {
            throw new \InvalidArgumentException(
                'The basicsOverrides entry "' . $identifier . '" in Content Block "' . $name . '" contains unsupported options "'
                . implode('", "', $unsupportedOptions) . '". Supported options are "' . implode('", "', self::OVERRIDABLE_OPTIONS) . '".',
                1794570536
            );
        }
        if ($this->hasOverridableField($fields, $identifier) === false) {
            throw new \InvalidArgumentException(
                'The basicsOverrides entry "' . $identifier . '" in Content Block "' . $name . '" does not match any field on root level or inside a root level Palette.',
                1794570537
            );
        }
    }

    protected function hasOverridableField(array $fields, string $identifier): bool
    {
        foreach ($fields as $field) {
            if ($this->isPalette($field) && $this->hasOverridableField($field['fields'], $identifier)) {
                return true;
            }
            if ($this->isOverridableField($field, $identifier)) {
                return true;
            }
        }

        return false;
    }

    protected function overrideField(array $fields, string $identifier, array $options): array
    {
        foreach ($fields as $index => $field) {
            if ($this->isPalette($field)) {
                $fields[$index]['fields'] = $this->overrideField($field['fields'], $identifier, $options);
                continue;
            }
            if ($this->isOverridableField($field, $identifier)) {
                $fields[$index] = array_replace($field, $options);
            }
        }

        return $fields;
    }

    protected function isPalette(array $field): bool
    {
        $isPalette = ($field['type'] ?? '') === 'Palette' && is_array($field['fields'] ?? null);

        return $isPalette;
    }

    protected function isOverridableField(array $field, string $identifier): bool
    {
        $isOverridableField = ($field['identifier'] ?? null) === $identifier
            && in_array($field['type'] ?? '', ['Palette', 'Tab'], true) === false;

        return $isOverridableField;
    }

    /**
     * Checks whether a basic is registered and append its fields after the given array.
     * If the Basic is not registered, the given array is returned.
     *
     * @param array $fields fields from a Content Block
     * @param string $identifier identifier of the Basic
     * @return array fields with the basic's fields added or just the fields from the Content Block
     */
    protected function addBasicsToFields(BasicsRegistry $basicsRegistry, array $fields, string $identifier): array
    {
        if ($basicsRegistry->hasBasic($identifier)) {
            $fields = array_merge(
                $fields,
                $basicsRegistry->getBasic($identifier)->getFields()
            );
        }
        return $fields;
    }

    protected function applyBasicsToSubFields(BasicsRegistry $basicsRegistry, array $fields, int $depth = 0): array
    {
        if ($depth === 99) {
            throw new \RuntimeException('Infinite loop in Basics processing detected.', 1711291137);
        }
        $newFields = [];
        foreach ($fields as $field) {
            if (is_array($field['fields'] ?? null)) {
                $field['fields'] = $this->applyBasicsToSubFields($basicsRegistry, $field['fields'], $depth);
            }
            if (($field['type'] ?? '') === 'Basic') {
                $basic = $basicsRegistry->getBasic($field['identifier']);
                $appliedFields = $this->applyBasicsToSubFields($basicsRegistry, $basic->getFields(), $depth + 1);
                $newFields = array_merge($newFields, $appliedFields);
            } else {
                $newFields[] = $field;
            }
        }
        return $newFields;
    }
}
