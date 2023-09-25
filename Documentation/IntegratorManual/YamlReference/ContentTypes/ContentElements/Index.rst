.. include:: /Includes.rst.txt
.. _yaml_reference_content_element:

================
Content Elements
================

Folder: `ContentBlocks/ContentElements`.

Content Elements are a special Content Type in TYPO3. The basic structure is
already defined in the TYPO3 Core. Content Blocks only adds new types to it. The
:yaml:`typeName` for `CType` will be generated automatically from the
:yaml:`name`. Usually you don't need to know how it is called internally. If you
do need to know the name, you can inspect it e.g. in the Page TsConfig module.

A minimal Content Element looks like this:

.. code-block:: yaml
   :caption: EXT:your_extension/ContentBlocks/ContentElements/cta/EditorInterface.yaml

    name: example/cta
    fields:
      - identifier: header
        useExistingField: true

In case you need the well-known `Appearance` tab back, you can add pre-defined
Basics to your definition:

.. code-block:: yaml
   :caption: EXT:your_extension/ContentBlocks/ContentElements/cta/EditorInterface.yaml

    name: example/cta
    basics:
        - TYPO3/Appearance
        - TYPO3/Links
    fields:
      - identifier: header
        useExistingField: true

The Appearance tab will then be added after all your custom fields.
