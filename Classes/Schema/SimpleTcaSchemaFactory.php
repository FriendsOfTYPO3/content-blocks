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

use InvalidArgumentException;
use Symfony\Component\Finder\Finder;
use TYPO3\CMS\ContentBlocks\Schema\Exception\UndefinedSchemaException;
use TYPO3\CMS\ContentBlocks\Schema\Field\FieldCollection;
use TYPO3\CMS\ContentBlocks\Schema\Field\TcaField;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @internal Not part of TYPO3's public API.
 */
class SimpleTcaSchemaFactory implements SingletonInterface
{
    protected array $schemas = [];
    protected array $tca;

    public function __construct(
        protected FieldTypeResolver $typeResolver,
    ) {
        // ext_tables.php checker (Check for Broken Extensions backend functionality) does not load TCA.
        // So we need to do it manually.
        $this->initialize($GLOBALS['TCA'] ?? $this->loadConfigurationTcaFiles());
    }

    public function initialize(array $tca): void
    {
        $this->tca = $tca;
        foreach (array_keys($this->tca) as $table) {
            $this->schemas[$table] = $this->build($table);
        }
    }

    public function get(string $schemaName): SimpleTcaSchema
    {
        if (!$this->has($schemaName)) {
            throw new UndefinedSchemaException('No TCA schema exists for the name "' . $schemaName . '".', 1661540377);
        }
        return $this->schemas[$schemaName];
    }

    public function has(string $schemaName): bool
    {
        return isset($this->tca[$schemaName]);
    }

    protected function build(string $schemaName): SimpleTcaSchema
    {
        $allFields = new FieldCollection();
        $systemFields = new FieldCollection();
        $schemaDefinition = $this->tca[$schemaName];
        foreach ($schemaDefinition['columns'] ?? [] as $columnName => $columnConfig) {
            try {
                $fieldType = $this->typeResolver->resolve($columnConfig);
            } catch (\InvalidArgumentException) {
                continue;
            }
            $allFields[$columnName] = new TcaField($fieldType, $columnName, $columnConfig);
            if (in_array($columnName, $schemaDefinition['ctrl']['enablecolumns'] ?? [], true)) {
                foreach ($schemaDefinition['ctrl']['enablecolumns'] as $enablecolumnType => $systemFieldName) {
                    if ($systemFieldName === $columnName) {
                        $systemFields[$enablecolumnType] = new TcaField($fieldType, $columnName, $columnConfig);
                    }
                }
            }
        }
        $schema = new SimpleTcaSchema($schemaName, $allFields, $systemFields, $schemaDefinition['ctrl'] ?? []);
        return $schema;
    }

    /**
     * Load TCA configuration files.
     *
     * @see \TYPO3\CMS\Core\Configuration\Tca\TcaFactory::loadConfigurationTcaFiles()
     */
    private function loadConfigurationTcaFiles(): array
    {
        // To require TCA in a safe scoped environment avoiding local variable clashes.
        // Note: Return type 'mixed' is intended, otherwise broken TCA files with missing "return [];" statement would
        //       emit a "return value must be of type array, int returned" PHP TypeError. This is mitigated by an array
        //       check below.
        $scopedReturnRequire = static function (string $filename): mixed {
            return require $filename;
        };
        // First load "full table" files from Configuration/TCA
        $tca = [];
        $activePackages = GeneralUtility::makeInstance(PackageManager::class)->getActivePackages();
        foreach ($activePackages as $package) {
            try {
                $finder = Finder::create()->files()->sortByName()->depth(0)->name('*.php')->in($package->getPackagePath() . 'Configuration/TCA');
            } catch (InvalidArgumentException) {
                // No such directory in this package
                continue;
            }
            foreach ($finder as $fileInfo) {
                $tcaOfTable = $scopedReturnRequire($fileInfo->getPathname());
                if (is_array($tcaOfTable)) {
                    $tcaTableName = substr($fileInfo->getBasename(), 0, -4);
                    $tca[$tcaTableName] = $tcaOfTable;
                }
            }
        }
        return $tca;
    }
}
