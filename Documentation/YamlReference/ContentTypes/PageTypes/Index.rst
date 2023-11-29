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
   :caption: EXT:your_extension/ContentBlocks/PageType/blog/EditorInterface.yaml

    name: example/blog
    typeName: 1701284006
    fields:
      - identifier: additional_field
        type: Text

This will create a new Page Type entry above the page tree, which you can drag
and drop as usual. Your custom fields will be added after the `nav_title` field.

Options
=======

Here you can find all :ref:`common root options <yaml_reference_common>`.

.. confval:: typeName

   :Required: true
   :Type: integer

    The :yaml:`typeName` has to be a numerical value. There are
    some reserved numbers, which you can't use either: 1, 3, 4, 6, 7, 199, 254, 255.

   .. code-block:: yaml

       typeName: 1701284021

   .. tip::

      We recommend to use the **current unix timestamp** fo your type name. This
      is almost guaranteed unique. The kickstart command will default to the
      current timestamp as well.
