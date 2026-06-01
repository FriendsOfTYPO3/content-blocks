.. include:: /Includes.rst.txt
.. _field_type_relation:

========
Relation
========

The :yaml:`Relation` type can handle relations to other record types. They will
be available to select from the Record Selector.

Settings
========

..  confval-menu::
    :name: confval-relation-options
    :display: table
    :type:
    :default:
    :required:

.. confval:: allowed
   :name: relation-allowed
   :required: true
   :type: string (table name, comma-separated)

   One or more tables, that should be referenced.

   This table can be defined by another Content Block, but can also be an
   existing table defined by the Core or another extension.

.. confval:: maxitems
   :name: relation-maxitems
   :required: false
   :type: integer

   Maximum number of items. Defaults to a high value. JavaScript record
   validation prevents the record from being saved if the limit is not satisfied.

.. confval:: minitems
   :name: relation-minitems
   :required: false
   :type: integer

   Minimum number of items. Default is no minimum. JavaScript record validation
   prevents the record from being saved if the limit is not satisfied.
   The field can be set as required by setting :yaml:`minitems` to at least 1.

.. confval:: relationship
   :name: relation-relationship
   :required: false
   :type: string
   :default: oneToMany

   The relationship defines the cardinality between the relations. Possible
   values are :yaml:`oneToMany` (default), :yaml:`manyToOne` and
   :yaml:`oneToOne`. In case of a [x]toOne relation, the processed field will
   be filled directly with the record instead of a collection of records. In
   addition, :yaml:`maxitems` will be automatically set to :yaml:`1`.

.. confval:: autoSizeMax
   :name: relation-autoSizeMax
   :required: false
   :type: integer

   The field will never grow larger than this number of visible rows.

.. confval:: behaviour.allowLanguageSynchronization
   :name: relation-behaviour.allowLanguageSynchronization
   :required: false
   :type: boolean
   :default: false

   Allows to select if localization uses custom or default language value.

.. confval:: default
   :name: relation-default
   :required: false
   :type: string|integer

   Default value set if a new record is created.

.. confval:: dontRemapTablesOnCopy
   :name: relation-dontRemapTablesOnCopy
   :required: false
   :type: array

   A list of tables which should not be remapped to the new element uids if
   the field holds elements that are copied in the session.

.. confval:: elementBrowserEntryPoints
   :name: relation-elementBrowserEntryPoints
   :required: false
   :type: object

   Changes the default starting point when opening the element browser. Use
   the key :yaml:`_default` to set the default page uid.

   Example:

   .. code-block:: yaml

      elementBrowserEntryPoints:
        _default: 42

.. confval:: fieldControl
   :name: relation-fieldControl
   :required: false
   :type: object

   Enables or disables individual controls next to the field. Each control
   accepts a :yaml:`disabled` boolean. Available controls:
   :yaml:`addRecord`, :yaml:`editPopup`, :yaml:`listModule`,
   :yaml:`elementBrowser`, :yaml:`insertClipboard`.

   See :ref:`TCA fieldControl <t3tca:tca_property_fieldControl>`.

.. confval:: fieldInformation
   :name: relation-fieldInformation
   :required: false
   :type: object

   See :ref:`TCA fieldInformation <t3tca:tca_property_fieldInformation>`.

.. confval:: fieldWizard
   :name: relation-fieldWizard
   :required: false
   :type: object

   See :ref:`TCA fieldWizard <t3tca:tca_property_fieldWizard>`.

.. confval:: filter
   :name: relation-filter
   :required: false
   :type: array

   Defines filters for item values. Each entry requires a :yaml:`userFunc`
   key and an optional :yaml:`parameters` object. Does not work in combination
   with a wizard.

.. confval:: foreign_table
   :name: relation-foreign_table
   :required: false
   :type: string

   Restricts selectable records to a specific table. Can be used instead of
   or together with :yaml:`allowed`.

.. confval:: hideDeleteIcon
   :name: relation-hideDeleteIcon
   :required: false
   :type: boolean

   Removes the delete icon next to the selector box.

.. confval:: hideMoveIcons
   :name: relation-hideMoveIcons
   :required: false
   :type: boolean

   Removes the move icons next to the selector box.

.. confval:: hideSuggest
   :name: relation-hideSuggest
   :required: false
   :type: boolean

   Disables the suggest (autocomplete) wizard.

.. confval:: localizeReferencesAtParentLocalization
   :name: relation-localizeReferencesAtParentLocalization
   :required: false
   :type: boolean

   Defines whether referenced records should be localized when the current
   record gets localized. Only applies if references are not stored using MM
   tables.

.. confval:: MM
   :name: relation-MM
   :required: false
   :type: string

   Table name for storing the MM relation. Used together with
   :yaml:`foreign_table`.

.. confval:: MM_match_fields
   :name: relation-MM_match_fields
   :required: false
   :type: object

   Field-value pairs to both insert and match against when writing/reading MM
   relations.

.. confval:: MM_opposite_field
   :name: relation-MM_opposite_field
   :required: false
   :type: string

   Enables bidirectional MM relations. Set to the field name on the local side
   when configuring the foreign side.

.. confval:: MM_oppositeUsage
   :name: relation-MM_oppositeUsage
   :required: false
   :type: object

   Required on the opposite side of a bidirectional MM relation that uses match
   fields.

.. confval:: MM_table_where
   :name: relation-MM_table_where
   :required: false
   :type: string

   Additional WHERE clause used when reading MM relations.

.. confval:: multiple
   :name: relation-multiple
   :required: false
   :type: boolean
   :default: false

   Allows the same record to be selected more than once in the list.

.. confval:: prepend_tname
   :name: relation-prepend_tname
   :required: false
   :type: boolean

   When enabled, the table name is prepended to the stored relation uids.

.. confval:: readOnly
   :name: relation-readOnly
   :required: false
   :type: boolean
   :default: false

   Renders the field in a way that the user can see the value but cannot edit it.

.. confval:: size
   :name: relation-size
   :required: false
   :type: integer
   :default: 1

   Number of visible rows in the selector box. A value of 1 displays a
   drop-down.

.. confval:: suggestOptions
   :name: relation-suggestOptions
   :required: false
   :type: object

   Configuration for the suggest (autocomplete) wizard. Keys are table names
   or :yaml:`default` to apply to all tables. Each entry supports:

   additionalSearchFields (string)
      Comma-separated list of extra fields to search in.

   addWhere (string)
      Additional WHERE clause appended to the search query.

   cssClass (string)
      CSS class added to every result list item.

   maxItemsInResultList (integer, default 10)
      Maximum number of results to display.

   maxPathTitleLength (integer)
      Maximum characters shown for a path element.

   minimumCharacters (integer)
      Minimum characters required to trigger the search.

   orderBy (string)
      ORDER BY clause for the search query.

   pidList (string)
      Comma-separated page uids to limit the search scope.

   pidDepth (integer)
      Number of sub-page levels to expand from :yaml:`pidList`.

   receiverClass (string)
      Alternative PHP receiver class for the suggest wizard.

   renderFunc (string)
      User function to manipulate the displayed results.

   searchCondition (string)
      Additional WHERE clause (not prepended with AND).

   searchWholePhrase (boolean)
      Searches for the whole phrase rather than only the beginning.

   Example:

   .. code-block:: yaml

      suggestOptions:
        default:
          additionalSearchFields: 'nav_title, url'
          addWhere: 'AND pages.doktype = 1'
          minimumCharacters: 2

Examples
========

Minimal
-------

.. code-block:: yaml

    name: example/relation
    fields:
      - identifier: record_select
        type: Relation
        allowed: 'some_table'

Advanced / use case
-------------------

.. code-block:: yaml

    name: example/relation
    fields:
      - identifier: page_select
        type: Relation
        allowed: 'pages'
        maxitems: 1
        suggestOptions:
          default:
            additionalSearchFields: 'nav_title, url'
            addWhere: 'AND pages.doktype = 1'
