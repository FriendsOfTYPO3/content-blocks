.. include:: /Includes.rst.txt
.. _yaml_reference:

==================================
Editing interface (YAML reference)
==================================

The heart of a content block is the `EditorInterface.yaml` file. Here you can
find all possible configuration options. There are slight differences, whether
you are dealing with Content Elements, Page Types or Record Types. In general
Content Elements and Page Types are a special concept in TYPO3. The Core already
defines the table names, the type field, etc. You just have to define a new
type. This is done by providing the :yaml:`name` attribute, which will be
converted to the type name. Page Types require an integer value for the type.
Therefore you need to set it additionally with :yaml:`typeName`.

With TYPO3 you can also create custom Record Types. They require you to define
a custom :yaml:`table`, and a :yaml:`useAsLabel` field. Per default all extra
features like workspaces, language support, frontend restrictions, etc. are
enabled. You can selectively disable each one of them, if you don't use them.

..  contents::
    :local:

General definitions
===================

.. confval:: name

   :Required: true
   :Type: string

   Every editing interface configuration must contain exactly one name. The name
   is made up of vendor and content block name separated by a `/` just like the
   `vendor/package` notation in a traditional composer.json file. It must be
   unique and must have at least 3 characters.

.. confval:: prefixFields

   :Required: false
   :Type: boolean
   :Default: true

   By default, all fields are prefixed with the name of the content block to
   prevent collisions. In order to better reuse fields between content blocks,
   it can be useful to deactivate this option. Read more about
   :ref:`reusing fields here <cb_reuse_existing_fields>`.

.. confval:: priority

   :Required: false
   :Type: integer
   :Default: 0

   The priority can be used to prioritize certain content blocks in the loading
   order. The default loading order is alphabetically. Higher priorities will be
   loaded before lower ones. This affects e.g. the order in the "New Content
   Element Wizard".

.. confval:: typeName

   :Required: false (Page Type: true)
   :Type: string
   :Default: automatically generated from :yaml:`name`

   The identifier of the new Content Type. It is automatically generated from
   the name, if not defined manually. This is required for Page Types.

.. confval:: fields

   :Required: false
   :Type: array

   The main entry point for the field definitions. Fields defined in this array
   are displayed in the backend exactly in the same order. You can create new
   custom fields or reuse existing ones, which are defined via TCA. Learn
   :ref:`here <yaml_reference_field_properties>` what is needed to define a
   field.

Record Types / Collections
==========================

.. _yaml_reference_record_type:

These options are only available for Content Blocks placed inside the
`ContentBlocks/RecordType` folder or for fields of type
:ref:`Collection <field_type_collection>`.

.. confval:: table

   :Required: true (false for Collections)
   :Type: string

   The custom table name to be used for the new Record Type. The table name for
   Collections is determined by the :yaml:`identifier` and thus should not be
   defined there.

.. confval:: typeField

   :Required: false
   :Type: string

   The field identifier to use as the type switch. This field will be
   automatically generated and prepended as the very first field. The item list
   is filled automatically as well. There is no need to define this field
   manually in your fields list. Useful, if you want to define multiple types
   for a single table (single table inheritance). Collections don't need this
   option, as they usually only have one type.

.. confval:: useAsLabel

   :Required: false (but highly recommended)
   :Type: string|array

   Defines which field should be used as the title of the record. If not
   defined, the first valid child field will be used as the label. It is
   possible to define an array of fields, which will be displayed
   comma-separated in the backend.

.. confval:: fallbackLabelFields

   :Required: false
   :Type: array

   Defines which fields should be used as fallback, if :yaml:`useAsLabel` is not
   filled. The first filled field which is found will be used. Can only be used,
   if there is only one :yaml:`useAsLabel` field defined.

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

.. confval:: languageAware

   :Required: false
   :Type: boolean
   :Default: true

   If set to :yaml:`false`, language related fields are not created. Namely
   :sql:`sys_language_uid`, :sql:`l10n_parent`, :sql:`l10n_source` and :sql:`l10n_diffsource`.

.. confval:: workspaceAware

   :Required: false
   :Type: boolean
   :Default: true

   If set to :yaml:`false`, workspace related fields are not created. Namely
   :sql:`t3ver_oid`, :sql:`t3ver_wsid`, :sql:`t3ver_state` and :sql:`t3ver_stage`.

.. confval:: editLocking

   :Required: false
   :Type: boolean
   :Default: true

   If set to :yaml:`false`, the functionality to lock the editing for editors is
   removed. This refers to the :sql:`editlock` field.

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

   Example:

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

.. confval:: trackCreationDate

   :Required: false
   :Type: boolean
   :Default: true

   Tracks the timestamp of the creation date. Disabling this option removes this
   information.

.. confval:: trackUpdateDate

   :Required: false
   :Type: boolean
   :Default: true

   Tracks the timestamp of the last update. Disabling this option removes this
   information.

.. confval:: trackAncestorReference

   :Required: false
   :Type: boolean
   :Default: true

   If set to :yaml:`false`, the tracking field for the original record will not
   be created. Namely :sql:`t3_origuid`.

.. confval:: sortable

   :Required: false
   :Type: boolean
   :Default: true

   Tracks the order of records. Arrows will appear to sort records explicitly.
   Disabling this option removes this functionality. This corresponds to the
   TCA option :php:`sortby`.

.. confval:: sortField

   :Required: false
   :Type: string @todo should be an array for multi field sorting
   :Default: true

   The field identifier to use for sorting records. If set, this will disable
   the :yaml:`sortable` option automatically. This corresponds to the TCA option
   :php:`default_sortby`.

.. confval:: internalDescription

   :Required: false
   :Type: boolean
   :Default: false

   If enabled, this adds a new tab `Notes` with a description field. When filled
   with text, a record information will be displayed in the editing view. This
   corresponds with the TCA ctrl option :php:`descriptionColumn`. This field is
   supposed to be used only for the backend.

.. confval:: rootLevelType

   :Required: false
   :Type: string
   :Default: onlyOnPages

   Restricts the place, where the record can be created. Possible values are
   :yaml:`onlyOnPages` (default), :yaml:`onlyOnRootLevel` and :yaml:`both`. This
   corresponds to the TCA ctrl option :php:`rootLevel`.

.. confval:: security

   :Required: false
   :Type: array

   :yaml:`ignoreWebMountRestriction`
      default false, Allows users to access records that are not in their defined web-mount, thus bypassing this restriction.

   :yaml:`ignoreRootLevelRestriction`
      default false, Allows non-admin users to access records that are on the root-level (page ID 0), thus bypassing this usual restriction.

   :yaml:`ignorePageTypeRestriction`
      default false, Allows to use the record on any kind of page type.

.. confval:: readOnly

   :Required: false
   :Type: boolean
   :Default: false

   If enabled, the record can not be edited in the TYPO3 backend anymore.

.. confval:: adminOnly

   :Required: false
   :Type: boolean
   :Default: false

   If enabled, only admins can edit the record.

.. confval:: hideAtCopy

   :Required: false
   :Type: boolean
   :Default: false

   If enabled, the record will be disabled, when copied. Only works, if
   :yaml:`restriction.disabled` is set to :yaml:`true`.

.. confval:: appendLabelAtCopy

   :Required: false
   :Type: string

   If set, the label field :yaml:`useAsLabel` will be appended with this
   string, when copied.

Field definitions
=================

Field options, which can be defined inside the :yaml:`fields` array.

Common field properties
-----------------------
.. _yaml_reference_field_properties:

.. confval:: identifier

   :Required: true
   :Type: string

   The field's identifier has to be unique within a Content Block. Exception is
   within a collections' field array, as this starts a new scope.

.. confval:: type

   :Required: true
   :Type: string

   The field's type. See :ref:`field_types`.

.. confval:: label

   :Required: false
   :Type: string

   By default labels should be defined inside the :file:`Labels.xml` file. But in
   case there is only one language for the backend you may define labels directly
   in the YAML configuration. This has precedence over translation files.

.. confval:: description

   :Required: false
   :Type: string

   The same as for `label` above.

.. confval:: useExistingField

   :Required: false
   :Type: bool

   If set to true, the identifier is treated as an existing field from the Core
   or your own defined field in TCA. To learn more about reusing fields read
   :ref:`this article <cb_reuse_existing_fields>`.

.. confval:: alternativeSql

   :Required: false
   :Type: string (SQL)

   It is possible to override the default SQL definition of a field with this
   option.

   Example:

   :sql:`tinyint(2) DEFAULT '0' NOT NULL`.

   Not every field type can be overridden. Have a look at the standard SQL
   definition of the corresponding field.

.. confval:: prefixField

   :Required: false
   :Type: boolean
   :Default: true

   If set to false, the prefixing is disabled for this field. This overrules the
   global option :yaml:`prefixFields`.

.. _field_types:

Field types
-----------

.. toctree::
    :maxdepth: 1
    :titlesonly:

    Basic/Index
    Category/Index
    Checkbox/Index
    Collection/Index
    Color/Index
    DateTime/Index
    Email/Index
    File/Index
    FlexForm/Index
    Folder/Index
    Linebreak/Index
    Link/Index
    Number/Index
    Palette/Index
    Radio/Index
    Reference/Index
    Select/Index
    Tab/Index
    Text/Index
    Textarea/Index
