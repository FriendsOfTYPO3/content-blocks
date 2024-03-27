.. include:: /Includes.rst.txt
.. _yaml_reference_record_type:

============
Record Types
============

Folder: `ContentBlocks/RecordTypes`.

Record Types are generic Content Types in TYPO3. Basically everything, which is
not a Content Element or Page Type. Adding custom records requires you to define
a :yaml:`table` name. A minimal example looks like this:

.. code-block:: yaml
   :caption: EXT:your_extension/ContentBlocks/RecordTypes/my-record-type/EditorInterface.yaml

    name: example/my-record-type
    table: tx_vendor_my_record_type
    labelField: title
    fields:
      - identifier: title
        type: Text

Check out this :ref:`comprehensive guide <cb_guides_record_types>` on ways to
utilize Record Types.

Options
=======

Here you can find all :ref:`common root options <yaml_reference_common>`.

.. confval:: table
   :name: record-type-table

   :Required: true
   :Type: string

   The custom table name to be used for the new Record Type.

   .. warning::

      Avoid using dashes "-" inside your table names. They are not guaranteed to
      be escaped in the database. We recommend to always use snake case.

   .. code-block:: yaml

       table: tx_vendor_my_custom_table_name

.. confval:: labelField
   :name: record-type-labelField

   :Required: true
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
   :name: record-type-fallbackLabelFields

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


.. confval:: typeField
   :name: record-type-typeField

   :Required: false
   :Type: string

   The field identifier to use as the type switch. This field will be
   automatically generated and prepended as the very first field. The item list
   is filled automatically as well. There is no need to define this field
   manually in your fields list. Useful, if you want to define multiple types
   for a single table (single table inheritance).

   .. code-block:: yaml

       typeField: type

.. confval:: typeName
   :name: record-type-typeName

   :Required: false
   :Type: string
   :Default: automatically generated from :yaml:`name`

   The identifier of the new Record Type. It is automatically generated from
   the name, if not defined manually.

   .. code-block:: yaml

       typeName: type1

.. confval:: languageAware
   :name: record-type-languageAware

   :Required: false
   :Type: boolean
   :Default: true

   If set to :yaml:`false`, language related fields are not created. Namely
   :sql:`sys_language_uid`, :sql:`l10n_parent`, :sql:`l10n_source` and :sql:`l10n_diffsource`.

   .. code-block:: yaml

       # disable language support
       languageAware: false

.. confval:: workspaceAware
   :name: record-type-workspaceAware

   :Required: false
   :Type: boolean
   :Default: true

   Creates workspace related fields. Namely :sql:`t3ver_oid`, :sql:`t3ver_wsid`,
   :sql:`t3ver_state` and :sql:`t3ver_stage`. If
   :ref:`EXT:workspaces <t3coreapi:workspaces>` is not installed, these fields
   won't be created.

   .. code-block:: yaml

       # disable workspaces support
       workspaceAware: false

.. confval:: editLocking
   :name: record-type-editLocking

   :Required: false
   :Type: boolean
   :Default: true

   If set to :yaml:`false`, the functionality to lock the editing for editors is
   removed. This refers to the :sql:`editlock` field.

   .. code-block:: yaml

       # disable edit lock field
       editLocking: false

.. confval:: restriction
   :name: record-type-restriction

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
   :name: record-type-softDelete

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
   :name: record-type-trackCreationDate

   :Required: false
   :Type: boolean
   :Default: true

   Tracks the timestamp of the creation date. Disabling this option removes this
   information.

   .. code-block:: yaml

       trackCreationDate: false

.. confval:: trackUpdateDate
   :name: record-type-trackUpdateDate

   :Required: false
   :Type: boolean
   :Default: true

   Tracks the timestamp of the last update. Disabling this option removes this
   information.

   .. code-block:: yaml

       trackUpdateDate: false

.. confval:: sortable
   :name: record-type-sortable

   :Required: false
   :Type: boolean
   :Default: true

   Tracks the order of records. Arrows will appear to sort records explicitly.
   Disabling this option removes this functionality. This corresponds to the
   TCA option :php:`sortby`.

   .. code-block:: yaml

       sortable: false

.. confval:: sortField
   :name: record-type-sortField

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
   :name: record-type-internalDescription

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
   :name: record-type-rootLevelType

   :Required: false
   :Type: string
   :Default: onlyOnPages

   Restricts the place, where the record can be created. Possible values are
   :yaml:`onlyOnPages` (default), :yaml:`onlyOnRootLevel` and :yaml:`both`. This
   corresponds to the TCA ctrl option :php:`rootLevel`.

   .. code-block:: yaml

       rootLevelType: 'onlyOnRootLevel'

.. confval:: security
   :name: record-type-security

   :Required: false
   :Type: array

   :yaml:`ignoreWebMountRestriction`
      default false, Allows users to access records that are not in their defined web-mount, thus bypassing this restriction.

   :yaml:`ignoreRootLevelRestriction`
      default false, Allows non-admin users to access records that are on the root-level (page ID 0), thus bypassing this usual restriction.

   :yaml:`ignorePageTypeRestriction`
      default false (but true if table is used as :yaml:`foreign_table`), Allows to use the record on any kind of page type.

   .. code-block:: yaml

       security:
           ignoreWebMountRestriction: true
           ignoreRootLevelRestriction: true
           ignorePageTypeRestriction: true

.. confval:: readOnly
   :name: record-type-readOnly

   :Required: false
   :Type: boolean
   :Default: false

   If enabled, the record can not be edited in the TYPO3 backend anymore.

   .. code-block:: yaml

       readOnly: true

.. confval:: adminOnly
   :name: record-type-adminOnly

   :Required: false
   :Type: boolean
   :Default: false

   If enabled, only admins can edit the record.

   .. code-block:: yaml

       adminOnly: true

.. confval:: hideAtCopy
   :name: record-type-hideAtCopy

   :Required: false
   :Type: boolean
   :Default: false

   If enabled, the record will be disabled, when copied. Only works, if
   :yaml:`restriction.disabled` is set to :yaml:`true`.

   .. code-block:: yaml

       hideAtCopy: true

.. confval:: appendLabelAtCopy
   :name: record-type-appendLabelAtCopy

   :Required: false
   :Type: string

   If set, the label field :yaml:`labelField` will be appended with this
   string, when copied.

   .. code-block:: yaml

       appendLabelAtCopy: append me
