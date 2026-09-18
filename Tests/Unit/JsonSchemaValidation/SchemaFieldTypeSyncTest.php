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

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Guards the JSON schema sources against drifting away from the Field Type
 * implementations.
 *
 * Every property a `*FieldType` class reads from the YAML `$settings` array must
 * be allowed by the matching schema branch in
 * `Build/JsonSchema/SchemaSources/FieldTypes/*.json`, and vice versa.
 *
 * Field Types, their settings, their schema branch and the centrally declared
 * properties are all derived by FieldTypeIntrospection, so adding a Field Type
 * needs no change here. What remains below is the knowledge that cannot be
 * derived: which properties a class other than the Field Type consumes.
 */
final class SchemaFieldTypeSyncTest extends UnitTestCase
{
    /**
     * Properties which are allowed by the schema but consumed by
     * ContentBlockCompiler instead of the Field Type class.
     *
     * @var array<string, string[]>
     */
    private const COMPILER_HANDLED_PROPERTIES = [
        'Collection' => [
            'table', 'group', 'typeName', 'labelField', 'fallbackLabelFields', 'typeField',
            'languageAware', 'workspaceAware', 'editLocking', 'restriction', 'softDelete',
            'trackCreationDate', 'trackUpdateDate', 'sortable', 'sortField',
            'internalDescription', 'rootLevelType', 'security', 'adminOnly', 'hideAtCopy',
            'appendLabelAtCopy', 'hideInUi', 'shareAcrossTables', 'shareAcrossFields',
            'fields', 'overrideType',
        ],
        'File' => [
            'overrideType',
        ],
        'FlexForm' => [
            'fields',
        ],
        'Palette' => ['label', 'description', 'fields'],
        'Tab' => ['label'],
        'Linebreak' => ['ignoreIfNotInPalette'],
    ];

    /**
     * Settings keys a Field Type class reads which are never written by the
     * user: the compiler injects them after parsing the YAML.
     *
     * @var array<string, string[]>
     */
    private const INTERNAL_PROPERTIES = [
        'FlexForm' => [
            // Injected by ContentBlockCompiler / FlexFormGenerator.
            'ds', 'flexFormDefinitions',
        ],
    ];

    /**
     * Field Types with no YAML surface of their own and therefore no schema
     * branch. ImageManipulation is not part of the `type` enum: it is only
     * reachable via useExistingField.
     */
    private const FIELD_TYPES_WITHOUT_SCHEMA = [
        'ImageManipulation',
    ];

    public static function fieldTypeDataProvider(): iterable
    {
        foreach (FieldTypeIntrospection::fieldTypes() as $name => $definition) {
            yield $name => [$name, $definition];
        }
    }

    public static function schemaBackedFieldTypeDataProvider(): iterable
    {
        $schemaFiles = FieldTypeIntrospection::schemaFileByFieldType();
        foreach (FieldTypeIntrospection::fieldTypes() as $name => $definition) {
            if (isset($schemaFiles[$name])) {
                yield $name => [$name, $definition, $schemaFiles[$name]];
            }
        }
    }

    /**
     * A newly added Field Type must either get a schema branch or be listed as
     * deliberately schema-less, so it cannot slip in unvalidated.
     */
    #[DataProvider('fieldTypeDataProvider')]
    #[Test]
    public function everyFieldTypeHasASchemaBranch(string $name, array $definition): void
    {
        $hasBranch = isset(FieldTypeIntrospection::schemaFileByFieldType()[$name]);

        if (in_array($name, self::FIELD_TYPES_WITHOUT_SCHEMA, true)) {
            self::assertFalse(
                $hasBranch,
                sprintf('Field Type "%s" is listed as schema-less but does have a schema branch.', $name),
            );
            return;
        }

        self::assertTrue(
            $hasBranch,
            sprintf(
                'Field Type "%s" (%s) has no schema branch. Add a "type" branch pointing at a schema source in '
                . 'Build/JsonSchema/SchemaSources/field-type.json, or list it in FIELD_TYPES_WITHOUT_SCHEMA '
                . 'if it has no YAML surface of its own.',
                $name,
                $definition['file'],
            ),
        );
    }

    #[DataProvider('schemaBackedFieldTypeDataProvider')]
    #[Test]
    public function everyImplementedSettingIsAllowedBySchema(string $name, array $definition, string $schemaFile): void
    {
        $ignored = array_merge(
            FieldTypeIntrospection::structuralProperties(),
            FieldTypeIntrospection::commonTraitProperties(),
            self::INTERNAL_PROPERTIES[$name] ?? [],
        );
        $missing = array_diff(
            $definition['settings'],
            FieldTypeIntrospection::schemaProperties($schemaFile),
            $ignored,
        );

        self::assertSame(
            [],
            array_values($missing),
            sprintf(
                'Field Type "%s" (%s) reads the setting(s) "%s" from YAML, but %s does not allow them. '
                . 'content-blocks:lint therefore rejects valid configuration. '
                . 'Add the propert(ies) to the schema source and re-run "npm run bundle" in Build/JsonSchema.',
                $name,
                $definition['file'],
                implode('", "', $missing),
                $schemaFile,
            ),
        );
    }

    #[DataProvider('schemaBackedFieldTypeDataProvider')]
    #[Test]
    public function everySchemaPropertyIsReadByTheImplementation(string $name, array $definition, string $schemaFile): void
    {
        $ignored = array_merge(
            FieldTypeIntrospection::structuralProperties(),
            FieldTypeIntrospection::commonTraitProperties(),
            self::COMPILER_HANDLED_PROPERTIES[$name] ?? [],
        );
        $unused = array_diff(
            FieldTypeIntrospection::schemaProperties($schemaFile),
            $definition['settings'],
            $ignored,
        );

        self::assertSame(
            [],
            array_values($unused),
            sprintf(
                '%s allows the propert(ies) "%s" for Field Type "%s", but %s never reads them. '
                . 'They are silently ignored at runtime. Either implement them or remove them from the schema source.',
                $schemaFile,
                implode('", "', $unused),
                $name,
                $definition['file'],
            ),
        );
    }

    /**
     * The `type` branches and the schema sources on disk must describe the same
     * set: a branch pointing at a missing file breaks validation silently, and
     * a file no branch points at is dead weight.
     */
    #[Test]
    public function typeBranchesAndSchemaSourcesMatch(): void
    {
        $directory = FieldTypeIntrospection::schemaSourcePath() . '/FieldTypes/';
        $referenced = array_values(FieldTypeIntrospection::schemaFileByFieldType());
        $existing = array_map('basename', glob($directory . '*.json') ?: []);

        self::assertSame(
            [],
            array_values(array_diff($referenced, $existing)),
            'A "type" branch references (a) schema source(s) which do(es) not exist in ' . $directory . '.',
        );
        self::assertSame(
            [],
            array_values(array_diff($existing, $referenced)),
            'Schema source(s) exist which no "type" branch references. They are dead files.',
        );
    }
}
