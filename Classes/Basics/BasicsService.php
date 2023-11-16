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
 * @internal Not part of TYPO3's public API.
 */
class BasicsService
{
    public function __construct(protected readonly BasicsRegistry $basicsRegistry) {}

    public function applyBasics(array $yaml): array
    {
        if (is_array($yaml['basics'] ?? null)) {
            foreach ($yaml['basics'] as $basics) {
                $yaml['fields'] = $this->addBasicsToFields(
                    $yaml['fields'] ?? [],
                    $basics
                );
            }
        }
        $yaml['fields'] = $this->applyBasicsToSubFields($yaml['fields'] ?? []);

        return $yaml;
    }

    /**
     * Checks whether a basic is registered and append its fields after the given array.
     * If the basic is not registered, the given array is returned.
     *
     * @param array $fields fields from a content block
     * @param string $identifier identifier of the basic
     * @return array fields with the basic's fields added or just the fields from the content block
     */
    protected function addBasicsToFields(array $fields, string $identifier): array
    {
        if ($this->basicsRegistry->hasBasic($identifier)) {
            $fields = array_merge(
                $fields,
                $this->basicsRegistry->getBasic($identifier)->getFields()
            );
        }
        return $fields;
    }

    protected function applyBasicsToSubFields(array $fields): array
    {
        $newFields = [];
        foreach ($fields as $field) {
            if (is_array($field['fields'] ?? null)) {
                $field['fields'] = $this->applyBasicsToSubFields($field['fields']);
            }
            if (($field['type'] ?? '') === 'Basic') {
                foreach ($this->basicsRegistry->getBasic($field['identifier'])->getFields() as $basicsField) {
                    if (is_array($basicsField['fields'] ?? null)) {
                        $basicsField['fields'] = $this->applyBasicsToSubFields($basicsField['fields']);
                    }
                    $newFields[] = $basicsField;
                }
            } else {
                $newFields[] = $field;
            }
        }
        return $newFields;
    }
}
