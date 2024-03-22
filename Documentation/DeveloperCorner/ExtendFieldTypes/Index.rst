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

.. code-block:: php

    interface FieldTypeInterface
    {
        public static function createFromArray(array $settings): FieldTypeInterface;
        public function getTca(): array;
        public function getSql(string $column): string;
        public static function getName(): string;
        public static function getTcaType(): string;
        public static function isSearchable(): bool;
        public static function isRelation(): bool;
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
column name.

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

isRelation
----------

Should be true if based on TCA type :php:`select`, :php:`group`, or
:php:`inline`. Enables processing of child relations.

hasItems
--------

Should be true if based on TCA type :php:`select`, :php:`radio`,
or :php:`check`. Enables translation handling for option :php:`items`.
