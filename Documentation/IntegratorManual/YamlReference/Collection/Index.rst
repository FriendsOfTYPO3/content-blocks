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

.. rst-class:: dl-parameters

useAsLabel
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` string
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

   Defines which field of the collection item should be used as the title of the
   inline element. If not defined, the first child field will be used as the
   label.

   Example:

   .. code-block:: yaml

      identifier: collection
      type: Collection
      useAsLabel: text
      fields:
        - identifier: image
          type: File
        - identifier: text
          type: Text

fields
   :sep:`|` :aspect:`Required:` true
   :sep:`|` :aspect:`Type:` array
   :sep:`|` :aspect:`Default:` []
   :sep:`|`

   Configures a set of fields as repeatable child objects. All fields defined in
   field types are possible as children. However, consider not to have
   too many nested Collection fields to avoid performance issues. Content Blocks
   are not intended to represent complex data structures. Consider to create
   custom tables for these cases.

   Example:

   .. code-block:: yaml

      fields:
        - identifier: text
          type: Text
        - identifier: image
          type: File

Properties
==========

.. rst-class:: dl-parameters

maxitems
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` integer
   :sep:`|` :aspect:`Default:` 0
   :sep:`|`

   Maximum number of child items. Defaults to a high value. JavaScript record
   validation prevents the record from being saved if the limit is not satisfied.

minitems
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` integer
   :sep:`|` :aspect:`Default:` 0
   :sep:`|`

   Minimum number of child items. Defaults to 0. JavaScript record validation
   prevents the record from being saved if the limit is not satisfied.

foreign_table
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` string (table)
   :sep:`|`

   It is possible to reference another table instead of creating a new one. This
   table can be defined by another Content Block, but can also be an existing
   table defined by the Core or another extension. In case of another Content
   Block, the option :yaml:`aggregateRoot` has to be set to `false`, so that
   required fields are created. Existing tables need to manually define
   the :sql:`foreign_table_parent_uid`, :sql:`tablenames` and :sql:`fieldname`
   fields.

For more advanced configuration refer to the :ref:`TCA documentation <t3tca:columns-inline>`

Example
=======

Minimal
-------

.. code-block:: yaml

    name: example/collection
    group: common
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
    group: common
    fields:
      - identifier: slides
        type: Collection
        useAsLabel: title
        maxitems: 5
        minitems: 1
        fields:
          - identifier: image
            type: File
            minitems: 1
            maxitems: 1
          - identifier: title
            type: Text

.. code-block:: yaml

    # This custom table needs to be defined in a separate Content Block
    name: example/slide
    table: my_slide
    aggregateRoot: false
    fields:
      - identifier: image
        type: File

    name: example/collection
    group: common
    fields:
      - identifier: slides
        type: Collection
        foreign_table: my_slide
