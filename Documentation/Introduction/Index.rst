.. include:: /Includes.rst.txt
.. _introduction:

============
Introduction
============

   Defining "Content Elements" in TYPO3 is hard and the learning curve is steep.

A **Content Block** is a simplified, **component-based** approach of defining
**Content Types** in TYPO3. This includes **Content Elements**, **Page Types**
and generic **Record Types**. A **YAML** file serves as a basis for **field**
definitions. Content Blocks acts hereby as a **generator** for more complicated
low-level code and makes **smart assumptions** based on **best practice**. This
significantly reduces **redundancies** and **boilerplate** code and enables
users to concentrate on their actual work rather than fiddling with the almighty
Core API.

.. note::

   Content Blocks is not a replacement for the current system. It is a good
   basis to get started. However, for more advanced use-cases knowledge about
   the underlying API is required.

For more technical, historical and conceptual insights about Content Blocks we
recommend these further readings:

*  :ref:`Defining Content Types the Core way <core-content-types>`
*  :ref:`History <cb_history>`

Definition
==========

The minimal viable definition consists of a folder with a YAML file named
**EditorInterface.yaml** inside. Everything else is optional. However, it is
recommended to provide at least a custom icon inside the **Assets** folder.
For Content Elements specifically you can define a frontend and backend template
in the **Source** folder.

.. code-block:: none
   :caption: Directory structure of a Content Block

   ├── Assets
   │   └── Icon.svg
   ├── Source
   │   ├── Language
   │   │   └── Labels.xlf
   │   ├── EditorPreview.html
   │   └── Frontend.html
   └── EditorInterface.yaml

*  Learn more about the :ref:`Content Block definition <cb_definition>`

EditorInterface.yaml
====================

This mandatory file is the basis for the definition. It defines exactly one
Content Type with its fields and the representation in the editor interface. At
the very least, the :yaml:`name` must be defined, which is the unique identifier
for this Content Block. Fields are defined one after another in the
:yaml:`fields` array. This is also the order, in which they will be displayed.

.. code-block:: yaml
   :caption: EXT:some_extension/ContentBlocks/ContentElements/content-block-name

    name: vendor/content-block-name
    fields:
      - identifier: my_text_field
        type: Text

*  Refer to the :ref:`YAML reference <yaml_reference>` for a complete overview.
*  Learn more about :ref:`reusing fields <cb_reuse_existing_fields>`
*  Learn how to :ref:`extend TCA <cb_extendTca>` of Content Blocks (for advanced users).

Registration
============

To create a new Content Block, a folder **ContentBlocks** has to be created
on the root level inside an existing and loaded extension. Then, depending on
the Content Type you want to create, you either create a **ContentElements**,
**PageTypes** or **RecordTypes** folder, in which you finally put your Content
Block inside. To quickly kickstart a new Content Block, the command
:bash:`make:content-block` can be used.

.. code-block:: none
   :caption: Content Blocks reside in the `ContentBlocks` folder of an extension
   :emphasize-lines: 3

   ├── Classes
   ├── Configuration
   ├── ContentBlocks
   │   ├── ContentElements
   │   │   ├── block1
   │   │   └── block2
   │   ├── PageTypes
   │   │   ├── block3
   │   │   └── block4
   │   └── RecordTypes
   │       ├── block5
   │       └── block6
   ├── Resources
   └── composer.json

*  Kickstart a Content Block with the :ref:`make:content-block command <cb_skeleton>`
*  Learn more about the :ref:`registration process <cb_installation>`

Fluid templating
================

The `EditorPreview.html` is the Fluid template for the backend preview and the
`Frontend.html` template for the frontend. Both contain the above defined fields
inside the variable :html:`data` and can be directly accessed.

.. code-block:: html

    <cb:asset.css identifier="content-block-foo" file="Frontend.css"/>
    <cb:asset.script identifier="content-block-foo" file="Frontend.js"/>
    <cb:translate key="my-key"/>

    My header: {data.header}
    My textfield: {data.my_text_field}

Content Blocks provides its own asset ViewHelpers :html:`<cb:asset.css>` and
:html:`<cb:asset.script>`. Required arguments are :html:`identifier`,
and :html:`file` (relative to the "Assets" folder inside the Content Block).
Be aware: the Core asset ViewHelpers won't work for Content Blocks in composer
mode.

For frontend translations Content Blocks also provides its own translation
ViewHelper. This can be seen as a simplified :html:`f:translate` ViewHelper.
The only required argument is :html:`key`. The ViewHelper will automatically
resolve the path to the `Labels.xlf` file of the current Content Block.

*  Here is the main article for :ref:`templating with Content Blocks <cb_templating>`
*  Learn how to :ref:`share Partials <cb_extension_partials>` between Content Blocks.
