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
   numbers, which you can't use either: 1, 3, 4, 6, 7, 199, 254, 255.

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
