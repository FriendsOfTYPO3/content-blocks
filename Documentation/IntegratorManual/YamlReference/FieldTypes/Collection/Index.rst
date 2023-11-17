.. include:: /Includes.rst.txt
.. _field_type_collection:

==========
Collection
==========

The `Collection` type generates a field for Inline-Relational-Record-Editing
(IRRE), which allows nesting of other field types as children.
This field type allows building structures like image sliders, accordion, tabs
and so on.

It corresponds with the TCA :php:`type => 'inline'`.

SQL overrides via `alternativeSql` allowed: no.

First-level options
===================

All options, which can be defined for :ref:`Record Types <yaml_reference_record_type>`
can be used here as well.

.. confval:: fields

   :Required: true
   :Type: array

   Configures a set of fields as repeatable child objects. All fields defined in
   field types are possible as children. It is also possible to further nest
   Collection fields.

   Example:

   .. code-block:: yaml

      fields:
        - identifier: text
          type: Text
        - identifier: image
          type: File

Settings
========

.. confval:: maxitems

   :Required: false
   :Type: integer
   :Default: 0

   Maximum number of child items. Defaults to a high value. JavaScript record
   validation prevents the record from being saved if the limit is not satisfied.

.. confval:: minitems

   :Required: false
   :Type: integer
   :Default: 0

   Minimum number of child items. Defaults to 0. JavaScript record validation
   prevents the record from being saved if the limit is not satisfied.

.. confval:: foreign_table

   :Required: false
   :Type: string (table)

   It is possible to reference another table instead of creating a new one. This
   table can be defined by another Content Block, but can also be an existing
   table defined by the Core or another extension. In case of another Content
   Block, the option :yaml:`aggregateRoot` has to be set to `false`, so that
   required fields are created. Existing tables need to manually define
   the :sql:`foreign_table_parent_uid`, :sql:`tablenames` and :sql:`fieldname`
   fields.

For more advanced configuration refer to the :ref:`TCA documentation <t3tca:columns-inline>`

Custom icon
===========

In order to define a custom icon for your Collection field, you may place an
image file inside `Assets` called `{identifier}.svg`. So for example if your
identifier for the Collection is `my_collection`, then your image should be
named `my_collection.svg`. Alternatively, you can also provide png or gif files.
These should be 64x64px.

Example
=======

Minimal
-------

.. code-block:: yaml

    name: example/collection
    fields:
      - identifier: collection
        type: Collection
        fields:
          - identifier: text
            type: Text

Advanced / use case
-------------------

.. code-block:: yaml

    name: example/collection
    fields:
      - identifier: slides
        type: Collection
        labelField: title
        maxitems: 5
        minitems: 1
        fields:
          - identifier: image
            type: File
            minitems: 1
            maxitems: 1
          - identifier: title
            type: Text

This custom table :yaml:`my_slide` needs to be defined as a
:ref:`Record Type <yaml_reference_record_type>` in order to be used as a foreign
table in :yaml:`slides`.

.. code-block:: yaml

    name: example/slide
    table: my_slide
    aggregateRoot: false
    fields:
      - identifier: title
        type: Text
      - identifier: image
        type: File

    name: example/collection
    fields:
      - identifier: slides
        type: Collection
        foreign_table: my_slide
