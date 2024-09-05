.. include:: /Includes.rst.txt
.. _extending-field-types:

=====================
Extending Field Types
=====================

Content Blocks already comes with pre-defined :ref:`Field Types <field_types>`.
You should utilize them as much as possible. But in case you need special types
which cannot be covered from the basic ones, it is possible to extend these with
your own types.


When to add new field types
===========================

You can add own field types whenever you have fields with a distinct set of
configuration options set. These options can be set as default in your type
so you gain a semantic naming for this field. For example type `Money` could
be the core type :yaml:`Number` with :yaml:`format` set to :yaml:`decimal` as
default value.

Another use case is having a custom made TCA
:ref:`renderType <t3coreapi:FormEngine-Rendering-NodeFactory>`, which is not
covered by existing field types. This could be e.g. TCA type
:ref:`user <t3tca:columns-user>` with a custom renderType. This way it is
possible to use the renderType as a first-class type in Content Blocks.

Adding a new Field Type
=======================

To add a new Field Type it is required to implement the
:php:`\TYPO3\CMS\ContentBlocks\FieldType\FieldTypeInterface`. Have a look at
the Core implementations to get a feeling on how to implement them.

.. note::

    The registration is based on dependency injection. Make sure your extension
    has it enabled in Configuration/Services.yaml.

.. code-block:: php

    interface FieldTypeInterface
    {
        public static function createFromArray(array $settings): FieldTypeInterface;
        public function getTca(): array;
        public function getSql(string $column): string;
        public static function getName(): string;
        public static function getTcaType(): string;
        public static function isSearchable(): bool;
        public static function hasItems(): bool;
    }

createFromArray
---------------

The incoming :php:`$settings` array is the converted YAML definition. Apply your
logic here to instantiate a new type.


getTca
------

As the name suggests, you generate your TCA config here. This can be done based
on the settings provided when :php:`createFromArray` was called. Of course this
can also be a static configuration, if you don't want to provide any settings.

getSql
------

The SQL definition for your database column. Use :php:`$column` as the
column name. Return empty string to fall back to standard definition.

getName
-------

This is the actual type identifier for usage in the YAML :yaml:`type` option.
It is recommended to use UpperCamelCase, but it's not required.

getTcaType
----------

The TCA type, the new Content Blocks type is based on.

isSearchable
------------

Whether the field contents should be searchable in global search.

Example
=======

Example for a field type "Money".

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace VENDOR\MyExtension\FieldType;

    use TYPO3\CMS\ContentBlocks\FieldType\FieldTypeInterface;
    use TYPO3\CMS\ContentBlocks\FieldType\WithCommonProperties;

    final class MoneyFieldType implements FieldTypeInterface
    {
        use WithCommonProperties;

        private float $default = 0.00;
        private bool $required = false;
        private bool $nullable = false;

        public static function getName(): string
        {
            return 'Money';
        }

        public static function getTcaType(): string
        {
            return 'number';
        }

        public static function isSearchable(): bool
        {
            return false;
        }

        public static function createFromArray(array $settings): self
        {
            $self = new self();
            $self->setCommonProperties($settings);
            $default = $settings['default'] ?? $self->default;
            $self->default = (float)$default;
            $self->required = (bool)($settings['required'] ?? $self->required);
            $self->nullable = (bool)($settings['nullable'] ?? $self->nullable);
            return $self;
        }

        public function getTca(): array
        {
            $tca = $this->toTca();
            $config['type'] = self::getTcaType();
            if ($this->default !== 0.0) {
                $config['default'] = $this->default;
            }
            if ($this->required) {
                $config['required'] = true;
            }
            $config['format'] = 'decimal';
            $tca['config'] = array_replace($tca['config'] ?? [], $config);
            return $tca;
        }

        public function getSql(string $column): string
        {
            $null = ' NOT NULL';
            if ($this->nullable) {
                $null = '';
            }
            return "`$column` decimal(10,2) DEFAULT '0.00'" . $null;
        }
    }
