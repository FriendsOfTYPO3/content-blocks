.. include:: /Includes.rst.txt
.. _yaml_reference:

==================================
Editing interface (YAML reference)
==================================

The heart of a content block is the `EditorInterface.yaml` file. Here you can
find all possible configuration options. There are slight differences, whether
you are dealing with :ref:`Content Elements <yaml_reference_content_element>`,
:ref:`Page Types <yaml_reference_page_types>` or
:ref:`Record Types <yaml_reference_record_type>`. In general Content Elements
and Page Types are a special concept in TYPO3. The Core already defines the
table names, the type field, etc. You just have to define a new type. This is
done by providing the :yaml:`name` attribute, which will be converted to the
type name. Page Types require an integer value for the type. Therefore you need
to set it additionally with :yaml:`typeName`.

With TYPO3 you can also create custom Record Types. They require you to define
a custom :yaml:`table`, and a :yaml:`labelField` field. Per default all extra
features like workspaces, language support, frontend restrictions, etc. are
enabled. You can selectively disable each one of them, if you don't use them.

.. _content_types:

Content Types
=============

.. toctree::
    :titlesonly:

    ContentTypes/ContentElements/Index
    ContentTypes/PageTypes/Index
    ContentTypes/RecordTypes/Index

Table of contents
=================

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

   .. code-block:: yaml

       name: vendor/content-block-name

.. confval:: prefixFields

   :Required: false
   :Type: boolean
   :Default: true

   By default, all fields are prefixed with the name of the content block to
   prevent collisions. In order to better reuse fields between content blocks,
   it can be useful to deactivate this option. Read more about
   :ref:`reusing fields here <cb_reuse_existing_fields>`.

   .. code-block:: yaml

       prefixFields: false

.. confval:: priority

   :Required: false
   :Type: integer
   :Default: 0

   The priority can be used to prioritize certain content blocks in the loading
   order. The default loading order is alphabetically. Higher priorities will be
   loaded before lower ones. This affects e.g. the order in the "New Content
   Element Wizard".

   .. code-block:: yaml

       # this content block will be displayed before others
       priority: 10

.. confval:: typeName

   :Required: false (Page Type: true)
   :Type: string
   :Default: automatically generated from :yaml:`name`

   The identifier of the new Content Type. It is automatically generated from
   the name, if not defined manually. This is required for Page Types.

   .. code-block:: yaml

       # Page Types require a numerical type name
       typeName: 1337

       # Record Types can have a freely chosen type name
       typeName: type1

.. confval:: fields

   :Required: false
   :Type: array

   The main entry point for the field definitions. Fields defined in this array
   are displayed in the backend exactly in the same order. You can create new
   custom fields or reuse existing ones, which are defined via TCA. Learn
   :ref:`here <yaml_reference_field_properties>` what is needed to define a
   field.

   .. code-block:: yaml

       fields:
           - identifier: my_field
             type: Text

Common field properties
=======================
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

   By default labels should be defined inside the :file:`Labels.xml` file. But in
   case there is only one language for the backend you may define labels directly
   in the YAML configuration. This has precedence over translation files.

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

.. _field_types:

Field types
===========

.. toctree::
    :maxdepth: 1
    :titlesonly:

    FieldTypes/Basic/Index
    FieldTypes/Category/Index
    FieldTypes/Checkbox/Index
    FieldTypes/Collection/Index
    FieldTypes/Color/Index
    FieldTypes/DateTime/Index
    FieldTypes/Email/Index
    FieldTypes/File/Index
    FieldTypes/FlexForm/Index
    FieldTypes/Folder/Index
    FieldTypes/Linebreak/Index
    FieldTypes/Link/Index
    FieldTypes/Number/Index
    FieldTypes/Palette/Index
    FieldTypes/Radio/Index
    FieldTypes/Relation/Index
    FieldTypes/Select/Index
    FieldTypes/Tab/Index
    FieldTypes/Text/Index
    FieldTypes/Textarea/Index
