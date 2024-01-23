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

   Root/Index
   ContentTypes/ContentElements/Index
   ContentTypes/PageTypes/Index
   ContentTypes/RecordTypes/Index
   FieldTypes/Index
   Basics/Index
