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
a custom :yaml:`table` and a :yaml:`labelField` field. Per default all extra
features like workspaces, language support, frontend restrictions, etc. are
enabled. You can selectively disable each one of them, if you don't use them.

Full examples can be found in the examples repository: https://github.com/TYPO3-Initiatives/content-blocks-examples

**Table of Contents**

.. toctree::
   :titlesonly:
   :maxdepth: 1

   ContentTypes/ContentElements/Index
   ContentTypes/PageTypes/Index
   ContentTypes/RecordTypes/Index
   FieldTypes/Index

Common root options
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
