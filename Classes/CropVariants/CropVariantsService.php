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

namespace TYPO3\CMS\ContentBlocks\CropVariants;

/**
 * Contains main logic for Crop Variant replacement. Inside the `cropVariants`
 * option of a field, each Crop Variant can either be defined inline or as a
 * string, which references the identifier of a central Crop Variant. These
 * references are replaced by the actual configuration. Fields are processed on
 * every nesting level: Palettes, Collections, FlexForm sheets, as well as Type
 * Overrides. FlexForm sections are skipped, as they cannot contain File fields.
 *
 * @internal Not part of TYPO3's public API.
 */
readonly class CropVariantsService
{
    public function applyCropVariants(CropVariantsRegistry $cropVariantsRegistry, array $yaml): array
    {
        if (is_array($yaml['fields'] ?? null)) {
            $yaml['fields'] = $this->applyCropVariantsToFields($cropVariantsRegistry, $yaml['fields']);
        }
        return $yaml;
    }

    protected function applyCropVariantsToFields(CropVariantsRegistry $cropVariantsRegistry, array $fields): array
    {
        foreach ($fields as $index => $field) {
            if (is_array($field)) {
                $fields[$index] = $this->applyCropVariantsToField($cropVariantsRegistry, $field);
            }
        }
        return $fields;
    }

    protected function applyCropVariantsToField(CropVariantsRegistry $cropVariantsRegistry, array $field): array
    {
        if (is_array($field['cropVariants'] ?? null)) {
            $field['cropVariants'] = $this->resolveReferences($cropVariantsRegistry, $field['cropVariants']);
        }
        if (is_array($field['fields'] ?? null)) {
            $field['fields'] = $this->applyCropVariantsToFields($cropVariantsRegistry, $field['fields']);
        }
        if (is_array($field['overrideType'] ?? null)) {
            $field['overrideType'] = $this->applyCropVariantsToTypeOverrides($cropVariantsRegistry, $field['overrideType']);
        }
        return $field;
    }

    protected function applyCropVariantsToTypeOverrides(CropVariantsRegistry $cropVariantsRegistry, array $typeOverrides): array
    {
        foreach ($typeOverrides as $type => $fields) {
            if (is_array($fields)) {
                $typeOverrides[$type] = $this->applyCropVariantsToFields($cropVariantsRegistry, $fields);
            }
        }
        return $typeOverrides;
    }

    protected function resolveReferences(CropVariantsRegistry $cropVariantsRegistry, array $cropVariants): array
    {
        foreach ($cropVariants as $cropVariantName => $cropVariant) {
            if (is_string($cropVariant)) {
                $cropVariants[$cropVariantName] = $cropVariantsRegistry->getCropVariant($cropVariant)->getConfiguration();
            }
        }
        return $cropVariants;
    }
}
