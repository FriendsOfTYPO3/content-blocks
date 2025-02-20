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

    name: example/image
    typeName: image
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
