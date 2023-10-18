.. include:: /Includes.rst.txt
.. _yaml_reference_record_type:

============
Record Types
============

Folder: `ContentBlocks/RecordTypes`.

Record Types are generic Content Types in TYPO3. Basically everything, which is
not defined by the TYPO3 Core itself. Adding custom records requires you to
define a :yaml:`table` name. A minimal example looks like this:

.. code-block:: yaml
   :caption: EXT:your_extension/ContentBlocks/RecordTypes/my-record-type/EditorInterface.yaml

    name: example/my-record-type
    table: my_record_type
    labelField: title
    fields:
      -
        identifier: title
        type: Text

This example will create a new table `my_record_type`. Usually Record Types are
added in folders, so you first have to create a new folder page in the page
tree. After that, you can switch to the **List** module and click
"Create new record". There you will find your newly created Record Type under
the group "System Records".

It is also possible to allow creation of Record Types in normal pages. For that
you have to enable :yaml:`ignorePageTypeRestriction`:

.. code-block:: yaml
   :caption: EXT:your_extension/ContentBlocks/RecordTypes/my-record-type/EditorInterface.yaml

    name: example/my-record-type
    table: my_record_type
    labelField: title
    security:
      ignorePageTypeRestriction: true
    fields:
      -
        identifier: title
        type: Text

In order to create multiple types for a single Record Type, it is required to
define a :yaml:`typeField` field and a :yaml:`typeName`. The type field will be
created automatically and added at the top of your editing interface as a select
dropdown. The different types will be also added automatically to the list:

.. code-block:: yaml
   :caption: EXT:your_extension/ContentBlocks/RecordTypes/diver/EditorInterface.yaml

    name: example/diver
    table: person
    typeField: type
    typeName: diver
    fields:
      -
        identifier: title
        type: Text

.. code-block:: yaml
   :caption: EXT:your_extension/ContentBlocks/RecordTypes/instructor/EditorInterface.yaml

    name: example/instructor
    table: person
    typeField: type
    typeName: instructor
    fields:
      -
        identifier: title
        type: Text

Collections
===========

Collections are a :ref:`field type <field_type_collection>`, which you can only
define in the :yaml:`fields` array. They will create custom tables automatically
and use the :yaml:`identifier` as table name. Therefore they don't require you
to define a :yaml:`table`. Collections are always hidden in the **List** module.
This is controlled by the :yaml:`aggregateRoot` setting. Usually Collections
only have one type. To realise multiple types it is recommended to extract the
definition to a separate Record Type and use :yaml:`foreign_table` instead.

Options
=======

.. confval:: table

   :Required: true (false for Collections)
   :Type: string

   The custom table name to be used for the new Record Type. The table name for
   Collections is determined by the :yaml:`identifier` and thus should not be
   defined there.

   .. code-block:: yaml

       table: my_custom_table_name

.. confval:: typeField

   :Required: false
   :Type: string

   The field identifier to use as the type switch. This field will be
   automatically generated and prepended as the very first field. The item list
   is filled automatically as well. There is no need to define this field
   manually in your fields list. Useful, if you want to define multiple types
   for a single table (single table inheritance). Collections don't need this
   option, as they usually only have one type.

   .. code-block:: yaml

       typeField: type

.. confval:: typeName

   :Required: false
   :Type: string
   :Default: automatically generated from :yaml:`name`

   The identifier of the new Record Type. It is automatically generated from
   the name, if not defined manually.

   .. code-block:: yaml

       typeName: type1

.. confval:: labelField

   :Required: false (but highly recommended)
   :Type: string|array

   Defines which field should be used as the title of the record. If not
   defined, the first valid child field will be used as the label. It is
   possible to define an array of fields, which will be displayed
   comma-separated in the backend.

   .. code-block:: yaml

       # a single field for the label
       labelField: title

       # multiple fields will be displayed comma-separated
       labelField:
           - title
           - text

.. confval:: fallbackLabelFields

   :Required: false
   :Type: array

   Defines which fields should be used as fallback, if :yaml:`labelField` is not
   filled. The first filled field which is found will be used. Can only be used
   if there is only one :yaml:`labelField` field defined.

   .. code-block:: yaml

       # fallback fields will be used, if title from labelField is empty
       labelField: title
       fallbackLabelFields:
           - text1
           - text2

.. confval:: aggregateRoot

   :Required: false
   :Type: boolean
   :Default: true (false for Collections)

   By default, all tables are treated as `aggregateRoot`. This means, this table
   is not a child-table of another root. By assigning this option the `false`
   value, additional fields are created to enable a reference to a parent table:
   :sql:`foreign_table_parent_uid`, :sql:`tablenames` and :sql:`fieldname`. Now,
   a type Collection field can define :yaml:`foreign_table` with this table.
   When referencing an existing table, you need to take care yourself that these
   fields exist. Also, non-aggregate tables are hidden in the List module.

   .. code-block:: yaml

       # set this for Record Types if they should be used as `foreign_table` in Collections
       aggregateRoot: false

.. confval:: languageAware

   :Required: false
   :Type: boolean
   :Default: true

   If set to :yaml:`false`, language related fields are not created. Namely
   :sql:`sys_language_uid`, :sql:`l10n_parent`, :sql:`l10n_source` and :sql:`l10n_diffsource`.

   .. code-block:: yaml

       # disable language support
       languageAware: false

.. confval:: workspaceAware

   :Required: false
   :Type: boolean
   :Default: true

   If set to :yaml:`false`, workspace related fields are not created. Namely
   :sql:`t3ver_oid`, :sql:`t3ver_wsid`, :sql:`t3ver_state` and :sql:`t3ver_stage`.

   .. code-block:: yaml

       # disable workspaces support
       workspaceAware: false

.. confval:: editLocking

   :Required: false
   :Type: boolean
   :Default: true

   If set to :yaml:`false`, the functionality to lock the editing for editors is
   removed. This refers to the :sql:`editlock` field.

   .. code-block:: yaml

       # disable edit lock field
       editLocking: false

.. confval:: restriction

   :Required: false
   :Type: array
   :Default: true (for all sub properties)

   There are several restrictions in TYPO3, which filter records by certain
   constraints.

   :yaml:`disabled`
      Adds a checkbox to hide the record in the frontend.

   :yaml:`startTime`
      Adds a date picker to set the start time when to display the record.

   :yaml:`endTime`
      Adds a date picker to set the end time when to stop displaying the record.

   :yaml:`userGroup`
      Adds a selection to choose user groups, which are allowed to view the record.

   .. code-block:: yaml

       restriction:
         disabled: false
         startTime: true
         endTime: true
         userGroup: false

.. confval:: softDelete

   :Required: false
   :Type: boolean
   :Default: true

   When deleting records in the TYPO3 backend, they are not really deleted in
   the database. They are merely flagged as deleted. Disabling this option,
   removes this safety net.

   .. code-block:: yaml

       # records will be really deleted in the backend
       softDelete: false

.. confval:: trackCreationDate

   :Required: false
   :Type: boolean
   :Default: true

   Tracks the timestamp of the creation date. Disabling this option removes this
   information.

   .. code-block:: yaml

       trackCreationDate: false

.. confval:: trackUpdateDate

   :Required: false
   :Type: boolean
   :Default: true

   Tracks the timestamp of the last update. Disabling this option removes this
   information.

   .. code-block:: yaml

       trackUpdateDate: false

.. confval:: trackAncestorReference

   :Required: false
   :Type: boolean
   :Default: true

   If set to :yaml:`false`, the tracking field for the original record will not
   be created. Namely :sql:`t3_origuid`.

   .. code-block:: yaml

       trackAncestorReference: false

.. confval:: sortable

   :Required: false
   :Type: boolean
   :Default: true

   Tracks the order of records. Arrows will appear to sort records explicitly.
   Disabling this option removes this functionality. This corresponds to the
   TCA option :php:`sortby`.

   .. code-block:: yaml

       sortable: false

.. confval:: sortField

   :Required: false
   :Type: string|array
   :Default: true

   The field identifier to use for sorting records. If set, this will disable
   the :yaml:`sortable` option automatically. This corresponds to the TCA option
   :php:`default_sortby`. It is possible to define multiple sorting fields with
   an array.

   .. code-block:: yaml

       # simple sort by one field in ascending order
       sortField: title

       # sorting by multiple fields with different orders
       sortField:
         - identifier: title
           order: desc
         - identifier: text
           order: asc

.. confval:: internalDescription

   :Required: false
   :Type: boolean
   :Default: false

   If enabled, this adds a new tab `Notes` with a description field. When filled
   with text, a record information will be displayed in the editing view. This
   corresponds with the TCA ctrl option :php:`descriptionColumn`. This field is
   supposed to be used only for the backend.

   .. code-block:: yaml

       internalDescription: true

.. confval:: rootLevelType

   :Required: false
   :Type: string
   :Default: onlyOnPages

   Restricts the place, where the record can be created. Possible values are
   :yaml:`onlyOnPages` (default), :yaml:`onlyOnRootLevel` and :yaml:`both`. This
   corresponds to the TCA ctrl option :php:`rootLevel`.

   .. code-block:: yaml

       rootLevelType: 'onlyOnRootLevel'

.. confval:: security

   :Required: false
   :Type: array

   :yaml:`ignoreWebMountRestriction`
      default false, Allows users to access records that are not in their defined web-mount, thus bypassing this restriction.

   :yaml:`ignoreRootLevelRestriction`
      default false, Allows non-admin users to access records that are on the root-level (page ID 0), thus bypassing this usual restriction.

   :yaml:`ignorePageTypeRestriction`
      default false (but true if `aggregateRoot` is false e.g. for Collection fields), Allows to use the record on any kind of page type.

   .. code-block:: yaml

       security:
           ignoreWebMountRestriction: true
           ignoreRootLevelRestriction: true
           ignorePageTypeRestriction: true

.. confval:: readOnly

   :Required: false
   :Type: boolean
   :Default: false

   If enabled, the record can not be edited in the TYPO3 backend anymore.

   .. code-block:: yaml

       readOnly: true

.. confval:: adminOnly

   :Required: false
   :Type: boolean
   :Default: false

   If enabled, only admins can edit the record.

   .. code-block:: yaml

       adminOnly: true

.. confval:: hideAtCopy

   :Required: false
   :Type: boolean
   :Default: false

   If enabled, the record will be disabled, when copied. Only works, if
   :yaml:`restriction.disabled` is set to :yaml:`true`.

   .. code-block:: yaml

       hideAtCopy: true

.. confval:: appendLabelAtCopy

   :Required: false
   :Type: string

   If set, the label field :yaml:`labelField` will be appended with this
   string, when copied.

   .. code-block:: yaml

       appendLabelAtCopy: append me
