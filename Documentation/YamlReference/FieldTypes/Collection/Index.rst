.. include:: /Includes.rst.txt
.. _field_type_collection:

==========
Collection
==========

:php:`type => 'inline' // TCA`

The :yaml:`Collection` type generates a field for
:ref:`Inline-Relational-Record-Editing (IRRE) <t3tca:columns-inline>`, which
allows nesting of other :ref:`field types <field_types>`. This field type allows
building structures like image sliders, accordions, tabs and so on.

Collections will automatically create custom tables and use the
:yaml:`identifier` as table name. It is possible to override this with the
setting :yaml:`table`. Collections are always hidden in the **List**
module. Usually Collections only have one type. To realise multiple types it is
recommended to extract the definition to a separate
:ref:`Record Type <yaml_reference_record_type_multiple_types>` and use
:ref:`foreign_table <field_type_collection_foreign_table>` instead.

Custom icon
===========

In order to define a custom icon for your Collection field, you may place an
image file inside **Assets** folder called **{identifier}.svg**. So for example
if your identifier for the Collection is :yaml:`my_collection`, then your image
should be named **my_collection.svg**. Alternatively, you can also provide png
or gif files. These should be 64x64px.

Settings
========

.. include:: /Snippets/LabelField.rst

.. confval:: table

   :Required: false
   :Type: string

   Alternative table name for the Collection. Default is :yaml:`identifier` with
   prefix if enabled.

   .. code-block:: yaml

       table: tx_vendor_my_custom_table_name

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

.. confval:: minitems

   :Required: false
   :Type: integer
   :Default: 0

   Minimum number of child items. Defaults to 0. JavaScript record validation
   prevents the record from being saved if the limit is not satisfied.

.. confval:: maxitems

   :Required: false
   :Type: integer
   :Default: 0

   Maximum number of child items. Defaults to a high value. JavaScript record
   validation prevents the record from being saved if the limit is not satisfied.

.. confval:: appearance.collapseAll

   :Required: false
   :Type: bool|null
   :Default: null

   * Default (null): Last collapsed/expanded state is remembered
   * true: Show all child records collapsed
   * false: Show all child records expanded

.. confval:: appearance.levelLinksPosition

   :Required: false
   :Type: string
   :Default: top

   Defines where to show the "New record" link in relation to the child records.
   Valid keywords are :yaml:`top`, :yaml:`bottom` and :yaml:`both`.

.. _field_type_collection_foreign_table:
.. confval:: foreign_table

   :Required: false
   :Type: string (table)

   It is possible to reference another table instead of creating a new one. This
   table can be defined by another :ref:`Content Block <yaml_reference_record_type_in_collection>`,
   but can also be an existing table defined by the Core or another extension.
   Existing tables need to manually define the :sql:`foreign_table_parent_uid`,
   :sql:`tablenames` and :sql:`fieldname` fields.

   .. note::

      When you use :yaml:`foreign_table` it is not possible to define
      :yaml:`fields` anymore. They will not be evaluated.

.. _field_type_collection_foreign_field:
.. confval:: foreign_field

   :Required: false
   :Type: string (field)

   It is possible to override the field name pointing to the parent record. Per
   default it is called :sql:`foreign_table_parent_uid`. This corresponds with
   the TCA option :ref:`foreign_field <t3tca:columns-inline-properties-foreign-field>`.

.. confval:: shareAcrossTables

   :Required: false
   :Type: boolean
   :Default: false

   Allows to reference a Record Type across multiple **tables**, if
   :ref:`foreign_table <field_type_collection_foreign_table>` is used.

   Make sure to add this to **every** Collection, which shares the table.

   This will create a new field called :sql:`tablenames`. It corresponds to the
   TCA option :ref:`foreign_table_field <t3tca:columns-inline-properties-foreign-table-field>`.
   The field name can be overridden by defining :yaml:`foreign_table_field` explicitly.

.. confval:: shareAcrossFields

   :Required: false
   :Type: boolean
   :Default: false

   Allows to reference a Record Type across multiple **fields**, if
   :ref:`foreign_table <field_type_collection_foreign_table>` is used.

   Make sure to add this to **every** Collection, which shares the table.

   This will create a new field called :sql:`fieldname`. It corresponds to the
   TCA option :ref:`foreign_match_fields <t3tca:columns-inline-properties-foreign-match-fields>`.

For more advanced configuration refer to the :ref:`TCA documentation <t3tca:columns-inline-properties>`

Example
=======

Minimal
-------

.. code-block:: yaml

    name: example/collection
    fields:
      - identifier: collection
        type: Collection
        labelField: text
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
        appearance:
          collapseAll: true
          levelLinksPosition: both
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
    labelField: title
    fields:
      - identifier: title
        type: Text
      - identifier: image
        type: File

.. code-block:: yaml

    name: example/collection
    fields:
      - identifier: slides
        type: Collection
        foreign_table: my_slide
        shareAcrossTables: true
        shareAcrossFields: true
