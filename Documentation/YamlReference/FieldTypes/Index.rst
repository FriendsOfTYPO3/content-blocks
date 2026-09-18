.. include:: /Includes.rst.txt
.. _field_types:

===========
Field Types
===========

These are the Content Blocks Field Types, which you can use out of the box. They
cover mostly basic types. :ref:`Custom Field Types <extending-field-types>` can
be developed from scratch. More semantic field types may be added over time.

If you are familiar with :ref:`TCA types <t3tca:columns-types>`, then you will
probably recognize most of these types. Some types have been renamed for better
clarity.

.. note::

    The documented field options here are not complete. You are allowed to use
    every option, which you can also find in the :ref:`TCA <t3tca:columns-types>`
    documentation. Have a look at the :ref:`TCA type mapping <type_mapping>` if
    you are unsure, which field type is the counterpart to the TCA type.

Simple Field Types:

..  rst-class:: horizbuttons-attention-m

*  :ref:`Checkbox <field_type_checkbox>`
*  :ref:`Color <field_type_color>`
*  :ref:`DateTime <field_type_datetime>`
*  :ref:`Email <field_type_email>`
*  :ref:`Link <field_type_link>`
*  :ref:`Number <field_type_number>`
*  :ref:`Password <field_type_password>`
*  :ref:`Radio <field_type_radio>`
*  :ref:`SelectNumber <field_type_select-number>`
*  :ref:`SelectText <field_type_select-text>`
*  :ref:`Slug <field_type_slug>`
*  :ref:`Text <field_type_text>`
*  :ref:`Textarea <field_type_textarea>`
*  :ref:`Uuid <field_type_uuid>`

Relational Field Types:

..  rst-class:: horizbuttons-attention-m

*  :ref:`Category <field_type_category>`
*  :ref:`Collection <field_type_collection>`
*  :ref:`File <field_type_file>`
*  :ref:`Folder <field_type_folder>`
*  :ref:`Language <field_type_language>`
*  :ref:`Relation <field_type_relation>`
*  :ref:`Select <field_type_select>`

Structural Field Types:

..  rst-class:: horizbuttons-attention-m

*  :ref:`FlexForm <field_type_flexform>`
*  :ref:`Json <field_type_json>`

Special field types:

..  rst-class:: horizbuttons-attention-m

*  :ref:`Basic <field_type_basic>`
*  :ref:`Linebreak <field_type_linebreak>`
*  :ref:`Palette <field_type_palette>`
*  :ref:`Pass <field_type_pass>`
*  :ref:`Tab <field_type_tab>`

Common field options
====================

.. _yaml_reference_field_properties:

Field options, which can be defined inside the :yaml:`fields` array.

..  confval-menu::
    :name: confval-common-field-options
    :display: table
    :type:
    :default:
    :required:

.. confval:: identifier
   :name: field-types-identifier
   :required: true
   :type: string

   The field's identifier has to be unique within a Content Block. Exception is
   within a collections' field array, as this starts a new scope.

   .. warning::

      Avoid using dashes "-" inside your identifiers. They are not guaranteed to
      be escaped in the database. We recommend to always use snake case.

   .. code-block:: yaml

       fields:
         - identifier: my_identifier
           type: Text

.. confval:: type
   :name: field-types-type
   :required: true
   :type: string

   The field's type. See :ref:`field_types`.

   .. code-block:: yaml

       fields:
         - identifier: my_identifier
           type: Text

.. confval:: label
   :name: field-types-label
   :required: false
   :type: string

   By default labels should be defined inside the :file:`labels.xlf` file. But in
   case there is only one language for the backend you may define labels directly
   in the YAML configuration. Translation files have precedence over this.

   .. code-block:: yaml

       fields:
         - identifier: my_identifier
           type: Text
           label: Static label

.. confval:: description
   :name: field-types-description
   :required: false
   :type: string

   The same as for `label` above.

   .. code-block:: yaml

       fields:
         - identifier: my_identifier
           type: Text
           description: Static description

.. confval:: useExistingField
   :name: field-types-useExistingField
   :required: false
   :type: bool
   :default: false

   If set to true, the identifier is treated as an existing field from the Core
   or your own defined field in TCA. To learn more about reusing fields read
   :ref:`this article <cb_reuse_existing_fields>`.

   .. code-block:: yaml

       fields:
         - identifier: bodytext
           useExistingField: true

.. confval:: prefixField
   :name: field-types-prefixField
   :required: false
   :type: boolean
   :default: true

   If set to false, the prefixing is disabled for this field. This overrules the
   global option :confval:`prefixFields <root-prefixFields>`.

   .. code-block:: yaml

       fields:
         - identifier: my_identifier
           type: Text
           prefixField: false

   Read more about :ref:`prefixing <api_prefixing>`.

.. confval:: prefixType
   :name: field-types-prefixType
   :required: false
   :type: string
   :default: full

   Determines how to prefix the field if local :yaml:`prefixField` or global
   :yaml:`prefixFields` is enabled. Can be either :yaml:`full` (default) or
   :yaml:`vendor`.

   .. code-block:: yaml

       fields:
         - identifier: my_identifier
           type: Text
           prefixField: true
           prefixType: vendor

   Read more about :ref:`prefixing <api_prefixing>`.

.. confval:: alias
   :name: field-types-alias
   :required: false
   :type: string

   Defines an alias for the :yaml:`identifier`. This alias is used in Fluid
   templates and everywhere else you need to access the field value. There is
   no fallback to the identifier. If set, you have to migrate all existing
   occurrences to the new alias.

   .. code-block:: yaml

       fields:
         - identifier: my_identifier
           type: Text
           alias: myAliasIdentifier

.. confval:: displayCond
   :name: field-types-displayCond
   :required: false
   :type: string|array

   Can be used to display the field only under certain conditions.
   Please have a look at the :ref:`official documentation <t3tca:columns-properties-displaycond>`
   for more information.

   .. code-block:: yaml

       # Simple, only one rule.
       displayCond: 'FIELD:identifier:=:value'

   .. code-block:: yaml

       # Multiple rules combined with AND.
       displayCond:
         AND:
           - 'FIELD:identifier:=:value'
           - 'FIELD:another_identifier:=:1'

   .. tip::

      Fields used in a condition should have the column option :yaml:`onChange`
      set to :yaml:`reload`.

.. confval:: onChange
   :name: field-types-onChange
   :required: false
   :type: string

   Can be used to trigger a reload of the Content Type when this specific
   field is changed. Should be used, if a rule of :yaml:`displayCond` is used
   for this field.

   .. code-block:: yaml

      onChange: reload

.. confval:: l10n_mode
   :name: field-types-l10n_mode
   :required: false
   :type: string

   Controls how the field behaves in localized records. Possible values are
   :yaml:`exclude` (the value of the default language record is kept and the
   field is not shown) and :yaml:`prefixLangTitle` (the value is copied and
   prefixed with the language title on localization). See
   :ref:`l10n_mode <t3tca:columns-properties-l10n-mode>`.

   .. code-block:: yaml

       fields:
         - identifier: my_identifier
           type: Text
           l10n_mode: exclude

.. confval:: l10n_display
   :name: field-types-l10n_display
   :required: false
   :type: string

   Controls how the field is displayed in localized records. Possible values are
   :yaml:`hideDiff` (hides the diff view of the default language value) and
   :yaml:`defaultAsReadonly` (shows the default language value as read-only). See
   :ref:`l10n_display <t3tca:columns-properties-l10n-display>`.

   .. code-block:: yaml

       fields:
         - identifier: my_identifier
           type: Text
           l10n_display: defaultAsReadonly

.. confval:: exclude
   :name: field-types-exclude
   :required: false
   :type: boolean
   :default: true

   If set, backend users can only edit this field if their backend user group
   explicitly allows it. Content Blocks enables this by default, contrary to
   TCA, where it defaults to :yaml:`false`. See
   :ref:`exclude <t3tca:columns-properties-exclude>`.

   .. code-block:: yaml

       fields:
         - identifier: my_identifier
           type: Text
           exclude: false

.. confval:: renderType
   :name: field-types-renderType
   :required: false
   :type: string

   Selects an alternative FormEngine element for the field. Only available for
   the field types which document it, as core TCA only defines render types for
   a subset of TCA types. See the :ref:`TCA types reference <t3tca:columns-types>` for the render types
   available per TCA type.

   .. code-block:: yaml

       fields:
         - identifier: my_identifier
           type: Textarea
           renderType: codeEditor

.. confval:: behaviour
   :name: field-types-behaviour
   :required: false
   :type: array

   Field behaviour options. The most commonly used one is
   :yaml:`allowLanguageSynchronization`, which lets editors choose between a
   custom value and the value of the default language record. See
   :ref:`allowLanguageSynchronization <t3tca:tca_property_behaviour_allowLanguageSynchronization>`.

   .. code-block:: yaml

       fields:
         - identifier: my_identifier
           type: Text
           behaviour:
             allowLanguageSynchronization: true

.. confval:: fieldInformation
   :name: field-types-fieldInformation
   :required: false
   :type: array

   Renders informational text directly above the field. See
   :ref:`TCA fieldInformation <t3tca:tca_property_fieldInformation>`.

.. confval:: fieldControl
   :name: field-types-fieldControl
   :required: false
   :type: array

   Adds buttons next to the field, for example a link or wizard popup. See
   :ref:`TCA fieldControl <t3tca:tca_property_fieldControl>`.

.. confval:: fieldWizard
   :name: field-types-fieldWizard
   :required: false
   :type: array

   Renders additional widgets below the field, for example
   :yaml:`defaultLanguageDifferences` or :yaml:`localizationStateSelector`. See
   :ref:`TCA fieldWizard <t3tca:tca_property_fieldWizard>`.

..  note::

    :yaml:`renderType`, :yaml:`behaviour`, :yaml:`fieldControl`,
    :yaml:`fieldInformation` and :yaml:`fieldWizard` are only accepted for the
    field types whose page lists them. Core TCA evaluates them per TCA type, so
    the JSON schema mirrors that restriction.

..  toctree::
    :maxdepth: 1
    :titlesonly:
    :glob:

    */Index
