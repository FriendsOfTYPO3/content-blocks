.. include:: /Includes.rst.txt
.. _field_type_select:

======
Select
======

The :yaml:`Select` type generates a simple select field.

Settings
========

..  confval-menu::
    :name: confval-select-options
    :display: table
    :type:
    :default:
    :required:

.. confval:: renderType
   :name: select-renderType
   :required: yes
   :type: string

   *  :yaml:`selectSingle`
   *  :yaml:`selectCheckBox`
   *  :yaml:`selectSingleBox`
   *  :yaml:`selectTree`
   *  :yaml:`selectMultipleSideBySide`

.. confval:: items
   :name: select-items
   :required: false
   :type: array

   Contains the elements for the selector box. Each item is an array. An item
   consists of a :yaml:`label` and a :yaml:`value`.

   Example:

   .. code-block:: yaml

      items:
        - label: 'The first'
          value: one
        - label: 'The second'
          value: two
        - label: 'The third'
          value: three

   .. tip::

      You can omit the label, if you have the translation already in your
      labels.xlf file.

      .. code-block:: yaml

          items:
            - value: one
            - value: two
            - value: three

   .. tip::

      You can also use icons so they are displayed in the backend.
      See :ref:`select-icons` for a full example.

      .. code-block:: yaml

          items:
            - value: image-left
              icon: content-beside-text-img-left
            - value: image-right
              icon: content-beside-text-img-right
            - value: image-above
              icon: content-beside-text-img-above-center

      For this you need the following setting according to the :ref:`TCA documentation <t3tca:tca_property_fieldWizard_selectIcons>`.

      .. code-block:: yaml

          fieldWizard:
            selectIcons:
              disabled: false

   XLF translation keys for items have the following convention:

   .. code-block:: xml

        <body>
            <trans-unit id="FIELD_IDENTIFIER.items.one.label">
                <source>Label for item with value one</source>
            </trans-unit>
            <trans-unit id="FIELD_IDENTIFIER.items.two.label">
                <source>Label for item with value two</source>
            </trans-unit>
            <trans-unit id="FIELD_IDENTIFIER.items.VALUE.label">
                <source>Label for item with value VALUE</source>
            </trans-unit>
            <trans-unit id="FIELD_IDENTIFIER.items.label">
                <source>Label for item with empty value</source>
            </trans-unit>
        </body>

.. confval:: default
   :name: select-default
   :required: false
   :type: string

   Default value set if a new record is created.

.. confval:: maxitems
   :name: select-maxitems
   :required: false
   :type: integer

   Maximum number of child items. Defaults to a high value. JavaScript record
   validation prevents the record from being saved if the limit is not satisfied.
   If `maxitems` ist set to greater than 1, multiselect is automatically enabled.

.. confval:: minitems
   :name: select-minitems
   :required: false
   :type: integer

   Minimum number of items. Default is no minimum. JavaScript record validation
   prevents the record from being saved if the limit is not satisfied.
   The field can be set as required by setting :yaml:`minitems` to at least 1.

.. confval:: relationship
   :name: select-relationship
   :required: false
   :type: string
   :default: oneToMany

   .. note::

      This can only be used in combination with :yaml:`foreign_table`.

   The relationship defines the cardinality between the relations. Possible
   values are :yaml:`oneToMany` (default), :yaml:`manyToOne` and
   :yaml:`oneToOne`. In case of a [x]toOne relation, the processed field will
   be filled directly with the record instead of a collection of records. In
   addition, :yaml:`maxitems` will be automatically set to :yaml:`1`. If the
   :yaml:`renderType` is set to :yaml:`selectSingle`, a relationship
   :yaml:`manyToOne` is automatically inferred.

.. confval:: dbFieldLength
   :name: select-dbFieldLength
   :required: false
   :type: int
   :default: 255

   This option can be used to set an alternative size for the database
   :sql:`varchar` column. The default size is `255`.

.. confval:: allowedCustomProperties
   :name: select-allowedCustomProperties
   :required: false
   :type: array
   :default: ["itemsProcConfig"]

   Sometimes it is needed to provide custom configuration for the :ref:`itemsProcFunc <t3tca:tca_property_itemsProcFunc>`
   functionality. These extra properties need to be explicitly allowed via this
   option. This option receives an array of those strings. By default, the
   custom option :yaml:`itemsProcConfig` is allowed.

.. confval:: allowNonIdValues
   :name: select-allowNonIdValues
   :required: false
   :type: boolean
   :default: false

   Only useful if :yaml:`foreign_table` is set. If enabled, values which are
   not integer ids will be allowed.

.. confval:: appearance.expandAll
   :name: select-appearance.expandAll
   :required: false
   :type: boolean

   All select groups are initially expanded.

.. confval:: authMode
   :name: select-authMode
   :required: false
   :type: string

   Authorization mode for the selector box. Possible value:

   *  :yaml:`explicitAllow`

.. confval:: autoSizeMax
   :name: select-autoSizeMax
   :required: false
   :type: integer

   The select field will never be smaller than :yaml:`size` and never larger
   than this value.

.. confval:: behaviour.allowLanguageSynchronization
   :name: select-behaviour.allowLanguageSynchronization
   :required: false
   :type: boolean
   :default: false

   Allows to select if localization uses custom or default language value.

.. confval:: disableNoMatchingValueElement
   :name: select-disableNoMatchingValueElement
   :required: false
   :type: boolean

   If set, no placeholder element is inserted when the current value does not
   match any of the existing items.

.. confval:: dontRemapTablesOnCopy
   :name: select-dontRemapTablesOnCopy
   :required: false
   :type: array

   A list of tables which should not be remapped to the new element uids if
   the field holds elements that are copied in the session.

.. confval:: exclusiveKeys
   :name: select-exclusiveKeys
   :required: false
   :type: string

   Comma-separated list of keys that exclude any other keys in a multi-select
   box.

.. confval:: fieldControl
   :name: select-fieldControl
   :required: false
   :type: object

   See :ref:`TCA fieldControl <t3tca:tca_property_fieldControl>`.

.. confval:: fieldInformation
   :name: select-fieldInformation
   :required: false
   :type: object

   See :ref:`TCA fieldInformation <t3tca:tca_property_fieldInformation>`.

.. confval:: fieldWizard
   :name: select-fieldWizard
   :required: false
   :type: object

   See :ref:`TCA fieldWizard <t3tca:tca_property_fieldWizard>`.

.. confval:: fileFolderConfig.allowedExtensions
   :name: select-fileFolderConfig.allowedExtensions
   :required: false
   :type: string

   List of file extensions to select. If blank, all files are selected.

.. confval:: fileFolderConfig.depth
   :name: select-fileFolderConfig.depth
   :required: false
   :type: integer
   :default: 99

   Depth of directory recursion when listing files from
   :yaml:`fileFolderConfig.folder`.

.. confval:: fileFolderConfig.folder
   :name: select-fileFolderConfig.folder
   :required: false
   :type: string

   Path to the folder from which files are added to the item array.

.. confval:: foreign_table
   :name: select-foreign_table
   :required: false
   :type: string

   The item array will be filled with records from this table.

.. confval:: foreign_table_item_group
   :name: select-foreign_table_item_group
   :required: false
   :type: string

   References a field in the foreign table that holds an item group identifier.

.. confval:: foreign_table_prefix
   :name: select-foreign_table_prefix
   :required: false
   :type: string

   Label prefix applied to the title of records from the foreign table.

.. confval:: foreign_table_where
   :name: select-foreign_table_where
   :required: false
   :type: string

   WHERE clause used when selecting items from :yaml:`foreign_table`.

.. confval:: itemGroups
   :name: select-itemGroups
   :required: false
   :type: object

   Key-value pairs of item group identifiers and their labels. Items can
   reference a group via the :yaml:`group` key.

.. confval:: itemsProcessors
   :name: select-itemsProcessors
   :required: false
   :type: object

   A list of PHP classes called to fill or manipulate the items array. Each
   entry is keyed by a numeric index and requires a :yaml:`class` property.
   An optional :yaml:`parameters` object can be passed to the processor.

   Example:

   .. code-block:: yaml

      itemsProcessors:
        0:
          class: 'Vendor\Extension\ItemsProcessor\MyProcessor'
          parameters:
            foo: bar

.. confval:: itemsProcFunc
   :name: select-itemsProcFunc
   :required: false
   :type: string

   .. deprecated:: 2.3.0

      Use :yaml:`itemsProcessors` instead.

   PHP method which is called to fill or manipulate the items array. See
   :ref:`TCA itemsProcFunc <t3tca:tca_property_itemsProcFunc>`.

.. confval:: itemsProcConfig
   :name: select-itemsProcConfig
   :required: false
   :type: object

   Additional configuration passed to :yaml:`itemsProcFunc`. Must be listed in
   :yaml:`allowedCustomProperties` (included by default).

.. confval:: localizeReferencesAtParentLocalization
   :name: select-localizeReferencesAtParentLocalization
   :required: false
   :type: boolean

   Defines whether referenced records should be localized when the current
   record gets localized. Only applies if references are not stored using MM
   tables.

.. confval:: MM
   :name: select-MM
   :required: false
   :type: string

   Table name for storing the MM relation. Used together with
   :yaml:`foreign_table`.

.. confval:: MM_match_fields
   :name: select-MM_match_fields
   :required: false
   :type: object

   Field-value pairs to both insert and match against when writing/reading MM
   relations.

.. confval:: MM_opposite_field
   :name: select-MM_opposite_field
   :required: false
   :type: string

   Enables bidirectional MM relations. Set to the field name on the local side
   when configuring the foreign side.

.. confval:: MM_oppositeUsage
   :name: select-MM_oppositeUsage
   :required: false
   :type: object

   Required on the opposite side of a bidirectional MM relation that uses match
   fields.

.. confval:: MM_table_where
   :name: select-MM_table_where
   :required: false
   :type: string

   Additional WHERE clause used when reading MM relations.

.. confval:: multiple
   :name: select-multiple
   :required: false
   :type: boolean
   :default: false

   Allows the same item to be selected more than once in a list.

.. confval:: readOnly
   :name: select-readOnly
   :required: false
   :type: boolean
   :default: false

   Renders the field in a way that the user can see the value but cannot edit it.

.. confval:: size
   :name: select-size
   :required: false
   :type: integer
   :default: 1

   If set to 1, displays a select drop-down. A higher value renders a select
   box of the given number of visible rows.

.. confval:: sortItems
   :name: select-sortItems
   :required: false
   :type: object

   Sorts the items by :yaml:`label` or :yaml:`value` in ascending
   (:yaml:`asc`) or descending (:yaml:`desc`) order.

   Example:

   .. code-block:: yaml

      sortItems:
        label: asc

.. confval:: treeConfig.childrenField
   :name: select-treeConfig.childrenField
   :required: false
   :type: string

   Field name of the :yaml:`foreign_table` that references the uid of the
   child records. Required when :yaml:`renderType` is :yaml:`selectTree` and
   no :yaml:`treeConfig.parentField` is set.

.. confval:: treeConfig.parentField
   :name: select-treeConfig.parentField
   :required: false
   :type: string

   Field name of the :yaml:`foreign_table` that references the uid of the
   parent record. Required when :yaml:`renderType` is :yaml:`selectTree` and
   no :yaml:`treeConfig.childrenField` is set.

.. confval:: treeConfig.dataProvider
   :name: select-treeConfig.dataProvider
   :required: false
   :type: string

   Custom data provider class for use cases where special data preparation is
   necessary.

.. confval:: treeConfig.startingPoints
   :name: select-treeConfig.startingPoints
   :required: false
   :type: string

   Allows setting multiple records as roots for tree records.

.. confval:: treeConfig.appearance.expandAll
   :name: select-treeConfig.appearance.expandAll
   :required: false
   :type: boolean

   All tree nodes are initially expanded.

.. confval:: treeConfig.appearance.maxLevels
   :name: select-treeConfig.appearance.maxLevels
   :required: false
   :type: integer

   The maximum number of levels to render. Can be used to prevent recursion.

.. confval:: treeConfig.appearance.nonSelectableLevels
   :name: select-treeConfig.appearance.nonSelectableLevels
   :required: false
   :type: string

   Comma-separated list of levels that cannot be selected. Defaults to
   :yaml:`"0"` (the root node).

.. confval:: treeConfig.appearance.showHeader
   :name: select-treeConfig.appearance.showHeader
   :required: false
   :type: boolean

   Whether to show the tree header containing a filter field and
   expand/collapse-all buttons.

Example
=======

Minimal
-------

Select single:

.. code-block:: yaml

    name: example/select
    fields:
      - identifier: select
        type: Select
        renderType: selectSingle
        items:
          - label: 'The first'
            value: one
          - label: 'The second'
            value: two

Select multiple:

.. code-block:: yaml

    name: example/select
    fields:
      - identifier: select_side_by_side
        type: Select
        renderType: selectMultipleSideBySide
        items:
          - label: 'The first'
            value: one
          - label: 'The second'
            value: two

Advanced / use case
-------------------

Select single:

.. code-block:: yaml

    name: example/select
    fields:
      - identifier: select
        type: Select
        renderType: selectSingle
        default: 'one'
        items:
          - label: 'The first'
            value: one
          - label: 'The second'
            value: two
          - label: 'The third'
            value: three
        foreign_table: pages
        foreign_table_where: 'AND {#pages}.{#pid} = 123 ORDER BY uid'

Select multiple:

.. code-block:: yaml

    name: example/select
    fields:
      - identifier: select_side_by_side
        type: Select
        renderType: selectMultipleSideBySide
        default: 'one'
        minitems: 1
        maxitems: 3
        items:
          - label: 'The first'
            value: one
          - label: 'The second'
            value: two
          - label: 'The third'
            value: three
        foreign_table: pages
        foreign_table_where: 'AND {#pages}.{#pid} = 123 ORDER BY uid'

Select tree:

.. code-block:: yaml

    name: example/select
    fields:
      - identifier: select_tree
        type: Select
        renderType: selectTree
        size: 5
        foreign_table: 'pages'
        foreign_table_where: 'ORDER BY pages.uid'
        treeConfig:
          parentField: pid


..  _select-icons:

Select with icons:

.. code-block:: yaml

   name: example/select
   fields:
     - identifier: select_icons
       type: Select
       renderType: selectSingle
       fieldWizard:
         selectIcons:
           disabled: false
       default: 'image-left'
       items:
         - label: 'Image beside text (left)'
           value: image-left
           icon: content-beside-text-img-left
         - label: 'Image beside text (right)'
           value: image-right
           icon: content-beside-text-img-right
         - label: 'Image above text (center)'
           value: image-above
           icon: content-beside-text-img-above-cent
