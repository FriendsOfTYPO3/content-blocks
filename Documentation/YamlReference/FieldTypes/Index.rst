.. include:: /Includes.rst.txt
.. _field_types:

===========
Field Types
===========

The Content Block field types mirror the available
:ref:`TCA types <t3tca:columns-types>`. Some types have been renamed to better
reflect the actual usage. For the most part options are identical. There are
some additional options, which are not available in TCA to ease the usage.

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
           identifier: my_identifier
           type: Text

.. confval:: type
   :name: field-types-type
   :required: true
   :type: string

   The field's type. See :ref:`field_types`.

   .. code-block:: yaml

       fields:
           identifier: my_identifier
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
           identifier: my_identifier
           type: Text
           label: Static label

.. confval:: description
   :name: field-types-description
   :required: false
   :type: string

   The same as for `label` above.

   .. code-block:: yaml

       fields:
           identifier: my_identifier
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
           identifier: bodytext
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
           identifier: my_identifier
           type: Text
           prefixField: false

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
           identifier: my_identifier
           type: Text
           prefixField: true
           prefixType: vendor

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

..  toctree::
    :maxdepth: 1
    :titlesonly:
    :glob:

    */Index
