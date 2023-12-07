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

The goal is to **encapsulate** all resources belonging to the Content Block
inside one **component**. This leads to re-usable components, which can be
easily copy-pasted into other projects.

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

This file is the **basis** for the definition. It defines **exactly** one
Content Type. Using YAML over PHP includes a wider range of people, which is
able to modify Content Blocks without the need of a developer.

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

The registration works by simply placing a Content Block into a dedicated
folder. For this purpose an already loaded extension is required as a host.
Depending on the Content Type the Content Block is put into a predestinated
sub-folder.

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

Terminology
===========

**Content Blocks** is the name of the extension and generates the code for the Core API.

A single **Content Block** is a small chunk of information, which defines exactly one Content Type.

A **Content Type** is an entity in TYPO3, which defines a set of fields and their behavior.

A **Content Element** is a special Content Type, which has a frontend rendering definition.

A **Page Type** is a special Content Type, which defines the behavior of a web page.

A **Record Type** is a generic Content Type.

..  uml::

    object ContentBlock
    object ContentType
    object ContentElement
    object PageType
    object RecordType

    ContentBlock <|-- ContentType
    ContentType <|-- ContentElement
    ContentType <|-- PageType
    ContentType <|-- RecordType
