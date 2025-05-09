.. include:: /Includes.rst.txt
.. _yaml_reference_file_types:

=========
FileTypes
=========

.. versionadded:: 1.2

Folder: `ContentBlocks/FileTypes`

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

Learn more about :ref:`File Types <api_file_types>`.

Options
=======

..  note::

    There are no common root options for this type.

..  confval-menu::
    :name: confval-file-types-options
    :display: table
    :type:
    :default:
    :required:

.. confval:: typeName
   :name: file-type-typeName
   :required: true
   :type: string

   The :yaml:`typeName` has to be one of these keywords:

   * text
   * image
   * audio
   * video
   * application

   .. code-block:: yaml

       typeName: image
