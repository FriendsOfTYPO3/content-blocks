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

Options
=======

Here you can find all :ref:`common root options <yaml_reference_common>`.

.. confval:: description

   :Required: false
   :Type: string

   This is the description of the Content Element. If you have a Labels.xlf
   file, you should define it there with the key :xml:`description`. If both are
   defined, the translation file has precedence.

   .. code-block:: yaml

       description: "Here comes my description"

.. confval:: group

   :Required: false
   :Type: string
   :Default: common

   By default, all new Content Elements are placed in the `common` tab of the
   "New Content Element Wizard". You can choose another tab, if you want to
   group your elements.

   .. code-block:: yaml

       group: special

   The Core defines these standard tabs, which are always available:

   *  `common`
   *  `menu`
   *  `special`
   *  `forms`
   *  `plugins`

.. confval:: typeName

   :Required: false
   :Type: string
   :Default: automatically generated from :yaml:`name`

   The identifier of the new Content Element. It is automatically generated from
   the name, if not defined manually.

   .. code-block:: yaml

       typeName: my_content_element

.. confval:: saveAndClose

   :Required: false
   :Type: bool
   :Default: false

   Can be activated in order to skip the edit view when adding the Content
   Element via the NewContentElementWizard. This can be useful if you have a
   Content Element or Plugin without configuration.

   .. code-block:: yaml

       saveAndClose: true
