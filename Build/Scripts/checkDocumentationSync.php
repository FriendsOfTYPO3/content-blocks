#!/usr/bin/env php
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

/**
 * Reports properties which the JSON schema allows for a Field Type but which are
 * not documented on the corresponding page in
 * Documentation/YamlReference/FieldTypes/, and vice versa.
 *
 * Field Types, their schema branch and the centrally documented properties are
 * derived by FieldTypeIntrospection, so adding a Field Type needs no change
 * here.
 *
 * This is a report only. Run it after changing
 * a schema source to see what is left to document.
 *
 * Usage:
 *   php Build/Scripts/checkDocumentationSync.php
 *   php Build/Scripts/checkDocumentationSync.php --fail-on-drift
 *
 * The schema <-> implementation direction is enforced by
 * Tests/Unit/JsonSchemaValidation/SchemaFieldTypeSyncTest.php.
 */

use TYPO3\CMS\ContentBlocks\Tests\Unit\JsonSchemaValidation\FieldTypeIntrospection;

$root = dirname(__DIR__, 2);
require $root . '/.Build/vendor/autoload.php';

$documentationDirectory = $root . '/Documentation/YamlReference/FieldTypes';
$failOnDrift = in_array('--fail-on-drift', $argv, true);

/**
 * Documented concepts which are not YAML properties of the field itself and
 * therefore never appear in the schema.
 */
$documentationOnlyProperties = [
    'allowedCustomProperties',
];

/**
 * A Collection also defines the Record Type of its child table. Those
 * properties are documented under
 * Documentation/YamlReference/ContentTypes/RecordTypes/ rather than repeated on
 * the Collection page, so they are read from there.
 */
$recordTypeProperties = FieldTypeIntrospection::documentedProperties(
    $root . '/Documentation/YamlReference/ContentTypes/RecordTypes/Index.rst',
);

$ignoredEverywhere = array_merge(
    FieldTypeIntrospection::centrallyDefinedProperties(),
    $documentationOnlyProperties,
);

$drift = 0;
$reports = [];
foreach (FieldTypeIntrospection::schemaFileByFieldType() as $name => $schemaFile) {
    $page = $documentationDirectory . '/' . $name . '/Index.rst';
    if (!is_file($page)) {
        $reports[$name] = ['    no documentation page at Documentation/YamlReference/FieldTypes/' . $name . '/Index.rst'];
        $drift++;
        continue;
    }

    $ignored = $ignoredEverywhere;
    if ($name === 'Collection') {
        $ignored = array_merge($ignored, $recordTypeProperties);
    }

    $schemaProperties = FieldTypeIntrospection::schemaProperties($schemaFile);
    $documented = FieldTypeIntrospection::documentedProperties($page);

    $lines = [];
    $undocumented = array_diff($schemaProperties, $documented, $ignored);
    if ($undocumented !== []) {
        $lines[] = '    allowed by the schema but not documented: ' . implode(', ', $undocumented);
    }
    $unknown = array_diff($documented, $schemaProperties, $ignored);
    if ($unknown !== []) {
        $lines[] = '    documented but not allowed by the schema: ' . implode(', ', $unknown);
    }
    if ($lines !== []) {
        $reports[$name] = $lines;
        $drift += count($lines);
    }
}

if ($reports === []) {
    echo 'Documentation and JSON schema are in sync.' . PHP_EOL;
    exit(0);
}

echo 'Documentation drift report' . PHP_EOL;
echo str_repeat('=', 26) . PHP_EOL . PHP_EOL;
foreach ($reports as $name => $lines) {
    echo $name . PHP_EOL;
    echo implode(PHP_EOL, $lines) . PHP_EOL . PHP_EOL;
}
echo sprintf('%d finding(s) in %d Field Type(s).', $drift, count($reports)) . PHP_EOL;

exit($failOnDrift ? 1 : 0);
