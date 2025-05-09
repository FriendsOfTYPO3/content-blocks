.. include:: /Includes.rst.txt
.. _changelog-1.2:

===
1.2
===

..  contents::

Features
========

File Types
----------

Content Blocks now supports the re-definition of File Types (sys_file_reference).
This new Content Type can be defined inside the `ContentBlocks/FileTypes` folder:

.. code-block:: yaml
   :caption: EXT:your_extension/ContentBlocks/FileTypes/image/config.yaml

    name: example/file-type-image
    typeName: image
    prefixFields: false
    fields:
      - identifier: image_overlay_palette
        type: Palette
        label: 'LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette'
        fields:
          - identifier: alternative
            useExistingField: true
          - identifier: description
            useExistingField: true
          - type: Linebreak
          - identifier: link
            useExistingField: true
          - identifier: title
            useExistingField: true
          - type: Linebreak
          - identifier: example_custom_field
            type: Text
            label: 'My custom Field'
          - type: Linebreak
          - identifier: crop
            useExistingField: true

By re-defining the :yaml:`image` type like in this example, you can add your
custom fields and position them in your desired spot.

Further read:

*  :ref:`File Types (API) <api-file-types>`
*  :ref:`FileTypes (YAML reference) <yaml-reference-file-types>`

Type Overrides
--------------

Content Blocks now features a way to override types in the context of a
Collection with the new field option :yaml:`overrideType`. This does also
work for the :yaml:`File`.

.. code-block:: yaml

    name: friendsoftypo3/example
    table: tx_friendsoftypo3_example
    prefixFields: false
    labelField: title
    fields:
      -
        identifier: title
        type: Text
        label: Title
      - identifier: collection_override
        type: Collection
        foreign_table: tx_hov_domain_model_record1
        overrideType:
          record1:
            - identifier: type
              type: Select
              useExistingField: true
            - identifier: title
              type: Text
              useExistingField: true
            - identifier: custom_field
              type: Text

Further read:

*  :ref:`Type Overrides (API) <api-type-overrides>`
