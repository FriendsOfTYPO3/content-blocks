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

namespace TYPO3\CMS\ContentBlocks\Tests\Unit\Definition\Factory;

use TYPO3\CMS\ContentBlocks\Definition\Factory\PrefixType;
use TYPO3\CMS\ContentBlocks\Definition\Factory\TableDefinitionCollectionFactory;
use TYPO3\CMS\ContentBlocks\Generator\FlexFormGenerator;
use TYPO3\CMS\ContentBlocks\Generator\TcaGenerator;
use TYPO3\CMS\ContentBlocks\Loader\LoadedContentBlock;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\ContentBlocks\Tests\Unit\Fixtures\NoopLanguageFileRegistry;
use TYPO3\CMS\ContentBlocks\Tests\Unit\Fixtures\TestSystemExtensionAvailability;
use TYPO3\CMS\Core\EventDispatcher\NoopEventDispatcher;
use TYPO3\CMS\Core\Preparations\TcaPreparation;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class DisplayCondPrefixEvaluationTest extends UnitTestCase
{
    public static function createPrefixedSingleDisplayCondDataProvider(): iterable
    {
        yield 'simple displayCond, global full prefix on (string)' => [
            'contentBlockName' => 'bar/foo',
            'prefixType' => PrefixType::FULL,
            'identifier' => 'aField',
            'parsedIdentifier' => 'bar_foo_aField',
            'prefixAllFields' => true,
            'prefixSingleField' => false,
            'displayCond' => 'FIELD:aField:=:aValue',
            'expected' => 'FIELD:bar_foo_aField:=:aValue',
        ];

        yield 'simple displayCond, global vendor prefix on (string)' => [
            'contentBlockName' => 'bar/foo',
            'prefixType' => PrefixType::VENDOR,
            'identifier' => 'aField',
            'parsedIdentifier' => 'bar_aField',
            'prefixAllFields' => true,
            'prefixSingleField' => false,
            'displayCond' => 'FIELD:aField:=:aValue',
            'expected' => 'FIELD:bar_aField:=:aValue',
        ];

        yield 'simple displayCond, unprefixed (string)' => [
            'contentBlockName' => 'bar/foo',
            'prefixType' => PrefixType::FULL,
            'identifier' => 'aField',
            'parsedIdentifier' => 'aField',
            'prefixAllFields' => false,
            'prefixSingleField' => false,
            'displayCond' => 'FIELD:aField:=:aValue',
            'expected' => 'FIELD:aField:=:aValue',
        ];

        yield 'simple displayCond, single full prefix on (string)' => [
            'contentBlockName' => 'bar/foo',
            'prefixType' => PrefixType::FULL,
            'identifier' => 'aField',
            'parsedIdentifier' => 'bar_foo_aField',
            'prefixAllFields' => false,
            'prefixSingleField' => true,
            'displayCond' => 'FIELD:aField:=:aValue',
            'expected' => 'FIELD:bar_foo_aField:=:aValue',
        ];

        yield 'simple displayCond, single vendor prefix on (string)' => [
            'contentBlockName' => 'bar/foo',
            'prefixType' => PrefixType::VENDOR,
            'identifier' => 'aField',
            'parsedIdentifier' => 'bar_aField',
            'prefixAllFields' => false,
            'prefixSingleField' => true,
            'displayCond' => 'FIELD:aField:=:aValue',
            'expected' => 'FIELD:bar_aField:=:aValue',
        ];

        yield 'simple foreign displayCond, global full prefix on (string)' => [
            'contentBlockName' => 'bar/foo',
            'prefixType' => PrefixType::FULL,
            'identifier' => 'aField',
            'parsedIdentifier' => 'bar_foo_aField',
            'prefixAllFields' => true,
            'prefixSingleField' => false,
            'displayCond' => 'FIELD:cField:=:aValue',
            'expected' => 'FIELD:bar_foo_cField:=:aValue',
        ];

        yield 'simple foreign displayCond, global vendor prefix on (string)' => [
            'contentBlockName' => 'bar/foo',
            'prefixType' => PrefixType::VENDOR,
            'identifier' => 'aField',
            'parsedIdentifier' => 'bar_aField',
            'prefixAllFields' => true,
            'prefixSingleField' => false,
            'displayCond' => 'FIELD:cField:=:aValue',
            'expected' => 'FIELD:bar_cField:=:aValue',
        ];

        yield 'simple foreign displayCond, unprefixed (string)' => [
            'contentBlockName' => 'bar/foo',
            'prefixType' => PrefixType::FULL,
            'identifier' => 'aField',
            'parsedIdentifier' => 'aField',
            'prefixAllFields' => false,
            'prefixSingleField' => false,
            'displayCond' => 'FIELD:cField:=:aValue',
            'expected' => 'FIELD:cField:=:aValue',
        ];

        yield 'simple foreign displayCond, single full prefix on (string)' => [
            'contentBlockName' => 'bar/foo',
            'prefixType' => PrefixType::FULL,
            'identifier' => 'aField',
            'parsedIdentifier' => 'bar_foo_aField',
            'prefixAllFields' => false,
            'prefixSingleField' => true,
            'displayCond' => 'FIELD:cField:=:aValue',
            'expected' => 'FIELD:bar_foo_cField:=:aValue',
        ];

        yield 'simple foreign displayCond, single vendor prefix on (string)' => [
            'contentBlockName' => 'bar/foo',
            'prefixType' => PrefixType::VENDOR,
            'identifier' => 'aField',
            'parsedIdentifier' => 'bar_aField',
            'prefixAllFields' => false,
            'prefixSingleField' => true,
            'displayCond' => 'FIELD:cField:=:aValue',
            'expected' => 'FIELD:bar_cField:=:aValue',
        ];

        yield 'different displayCond, global full prefix on (string)' => [
            'contentBlockName' => 'bar/foo',
            'prefixType' => PrefixType::VENDOR,
            'identifier' => 'aField',
            'parsedIdentifier' => 'bar_aField',
            'prefixAllFields' => true,
            'prefixSingleField' => true,
            'displayCond' => 'USER:aField:some:method:call',
            'expected' => 'USER:aField:some:method:call',
        ];

        yield 'invalid displayCond, global full prefix on (string)' => [
            'contentBlockName' => 'bar/foo',
            'prefixType' => PrefixType::VENDOR,
            'identifier' => 'aField',
            'parsedIdentifier' => 'bar_aField',
            'prefixAllFields' => false,
            'prefixSingleField' => true,
            'displayCond' => 'aField',
            'expected' => 'aField',
        ];
    }

    public static function createPrefixedMultipleDisplayCondDataProvider(): iterable
    {
        yield 'multiple displayCond, global full prefix on (array)' => [
            'contentBlockName' => 'bar/foo',
            'prefixType' => PrefixType::FULL,
            'identifier' => 'aField',
            'parsedIdentifier' => 'bar_foo_aField',
            'prefixAllFields' => true,
            'prefixSingleField' => false,
            'displayCond' => ['OR' => ['FIELD:aField:=:aValue', 'FIELD:bField:=:aValue']],
            'expected' => ['OR' => ['FIELD:bar_foo_aField:=:aValue', 'FIELD:bar_foo_bField:=:aValue']],
            'bFieldUsed' => true,
        ];

        yield 'multiple displayCond, global vendor prefix on (array)' => [
            'contentBlockName' => 'bar/foo',
            'prefixType' => PrefixType::VENDOR,
            'identifier' => 'aField',
            'parsedIdentifier' => 'bar_aField',
            'prefixAllFields' => true,
            'prefixSingleField' => false,
            'displayCond' => ['OR' => ['FIELD:aField:=:aValue', 'FIELD:bField:=:aValue']],
            'expected' => ['OR' => ['FIELD:bar_aField:=:aValue', 'FIELD:bar_bField:=:aValue']],
            'bFieldUsed' => true,
        ];

        yield 'multiple displayCond, unprefixed (array)' => [
            'contentBlockName' => 'bar/foo',
            'prefixType' => PrefixType::FULL,
            'identifier' => 'aField',
            'parsedIdentifier' => 'aField',
            'prefixAllFields' => false,
            'prefixSingleField' => false,
            'displayCond' => ['OR' => ['FIELD:aField:=:aValue', 'FIELD:bField:=:aValue']],
            'expected' => ['OR' => ['FIELD:aField:=:aValue', 'FIELD:bField:=:aValue']],
        ];

        yield 'multiple displayCond, single full prefix on (array)' => [
            'contentBlockName' => 'bar/foo',
            'prefixType' => PrefixType::FULL,
            'identifier' => 'aField',
            'parsedIdentifier' => 'bar_foo_aField',
            'prefixAllFields' => false,
            'prefixSingleField' => true,
            'displayCond' => ['OR' => ['FIELD:aField:=:aValue', 'FIELD:bField:=:aValue']],
            'expected' => ['OR' => ['FIELD:bar_foo_aField:=:aValue', 'FIELD:bField:=:aValue']],
        ];

        yield 'multiple displayCond, single vendor prefix on (array)' => [
            'contentBlockName' => 'bar/foo',
            'prefixType' => PrefixType::VENDOR,
            'identifier' => 'aField',
            'parsedIdentifier' => 'bar_aField',
            'prefixAllFields' => false,
            'prefixSingleField' => true,
            'displayCond' => ['OR' => ['FIELD:aField:=:aValue', 'FIELD:bField:=:aValue']],
            'expected' => ['OR' => ['FIELD:bar_aField:=:aValue', 'FIELD:bField:=:aValue']],
        ];
    }

    /**
     * @dataProvider createPrefixedMultipleDisplayCondDataProvider
     * @test
     */
    public function createPrefixedMultipleDisplayCondTest(string $contentBlockName, PrefixType $prefixType, string $identifier, string $parsedIdentifier, bool $prefixAllFields, bool $prefixSingleField, array $displayCond, array $expected, bool $bfieldUsed = false): void
    {
        $tca = $this->testPrefixedDisplayCond($contentBlockName, $prefixType, $identifier, $parsedIdentifier, $prefixAllFields, $prefixSingleField, $displayCond, $expected, $bfieldUsed);

        if (serialize($expected) !== serialize($tca['tt_content']['columns'][$parsedIdentifier]['displayCond'])) {
            echo "Accessing $parsedIdentifier.\n";
            print_r($expected);
            print_r($tca);
        } else {
            self::assertEquals($expected, $tca['tt_content']['columns'][$parsedIdentifier]['displayCond']);
        }
    }

    /**
     * @dataProvider createPrefixedSingleDisplayCondDataProvider
     * @test
     */
    public function createPrefixedSingleDisplayCondTest(string $contentBlockName, PrefixType $prefixType, string $identifier, string $parsedIdentifier, bool $prefixAllFields, bool $prefixSingleField, string $displayCond, string $expected, bool $bfieldUsed = false): void
    {
        $tca = $this->testPrefixedDisplayCond($contentBlockName, $prefixType, $identifier, $parsedIdentifier, $prefixAllFields, $prefixSingleField, $displayCond, $expected, $bfieldUsed);

        self::assertEquals($expected, $tca['tt_content']['columns'][$parsedIdentifier]['displayCond']);
    }

    protected function testPrefixedDisplayCond(string $contentBlockName, PrefixType $prefixType, string $identifier, string $parsedIdentifier, bool $prefixAllFields, bool $prefixSingleField, string|array $displayCond, string|array $expected, bool $bfieldUsed = false): array
    {
        $baseTca['tt_content'] = [];
        $GLOBALS['TCA'] = $baseTca;

        $contentBlock = LoadedContentBlock::fromArray([
            'name' => $contentBlockName,
            'yaml' => [
                'table' => 'tt_content',
                'prefixFields' => $prefixAllFields,
                'prefixType' => $prefixType->value,
                'fields' => [
                    [
                        'identifier' => 'aField',
                        'prefixField' => ($prefixAllFields || $prefixSingleField),
                        'displayCond' => $displayCond,
                        'type' => 'Text',
                    ],
                    [
                        'identifier' => 'bField',
                        'prefixField' => $prefixAllFields,
                        'displayCond' => '',
                        'type' => 'Text',
                    ],
                    [
                        'identifier' => 'cField',
                        'prefixField' => ($prefixAllFields || $prefixSingleField),
                        'displayCond' => '',
                        'type' => 'Text',
                    ],
                ],
            ],
        ]);

        $contentBlockRegistry = new ContentBlockRegistry();
        $contentBlockRegistry->register($contentBlock);
        $tableDefinitionCollection = (new TableDefinitionCollectionFactory($contentBlockRegistry))
            ->create();
        $systemExtensionAvailability = new TestSystemExtensionAvailability();
        $systemExtensionAvailability->addAvailableExtension('workspaces');
        $languageFileRegistry = new NoopLanguageFileRegistry();
        $flexFormGenerator = new FlexFormGenerator($languageFileRegistry);
        $tcaGenerator = new TcaGenerator(
            $tableDefinitionCollection,
            new NoopEventDispatcher(),
            $languageFileRegistry,
            new TcaPreparation(),
            $systemExtensionAvailability,
            $flexFormGenerator,
        );
        $tca = $tcaGenerator->generate($baseTca);

        return $tca;
    }
}
