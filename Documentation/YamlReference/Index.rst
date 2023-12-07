.. include:: /Includes.rst.txt
.. _yaml_reference:

==============
YAML reference
==============

The heart of a Content Block is the `EditorInterface.yaml` file. Here you can
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
   Basics/Index

.. _yaml_reference_common:

Common root options
===================

.. confval:: name

   :Required: true
   :Type: string

   Every editing interface configuration must contain exactly one name. The name
   is made up of vendor and content block name separated by a `/` just like the
   `vendor/package` notation in a traditional composer.json file. It must be
   unique and must have at least 3 characters. Both parts, the vendor and
   content block name, have to be lowercase and be separated by a dash `-`.

   .. code-block:: yaml

       name: my-vendor/my-content-block-name

.. confval:: title

   :Required: false
   :Type: string

   This is the title of the Content Block. If you have a Labels.xlf file, you
   should define it there with the key :xml:`title`. If both are defined, the
   translation file has precedence. If nothing is defined, the title falls back
   to :yaml:`name`.

   .. code-block:: yaml

       title: "My super duper Content Block"

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

.. confval:: prefixType

   :Required: false
   :Type: string
   :Default: full

   Determines how to prefix the field if :yaml:`prefixFields` is enabled. Can
   be either :yaml:`full` (default) or :yaml:`vendor`.

   .. code-block:: yaml

       prefixFields: true
       prefixType: vendor

.. confval:: priority

   :Required: false
   :Type: integer
   :Default: 0

   The priority can be used to prioritize certain Content Blocks in the loading
   order. Higher priorities will be loaded before lower ones. This affects e.g.
   the order in the "New Content Element Wizard".

   .. code-block:: yaml

       # This Content Block will be displayed before others without a priority set.
       priority: 10

   .. note::

      The **default** loading order is **undefined** and depends on the
      (file-)system and the order, in which extensions are loaded.

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
