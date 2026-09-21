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

namespace TYPO3\CMS\ContentBlocks\Tests\Unit\JsonSchemaValidation;

/**
 * Derives the Field Type <-> JSON schema <-> documentation relations from the
 * sources themselves, so that adding a Field Type requires no bookkeeping here.
 *
 * Shared by SchemaFieldTypeSyncTest and Build/Scripts/checkDocumentationSync.php.
 *
 * @internal Not part of TYPO3's public API.
 */
final class FieldTypeIntrospection
{
    /**
     * Traits which read YAML settings on behalf of the Field Type using them.
     * WithCustomProperties is handled separately: it reads a fixed allow list
     * instead of literal $settings keys.
     */
    private const SETTINGS_TRAITS = [
        'WithCommonProperties',
        'WithNullableProperty',
    ];

    public static function rootPath(): string
    {
        return dirname(__DIR__, 3);
    }

    public static function schemaSourcePath(): string
    {
        return self::rootPath() . '/Build/JsonSchema/SchemaSources';
    }

    /**
     * Properties declared centrally in `field-type.json` and not handled by any
     * Field Type class: the compiler consumes them before the class is reached.
     *
     * @return string[]
     */
    public static function structuralProperties(): array
    {
        return array_values(array_diff(self::sharedSchemaProperties(), self::commonTraitSettings()));
    }

    /**
     * Properties the WithCommonProperties trait reads for every Field Type,
     * but which core TCA only evaluates for a subset of TCA types. Per-type
     * schemas therefore whitelist them deliberately, which makes "read by the
     * class but absent from the schema" the expected state elsewhere.
     *
     * @return string[]
     */
    public static function commonTraitProperties(): array
    {
        return array_values(array_diff(self::commonTraitSettings(), self::sharedSchemaProperties()));
    }

    /**
     * Everything documented centrally in
     * Documentation/YamlReference/FieldTypes/Index.rst rather than on each
     * individual Field Type page.
     *
     * @return string[]
     */
    public static function centrallyDefinedProperties(): array
    {
        return array_values(array_unique(array_merge(
            self::sharedSchemaProperties(),
            self::commonTraitSettings(),
        )));
    }

    /**
     * @return string[]
     */
    private static function sharedSchemaProperties(): array
    {
        $schema = self::decode(self::schemaSourcePath() . '/field-type.json');
        return array_keys($schema['properties'] ?? []);
    }

    /**
     * @return string[]
     */
    private static function commonTraitSettings(): array
    {
        return self::settingsKeysOf(self::rootPath() . '/Classes/FieldType/WithCommonProperties.php');
    }

    /**
     * Maps a Field Type name to its schema source file, read from the
     * `if type === X then $ref` branches in the schema sources. Field Types
     * without such a branch (for example ImageManipulation, which is only
     * reachable via useExistingField) are absent from the map.
     *
     * @return array<string, string> e.g. ['SelectText' => 'select-text.json']
     */
    public static function schemaFileByFieldType(): array
    {
        $map = [];
        $sources = array_merge(
            [self::schemaSourcePath() . '/field-type.json'],
            glob(self::schemaSourcePath() . '/FieldTypes/*.json') ?: [],
        );
        foreach ($sources as $source) {
            self::collectTypeBranches(self::decode($source), $map);
        }
        ksort($map);
        return $map;
    }

    /**
     * Walks a decoded schema and records every `{if: {type: const X}, then: {$ref: Y}}` pair.
     *
     * @param array<string, string> $map
     */
    private static function collectTypeBranches(mixed $node, array &$map): void
    {
        if (!is_array($node)) {
            return;
        }
        $type = $node['if']['properties']['type']['const'] ?? null;
        $ref = $node['then']['$ref'] ?? null;
        if (is_string($type) && is_string($ref)) {
            $map[$type] = basename($ref);
        }
        foreach ($node as $child) {
            self::collectTypeBranches($child, $map);
        }
    }

    /**
     * Every Field Type implementation with the YAML settings keys it reads,
     * including those read by the traits it uses.
     *
     * @return array<string, array{file: string, settings: string[]}>
     */
    public static function fieldTypes(): array
    {
        $directory = self::rootPath() . '/Classes/FieldType';
        $traitSettings = [];
        foreach (self::SETTINGS_TRAITS as $trait) {
            $traitSettings[$trait] = self::settingsKeysOf($directory . '/' . $trait . '.php');
        }
        $traitSettings['WithCustomProperties'] = self::customPropertyAllowList($directory . '/WithCustomProperties.php');

        $fieldTypes = [];
        foreach (glob($directory . '/*FieldType.php') ?: [] as $file) {
            $source = file_get_contents($file);
            // Matches both #[FieldType(name: 'Text', ...)] and
            // #[FieldType(name: SpecialFieldType::TAB->value, ...)].
            if (preg_match("/#\[FieldType\(name:\s*(?:'([^']+)'|SpecialFieldType::(\w+)->value)/", $source, $matches) !== 1) {
                continue;
            }
            $name = $matches[1] !== '' ? $matches[1] : ucfirst(strtolower($matches[2]));
            $settings = self::settingsKeysOf($file);
            foreach ($traitSettings as $trait => $keys) {
                if (preg_match('/\buse\s+' . $trait . '\s*;/', $source) === 1) {
                    $settings = array_merge($settings, $keys);
                }
            }
            $fieldTypes[$name] = [
                'file' => basename($file),
                'settings' => array_values(array_unique($settings)),
            ];
        }
        ksort($fieldTypes);
        return $fieldTypes;
    }

    /**
     * @return string[]
     */
    public static function schemaProperties(string $schemaFile): array
    {
        $schema = self::decode(self::schemaSourcePath() . '/FieldTypes/' . $schemaFile);
        return array_keys($schema['properties'] ?? []);
    }

    /**
     * Collects the confval names of a reStructuredText page. Nested confvals
     * such as "behaviour.allowLanguageSynchronization" are reduced to their
     * root property.
     *
     * @return string[]
     */
    public static function documentedProperties(string $page): array
    {
        if (preg_match_all('/^\.\.\s+confval::\s*(\S+)/m', file_get_contents($page), $matches) === 0) {
            return [];
        }
        return array_values(array_unique(array_map(
            static fn(string $name): string => explode('.', $name)[0],
            $matches[1],
        )));
    }

    /**
     * The YAML keys a file reads from its $settings array.
     *
     * @return string[]
     */
    private static function settingsKeysOf(string $file): array
    {
        $source = file_get_contents($file);
        $keys = [];
        if (preg_match_all('/\$settings\s*\[\s*[\'"]([^\'"]+)[\'"]\s*\]/', $source, $matches) > 0) {
            $keys = array_merge($keys, $matches[1]);
        }
        if (preg_match_all('/array_key_exists\s*\(\s*[\'"]([^\'"]+)[\'"]\s*,\s*\$settings/', $source, $matches) > 0) {
            $keys = array_merge($keys, $matches[1]);
        }
        return array_values(array_unique($keys));
    }

    /**
     * WithCustomProperties passes through a hardcoded allow list rather than
     * reading $settings keys literally.
     *
     * @return string[]
     */
    private static function customPropertyAllowList(string $file): array
    {
        $source = file_get_contents($file);
        if (preg_match('/\$allowedCustomProperties\s*=\s*\[(.*?)\]/s', $source, $matches) !== 1) {
            return [];
        }
        preg_match_all('/[\'"]([^\'"]+)[\'"]/', $matches[1], $entries);
        return $entries[1];
    }

    /**
     * @return array<mixed>
     */
    private static function decode(string $file): array
    {
        return json_decode(file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);
    }
}
