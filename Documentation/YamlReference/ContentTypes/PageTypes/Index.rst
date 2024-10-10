.. include:: /Includes.rst.txt
.. _yaml_reference_page_types:

==========
Page Types
==========

Folder: `ContentBlocks/PageTypes`.

Page Types are a special Content Type in TYPO3. The basic structure is
already defined in the TYPO3 Core. Content Blocks only adds new types to it. A
minimal Page Type looks like this:

.. code-block:: yaml
   :caption: EXT:your_extension/ContentBlocks/PageTypes/blog/config.yaml

    name: example/blog
    typeName: 1701284006
    fields:
      - identifier: additional_field
        type: Text

This will create a new Page Type entry above the page tree, which you can drag
and drop as usual. Your custom fields will be added after the `nav_title` field.
SEO fields will be automatically added, if you have the SEO system extension
installed.

.. tip::

    Check out this :ref:`comprehensive guide <cb_guides_page_types>` on ways to
    utilize Page Types.

Options
=======

Here you can find all :ref:`common root options <yaml_reference_common>`.

.. confval:: typeName
   :name: page-type-typeName

   :Required: true
   :Type: integer

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

   :Required: false
   :Type: string
   :Default: default

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
