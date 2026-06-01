.. include:: /Includes.rst.txt
.. _field_type_collection:

==========
Collection
==========

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
:confval:`foreign_table <collection-foreign-table>` instead.

Custom icon
===========

In order to define a custom icon for your Collection field, you may place an
image file inside **assets** folder called **{identifier}.svg**. So for example
if your identifier for the Collection is :yaml:`my_collection`, then your image
should be named **my_collection.svg**. Alternatively, you can also provide png
or gif files. These should be 64x64px.

Settings
========

.. tip::

    All options from :ref:`Record Types <yaml_reference_record_type_options>`
    can be used here as well.

..  confval-menu::
    :name: confval-collection-options
    :display: table
    :type:
    :default:
    :required:

.. confval:: labelField
   :name: collection-labelField
   :required: true
   :type: string|array

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
   :name: collection-fallbackLabelFields
   :required: false
   :type: array

   Defines which fields should be used as fallback, if :yaml:`labelField` is not
   filled. The first filled field which is found will be used. Can only be used
   if there is only one :yaml:`labelField` field defined.

   .. code-block:: yaml

       # fallback fields will be used, if title from labelField is empty
       labelField: title
       fallbackLabelFields:
           - text1
           - text2


.. confval:: table
   :name: collection-table
   :required: false
   :type: string

   Alternative table name for the Collection. Default is :yaml:`identifier` with
   prefix if enabled.

   .. code-block:: yaml

       table: tx_vendor_my_custom_table_name

.. confval:: fields
   :name: collection-fields
   :required: true
   :type: array

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
   :name: collection-minitems
   :required: false
   :type: integer
   :default: "0"

   Minimum number of child items. Defaults to 0. JavaScript record validation
   prevents the record from being saved if the limit is not satisfied.

.. confval:: maxitems
   :name: collection-maxitems
   :required: false
   :type: integer
   :default: "0"

   Maximum number of child items. Defaults to a high value. JavaScript record
   validation prevents the record from being saved if the limit is not satisfied.

.. confval:: relationship
   :name: collection-relationship
   :required: false
   :type: string
   :default: oneToMany

   The relationship defines the cardinality between the relations. Possible
   values are :yaml:`oneToMany` (default), :yaml:`manyToOne` and
   :yaml:`oneToOne`. In case of a [x]toOne relation, the processed field will
   be filled directly with the record instead of a collection of records. In
   addition, :yaml:`maxitems` will be automatically set to :yaml:`1`.

.. confval:: appearance.collapseAll
   :name: collection-appearance.collapseAll
   :required: false
   :type: bool|null
   :default: null

   * Default (null): Last collapsed/expanded state is remembered
   * true: Show all child records collapsed
   * false: Show all child records expanded

.. confval:: appearance.levelLinksPosition
   :name: collection-appearance.levelLinksPosition
   :required: false
   :type: string
   :default: top

   Defines where to show the "New record" link in relation to the child records.
   Valid keywords are :yaml:`top`, :yaml:`bottom` and :yaml:`both`.

.. confval:: appearance.expandSingle
   :name: collection-appearance.expandSingle
   :required: false
   :type: boolean

   When enabled, only one child record is expanded at a time. Clicking a
   collapsed record collapses the currently open one.

.. confval:: appearance.showNewRecordLink
   :name: collection-appearance.showNewRecordLink
   :required: false
   :type: boolean
   :default: true

   Show or hide the "New record" link.

.. confval:: appearance.newRecordLinkAddTitle
   :name: collection-appearance.newRecordLinkAddTitle
   :required: false
   :type: boolean

   Adds the title of the foreign table to the "New record" link label.

.. confval:: appearance.newRecordLinkTitle
   :name: collection-appearance.newRecordLinkTitle
   :required: false
   :type: string

   Overrides the "New record" link label with a localized string. Only takes
   effect if :yaml:`appearance.newRecordLinkAddTitle` is not set to true.

.. confval:: appearance.createNewRelationLinkTitle
   :name: collection-appearance.createNewRelationLinkTitle
   :required: false
   :type: string

   Overrides the "Create new relation" button label with a localized string.
   Only useful when the element browser is enabled.

.. confval:: appearance.useCombination
   :name: collection-appearance.useCombination
   :required: false
   :type: boolean
   :default: false

   Enables editing of intermediate table attributes alongside the related
   child record in bidirectional relations. Requires
   :yaml:`foreign_selector` and :yaml:`foreign_unique` to be set to the same
   field.

.. confval:: appearance.suppressCombinationWarning
   :name: collection-appearance.suppressCombinationWarning
   :required: false
   :type: boolean
   :default: false

   Suppresses the warning message shown when :yaml:`appearance.useCombination`
   is active.

.. confval:: appearance.useSortable
   :name: collection-appearance.useSortable
   :required: false
   :type: boolean
   :default: true

   Activates drag & drop sorting of child records.

.. confval:: appearance.showPossibleLocalizationRecords
   :name: collection-appearance.showPossibleLocalizationRecords
   :required: false
   :type: boolean

   Show unlocalized records that exist in the original language but have not
   yet been translated.

.. confval:: appearance.showAllLocalizationLink
   :name: collection-appearance.showAllLocalizationLink
   :required: false
   :type: boolean

   Show a "Localize all records" link to fetch untranslated records from the
   original language.

.. confval:: appearance.showSynchronizationLink
   :name: collection-appearance.showSynchronizationLink
   :required: false
   :type: boolean

   Show a "Synchronize" link to update to a 1:1 translation with the original
   language.

.. confval:: appearance.enabledControls
   :name: collection-appearance.enabledControls
   :required: false
   :type: object

   Enables or disables individual controls on child records. Available keys
   with their defaults:

   info (bool, default true)
      Show or hide the info control.

   new (bool, default true)
      Show or hide the "new" control.

   dragdrop (bool, default true)
      Show or hide the drag & drop handle.

   sort (bool, default false)
      Show or hide the sort arrows.

   hide (bool, default true)
      Show or hide the hide/show toggle.

   delete (bool, default true)
      Show or hide the delete control.

   localize (bool, default true)
      Show or hide the localize control.

   Example:

   .. code-block:: yaml

      appearance:
        enabledControls:
          sort: true
          delete: false

.. confval:: appearance.showPossibleRecordsSelector
   :name: collection-appearance.showPossibleRecordsSelector
   :required: false
   :type: boolean

   Hides the foreign record selector from the interface even when
   :yaml:`foreign_selector` is configured. Useful when replacing the selector
   with a custom control via :yaml:`customControls`.

.. confval:: appearance.elementBrowserEnabled
   :name: collection-appearance.elementBrowserEnabled
   :required: false
   :type: boolean

   Shows or hides the element browser button in inline records.

.. confval:: foreign_table
   :name: collection-foreign-table
   :required: false
   :type: string (table)

   It is possible to reference another table instead of creating a new one. This
   table can be defined by another :ref:`Content Block <yaml_reference_record_type_in_collection>`,
   but can also be an existing table defined by the Core or another extension.

   .. note::

      When you use :yaml:`foreign_table` it is not possible to define
      :yaml:`fields` anymore. They will not be evaluated.

.. confval:: foreign_field
   :name: collection-foreign_field
   :required: false
   :type: string (field)

   It is possible to override the field name pointing to the parent record. Per
   default it is called :sql:`foreign_table_parent_uid`. This corresponds with
   the TCA option :ref:`foreign_field <t3tca:columns-inline-properties-foreign-field>`.

.. confval:: shareAcrossTables
   :name: collection-shareAcrossTables
   :required: false
   :type: boolean
   :default: false

   Allows to reference a Record Type across multiple **tables**, if
   :confval:`foreign_table <collection-foreign-table>`
   is used.

   Make sure to add this to **every** Collection, which shares the `foreign_table`.

   This will create a new field called :sql:`tablenames`. It corresponds to the
   TCA option :ref:`foreign_table_field <t3tca:columns-inline-properties-foreign-table-field>`.
   The field name can be overridden by defining :yaml:`foreign_table_field` explicitly.

   For examples visit this :ref:`section <cb_share_options_explanation>`

.. confval:: shareAcrossFields
   :name: collection-shareAcrossFields
   :required: false
   :type: boolean
   :default: false

   Allows to reference a Record Type across multiple **fields**, if
   :confval:`foreign_table <collection-foreign-table>`
   is used.

   Make sure to add this to **every** Collection, which shares the `foreign_table`.

   This will create a new field called :sql:`fieldname`. It corresponds to the
   TCA option :ref:`foreign_match_fields <t3tca:columns-inline-properties-foreign-match-fields>`.

   For examples visit this :ref:`section <cb_share_options_explanation>`

.. confval:: allowedRecordTypes
   :name: collection-allowedRecordTypes
   :required: false
   :type: array
   :default: []

   This option allows you to restrict possible record types for the type
   selector of the child record. The order of definition is used to sort the
   items. The first item in the list will always be the default type.

   .. code-block:: yaml

      allowedRecordTypes:
        - text
        - images

.. confval:: autoSizeMax
   :name: collection-autoSizeMax
   :required: false
   :type: integer

   The inline field will never grow larger than this number of items before
   a scrollbar appears.

.. confval:: behaviour.allowLanguageSynchronization
   :name: collection-behaviour.allowLanguageSynchronization
   :required: false
   :type: boolean
   :default: false

   Allows to select if localization uses custom or default language value.

.. confval:: behaviour.disableMovingChildrenWithParent
   :name: collection-behaviour.disableMovingChildrenWithParent
   :required: false
   :type: boolean
   :default: false

   Disables automatic moving of child records when the parent record is moved.

.. confval:: behaviour.enableCascadingDelete
   :name: collection-behaviour.enableCascadingDelete
   :required: false
   :type: boolean
   :default: true

   When disabled, child records are not deleted when the parent record is
   deleted.

.. confval:: customControls
   :name: collection-customControls
   :required: false
   :type: array

   A list of custom controls (user functions) used to extend the inline field
   interface. Each entry requires a :yaml:`userFunc` key.

.. confval:: fieldControl
   :name: collection-fieldControl
   :required: false
   :type: object

   See :ref:`TCA fieldControl <t3tca:tca_property_fieldControl>`.

.. confval:: fieldInformation
   :name: collection-fieldInformation
   :required: false
   :type: object

   See :ref:`TCA fieldInformation <t3tca:tca_property_fieldInformation>`.

.. confval:: fieldWizard
   :name: collection-fieldWizard
   :required: false
   :type: object

   See :ref:`TCA fieldWizard <t3tca:tca_property_fieldWizard>`.

.. confval:: filter
   :name: collection-filter
   :required: false
   :type: array

   Defines filters for item values. Each entry requires a :yaml:`userFunc`
   key and an optional :yaml:`parameters` object. Does not work in combination
   with a wizard.

.. confval:: foreign_default_sortby
   :name: collection-foreign_default_sortby
   :required: false
   :type: string

   ORDER BY clause for sorting child records when :yaml:`foreign_sortby` is
   not defined.

.. confval:: foreign_label
   :name: collection-foreign_label
   :required: false
   :type: string

   Field name of the child record to use as the inline element title.

.. confval:: foreign_match_fields
   :name: collection-foreign_match_fields
   :required: false
   :type: object

   Field-value pairs inserted into and matched against when writing/reading
   IRRE relations. Allows reusing the same child table across multiple parent
   fields.

.. confval:: foreign_selector
   :name: collection-foreign_selector
   :required: false
   :type: string

   Field name of the child record used as a selector to show all possible
   related records.

.. confval:: foreign_sortby
   :name: collection-foreign_sortby
   :required: false
   :type: string

   Field on the child record that stores the manual sort order.

.. confval:: foreign_table_field
   :name: collection-foreign_table_field
   :required: false
   :type: string

   Field on the child record pointing to the parent table name. Used together
   with :yaml:`shareAcrossTables`.

.. confval:: foreign_unique
   :name: collection-foreign_unique
   :required: false
   :type: string

   Field on the child record that must be unique across all children of a
   parent record.

.. confval:: MM
   :name: collection-MM
   :required: false
   :type: string

   Table name for storing the MM relation. Used together with
   :yaml:`foreign_table`.

.. confval:: MM_opposite_field
   :name: collection-MM_opposite_field
   :required: false
   :type: string

   Enables bidirectional MM relations. Set to the field name on the local side
   when configuring the foreign side.

.. confval:: overrideChildTca
   :name: collection-overrideChildTca
   :required: false
   :type: object

   Overrides TCA configuration of child records (e.g. :sql:`sys_file_reference`)
   attached to this field.

.. confval:: readOnly
   :name: collection-readOnly
   :required: false
   :type: boolean
   :default: false

   Renders the field in a way that the user can see the value but cannot edit it.

.. confval:: size
   :name: collection-size
   :required: false
   :type: integer
   :default: 1

   Number of visible rows in the inline field.

.. confval:: symmetric_field
   :name: collection-symmetric_field
   :required: false
   :type: string

   Works like :yaml:`foreign_field` for bidirectional symmetric relations.
   Defines the field storing the uid of the "other" parent.

.. confval:: symmetric_label
   :name: collection-symmetric_label
   :required: false
   :type: string

   Overrides the label defined in :yaml:`labelField` when viewing a symmetric
   relation from the other side.

.. confval:: symmetric_sortby
   :name: collection-symmetric_sortby
   :required: false
   :type: string

   Works like :yaml:`foreign_sortby` for bidirectional symmetric relations.

.. confval:: overrideType
   :name: collection-overrideType
   :required: false
   :type: array
   :default: []

   Type Overrides can be used to override the Record Definition in the context of
   as single field. This option only makes sense, if you don't define :yaml:`fields`,
   but an external :yaml:`foreign_table`, that you want to override.
   Refer to the :ref:`API documentation <api_type_overrides>` if you want to
   learn more.

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
