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
use Symfony\Component\VarExporter\VarExporter;
use TYPO3\CMS\ContentBlocks\Schema\Exception\UndefinedSchemaException;
use TYPO3\CMS\ContentBlocks\Schema\Field\FieldCollection;
use TYPO3\CMS\ContentBlocks\Schema\Field\TcaField;
use TYPO3\CMS\Core\Cache\Frontend\PhpFrontend;

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
    ) {
        // The schema must only be hydrated from previous caches,
        // which were built in BeforeTcaOverridesEvent.
        if (($schemas = $this->getFromCache()) !== false) {
            $this->schemas = $schemas;
        }
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
