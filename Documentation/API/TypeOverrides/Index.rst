.. include:: /Includes.rst.txt
.. _api_type_overrides:

==============
Type Overrides
==============

.. versionadded:: 1.2

Type Overrides are a feature available for the field type :ref:`Collection <field_type_collection>` and :ref:`File <field_type_file>`.
With them it is possible to override a type definition of an existing type when
used in the context of a Collection.

Internally the TCA option :ref:`overrideChildTca <t3tca:columns-inline-properties-overridechildtca-examples>`
is used here to override the :php:`showitem` string for the specified type.

This feature can be used for different purposes:

* Reorder fields of the child record
* Remove fields from the child record
* Add completely new fields to the child record
* Override options like label, description, renderType etc.

For either of those purposes you need to re-define the :yaml:`fields` definition
for the specified type.

Example 1: Override Multi Type Record
=====================================

.. code-block:: yaml

    name: friendsoftypo3/example
    table: tx_friendsoftypo3_example
    prefixFields: false
    labelField: title
    fields:
      -
        identifier: title
        type: Text
        label: Title
      - identifier: collection_override
        type: Collection
        foreign_table: tx_hov_domain_model_record1
        overrideType:
          record1:
            - identifier: type
              type: Select
              useExistingField: true
            - identifier: title
              type: Text
              useExistingField: true
            - identifier: custom_field
              type: Text

Here, the type `record1` is overridden in the context of the Collection field
:yaml:`collection_override`. A type override is created with the option
:yaml:`overrideType` and then with the :yaml:`typeName` as the next key. From
there you define the usual configuration like in :yaml:`fields`.

Example 2: Override Single Type Record
======================================

.. code-block:: yaml

    name: friendsoftypo3/example
    table: tx_friendsoftypo3_example
    prefixFields: false
    labelField: title
    fields:
      - identifier: title
        type: Text
        label: Title
      - identifier: collection_override
        type: Collection
        foreign_table: tx_hov_domain_model_record
        overrideType:
          - identifier: title
            type: Text
            useExistingField: true
          - identifier: custom_field
            type: Text

This is the same as for multi-type records. The only difference is that you can
omit the type key. Internally, the :yaml:`typeName` `1` is used.

Example 3: Override File Types
==============================

.. code-block:: yaml

    name: friendsoftypo3/example
    table: tx_friendsoftypo3_example
    prefixFields: false
    labelField: title
    fields:
      - identifier: title
        type: Text
        label: Title
      - identifier: file_override
        type: File
        overrideType:
          image:
            - identifier: image_overlay_palette
              type: Palette
              label: 'LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette'
              fields:
                - identifier: alternative
                  useExistingField: true
                - identifier: description
                  useExistingField: true
                - type: Linebreak
                - identifier: link
                  useExistingField: true
                - identifier: title
                  useExistingField: true
                - type: Linebreak
                - identifier: example_custom_field
                  type: Text
                  label: 'My custom Field'
                - type: Linebreak
                - identifier: crop
                  useExistingField: true

Lastly, type overrides can be used to re-define file definitions. In this
example the file type :yaml:`image` is overridden. File Types are usually
structured in a palette. This is why there is a type Palette as the first field.

.. tip::

    File Types can also be re-defined on a global level. Refer to
    the :ref:`File Types API <api_file_types>` for this.
