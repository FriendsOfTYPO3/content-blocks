.. include:: /Includes.rst.txt
.. _field_types:

===========
Field types
===========

The Content Block field types mirror the available
:ref:`TCA types <t3tca:columns-types>`. Some types have been renamed to better
reflect the actual usage. For the most part options are identical. There are
some additional options, which are not available in TCA to ease the usage.

..  rst-class:: horizbuttons-attention-m

*  :ref:`Basic <field_type_basic>`
*  :ref:`Category <field_type_category>`
*  :ref:`Checkbox <field_type_checkbox>`
*  :ref:`Collection <field_type_collection>`
*  :ref:`Color <field_type_color>`
*  :ref:`DateTime <field_type_datetime>`
*  :ref:`Email <field_type_email>`
*  :ref:`File <field_type_file>`
*  :ref:`FlexForm <field_type_flexform>`
*  :ref:`Folder <field_type_folder>`
*  :ref:`Json <field_type_json>`
*  :ref:`Language <field_type_language>`
*  :ref:`Linebreak <field_type_linebreak>`
*  :ref:`Link <field_type_link>`
*  :ref:`Number <field_type_number>`
*  :ref:`Palette <field_type_palette>`
*  :ref:`Password <field_type_password>`
*  :ref:`Radio <field_type_radio>`
*  :ref:`Relation <field_type_relation>`
*  :ref:`Select <field_type_select>`
*  :ref:`Slug <field_type_slug>`
*  :ref:`Tab <field_type_tab>`
*  :ref:`Text <field_type_text>`
*  :ref:`Textarea <field_type_textarea>`
*  :ref:`Uuid <field_type_uuid>`

Common field options
====================

.. _yaml_reference_field_properties:

Field options, which can be defined inside the :yaml:`fields` array.

.. confval:: identifier

   :Required: true
   :Type: string

   The field's identifier has to be unique within a Content Block. Exception is
   within a collections' field array, as this starts a new scope.

   .. code-block:: yaml

       fields:
           identifier: my_identifier
           type: Text

.. confval:: type

   :Required: true
   :Type: string

   The field's type. See :ref:`field_types`.

   .. code-block:: yaml

       fields:
           identifier: my_identifier
           type: Text

.. confval:: label

   :Required: false
   :Type: string

   By default labels should be defined inside the :file:`Labels.xlf` file. But in
   case there is only one language for the backend you may define labels directly
   in the YAML configuration. Translation files have precedence over this.

   .. code-block:: yaml

       fields:
           identifier: my_identifier
           type: Text
           label: Static label

.. confval:: description

   :Required: false
   :Type: string

   The same as for `label` above.

   .. code-block:: yaml

       fields:
           identifier: my_identifier
           type: Text
           description: Static description

.. confval:: useExistingField

   :Required: false
   :Type: bool

   If set to true, the identifier is treated as an existing field from the Core
   or your own defined field in TCA. To learn more about reusing fields read
   :ref:`this article <cb_reuse_existing_fields>`.

   .. code-block:: yaml

       fields:
           identifier: bodytext
           useExistingField: true

.. confval:: prefixField

   :Required: false
   :Type: boolean
   :Default: true

   If set to false, the prefixing is disabled for this field. This overrules the
   global option :ref:`prefixFields <yaml_reference_prefixFields>`.

   .. code-block:: yaml

       fields:
           identifier: my_identifier
           type: Text
           prefixField: false

.. confval:: prefixType

   :Required: false
   :Type: string
   :Default: full

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

   :Required: false
   :Type: string|array
   :Default: ''

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

   :Required: false
   :Type: string
   :Default: ''

   Can be used to trigger a reload of the Content Type when this specific
   field is changed. Should be used, if a rule of :yaml:`displayCond` is used
   for this field.

   .. code-block:: yaml

      onChange: reload

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
    Json/Index
    Language/Index
    Linebreak/Index
    Link/Index
    Number/Index
    Palette/Index
    Password/Index
    Radio/Index
    Relation/Index
    Select/Index
    Slug/Index
    Tab/Index
    Text/Index
    Textarea/Index
    Uuid/Index
