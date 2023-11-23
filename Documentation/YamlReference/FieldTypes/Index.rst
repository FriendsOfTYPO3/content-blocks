.. include:: /Includes.rst.txt
.. _field_types:

===========
Field types
===========

The Content Block field types mirror the available
:ref:`TCA types <t3tca:columns-types>`. Some types have been renamed to better
reflect the actual usage. For the most part options are identical. There are
some additional options, which are not available in TCA to ease the usage.

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
    Password/Index
    Radio/Index
    Relation/Index
    Select/Index
    Slug/Index
    Tab/Index
    Text/Index
    Textarea/Index

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

.. confval:: alternativeSql

   :Required: false
   :Type: string (SQL)

   It is possible to override the default SQL definition of a field with this
   option. Not every field type can be overridden. Have a look at the standard
   SQL definition of the corresponding field.

   .. code-block:: yaml

       fields:
           identifier: my_identifier
           type: Text
           alternativeSql: tinyint(2) DEFAULT "0" NOT NULL

.. confval:: prefixField

   :Required: false
   :Type: boolean
   :Default: true

   If set to false, the prefixing is disabled for this field. This overrules the
   global option :yaml:`prefixFields`.

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
