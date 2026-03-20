.. include:: /Includes.rst.txt
.. _yaml_reference_page_types:

=========
PageTypes
=========

Folder: `ContentBlocks/PageTypes`

.. code-block:: yaml
   :caption: EXT:your_extension/ContentBlocks/PageTypes/blog/config.yaml

    name: example/blog
    typeName: 1701284006
    fields:
      - identifier: additional_field
        type: Text

Learn more about :ref:`Page Types <cb_guides_page_types>`.

Options
=======

Here you can find all :ref:`common root options <yaml_reference_common>`.

..  confval-menu::
    :name: confval-page-types-options
    :display: table
    :type:
    :default:
    :required:

.. confval:: typeName
   :name: page-type-typeName
   :required: true
   :type: integer

   The :yaml:`typeName` has to be a numerical value. There are some reserved
   numbers, which you can't use either: 199, 254.

   .. code-block:: yaml

       typeName: 1701284021

   .. tip::

      We recommend to use the **current unix timestamp** fo your type name. This
      is almost guaranteed unique. The kickstart command will default to the
      current timestamp as well.

.. confval:: group
   :name: page-type-group
   :required: false
   :type: string
   :default: default

   The group is used for the grouping of the record type selector in the edit
   view of records. In addition, it is used for the "Create multiple pages" view
   for selecting the type. By default, all new types are placed in the `default`
   group.

   .. code-block:: yaml

       group: special

   The Core defines these groups for Page Types:

   *  `default`
   *  `link`
   *  `special`

.. confval:: allowedRecordTypes
   :name: page-type-allowedRecordTypes
   :required: false
   :type: array<string>

   List of allowed Record Types (tables) for this specific Page Type. If defined,
   only this list of records can be created on this Page Type. This is also
   evaluated when switching a Page Type to another one. Per default the tables
   `pages`, `sys_category`, `sys_file_reference` and `sys_file_collection` are
   allowed, if not configured otherwise.

   .. hint::

      If a Record Type ignores Page Type restrictions, then you don't need to
      list it here. It will be allowed regardless of this setting.

      .. code-block:: yaml

       security:
           ignorePageTypeRestriction: true

   Example: Extending the default allowed values with custom ones.

   .. code-block:: yaml

       allowedRecordTypes:
         - pages
         - sys_category
         - sys_file_reference
         - my_custom_table

   Example: Allow all records with an asterisk:

   .. code-block:: yaml

       allowedRecordTypes:
         - *

.. confval:: labelField
   :name: page-type-labelField
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
   :name: page-type-fallbackLabelFields
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
