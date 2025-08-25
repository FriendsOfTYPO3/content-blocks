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

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Finder\Finder;
use Symfony\Component\VarExporter\VarExporter;
use TYPO3\CMS\ContentBlocks\Schema\Exception\UndefinedSchemaException;
use TYPO3\CMS\ContentBlocks\Schema\Field\FieldCollection;
use TYPO3\CMS\ContentBlocks\Schema\Field\TcaField;
use TYPO3\CMS\Core\Cache\Frontend\PhpFrontend;
use TYPO3\CMS\Core\Package\PackageManager;

/**
 * @todo This class is a factory and Root Schema at the same time.
 * @todo Not good, but not bad either.
 *
 * @internal Not part of TYPO3's public API.
 */
#[Autoconfigure(public: true)]
class SimpleTcaSchemaFactory
{
    protected array $schemas = [];

    public function __construct(
        #[Autowire(service: 'cache.core')]
        protected readonly PhpFrontend $cache,
        protected FieldTypeResolver $typeResolver,
        protected PackageManager $packageManager,
    ) {
        if (($schemas = $this->getFromCache()) !== false) {
            $this->schemas = $schemas;
            return;
        }
        $baseTca = $this->loadConfigurationTcaFiles();
        $this->initialize($baseTca);
    }

    /**
     * This method should only be called in BeforeTcaOverridesEvent
     * with base TCA as argument.
     *
     * @param array<string, array> $tca
     */
    public function initialize(array $tca): void
    {
        foreach ($tca as $table => $schemaDefinition) {
            $this->schemas[$table] = $this->build($table, $schemaDefinition);
        }
        $this->setCache();
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
        return isset($this->schemas[$schemaName]);
    }

    protected function build(string $schemaName, array $schemaDefinition): SimpleTcaSchema
    {
        $allFields = new FieldCollection();
        $systemFields = new FieldCollection();
        foreach ($schemaDefinition['columns'] ?? [] as $columnName => $columnConfig) {
            if (is_array($columnConfig) === false) {
                throw new \InvalidArgumentException(
                    'Column configuration of field "' . $columnName . '" of type "' . $schemaName . '" must be an array, ' . gettype($columnConfig) . ' given.',
                    1755100263
                );
            }
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
     * @todo You may wonder, why we copy this code from the Core TcaFactory.
     * @todo We used to fill this schema by using the BeforeTcaOverridesEvent.
     * @todo The reason we removed this dependency was that many deployment
     * @todo processes use `typo3 cache:flush` in their pipeline. This works
     * @todo well in local / staging environments, but in frequently visited
     * @todo production environments a concurrent hit can happen, which may
     * @todo produce a compiled Content Blocks cache entry, while the flush
     * @todo command erased the SimpleTcaSchema cache entry at the same time.
     * @todo The problem is, this cache can't recover itself, if TCA is
     * @todo already cached and the event won't fire again, leaving the system
     * @todo in a broken state.
     * @todo Example: "The field "pages" is missing the required "type" in Content Block".
     * @todo To circumvent this error, the functionality to create base TCA
     * @todo is added to this class. Now, the cache can rebuild itself.
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
        // Backup the original TCA in case it is already set.
        $backupTca = $GLOBALS['TCA'] ?? null;
        // First load "full table" files from Configuration/TCA
        $GLOBALS['TCA'] = [];
        $activePackages = $this->packageManager->getActivePackages();
        foreach ($activePackages as $package) {
            try {
                $finder = Finder::create()->files()->sortByName()->depth(0)->name('*.php')->in($package->getPackagePath() . 'Configuration/TCA');
            } catch (\InvalidArgumentException) {
                // No such directory in this package
                continue;
            }
            foreach ($finder as $fileInfo) {
                $tcaOfTable = $scopedReturnRequire($fileInfo->getPathname());
                if (is_array($tcaOfTable)) {
                    $tcaTableName = substr($fileInfo->getBasename(), 0, -4);
                    $GLOBALS['TCA'][$tcaTableName] = $tcaOfTable;
                }
            }
        }
        $tca = $GLOBALS['TCA'];
        if ($backupTca) {
            $GLOBALS['TCA'] = $backupTca;
        } else {
            unset($GLOBALS['TCA']);
        }
        return $tca;
    }

    protected function getFromCache(): false|array
    {
        return $this->cache->require('ContentBlocks_SimpleTcaSchema');
    }

    protected function setCache(): void
    {
        $data = 'return ' . VarExporter::export($this->schemas) . ';';
        $this->cache->set('ContentBlocks_SimpleTcaSchema', $data);
    }
}
