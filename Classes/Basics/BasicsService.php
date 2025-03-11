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
 * a maximum nesting level of 8.
 *
 * @internal Not part of TYPO3's public API.
 */
readonly class BasicsService
{
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
        return $yaml;
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
                $field['fields'] = $this->applyBasicsToSubFields($basicsRegistry, $field['fields']);
            }
            if (($field['type'] ?? '') === 'Basic') {
                $basic = $basicsRegistry->getBasic($field['identifier']);
                $appliedFields = $this->applyBasicsToSubFields($basicsRegistry, $basic->getFields(), ++$depth);
                $newFields = array_merge($newFields, $appliedFields);
            } else {
                $newFields[] = $field;
            }
        }
        return $newFields;
    }
}
