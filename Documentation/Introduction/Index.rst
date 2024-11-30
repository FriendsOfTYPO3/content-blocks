.. include:: /Includes.rst.txt
.. _introduction:

============
Introduction
============

A **Content Block** is a simplified, **component-based** approach of defining
Content Types in TYPO3. This includes Content Elements, Page Types and generic
Record Types. A YAML file serves as a central definition. Content Blocks acts
hereby as a compiler for more complex low-level code. It adheres to best
practices by default, significantly reducing boilerplate code.

.. note::

   Content Blocks started out as a layer on top of TYPO3 and still is. The
   long-term goal is to integrate the concept of a Content Block into the Core
   as a first-class citizen. As of now, some knowledge of the underlying Core
   API is required to fully grasp the possibilities of this extension and how
   to customize it.

For more technical, historical and conceptual insights about Content Blocks we
recommend these further readings:

*  :ref:`About Content Elements <about_content_elements>`
*  :ref:`Defining Content Types the Core way <core-content-types>`
*  :ref:`History <cb_history>`

Definition
==========

The goal is to **encapsulate** all resources belonging to the Content Block
inside one **component**. This leads to re-usable components, which can be
easily copy-pasted into other projects.

..  card::
    :class: mb-4

    ..  directory-tree::
        :level: 3

            *   :path:`my-content-block`

                *   :path:`assets`

                    *   :file:`icon.svg`

                *   :path:`language`

                    *   :file:`labels.xlf`

                *   :path:`templates`

                    *   :file:`backend-preview.html`
                    *   :file:`frontend.html`

                *   :file:`config.yaml`

*  Learn more about the :ref:`Content Block definition <cb_definition>`

config.yaml
===========

This file is the **basis** for the definition. It defines **exactly** one
Content Type. Using YAML over PHP includes a wider range of people, which is
able to modify Content Blocks without the need of a developer.

.. code-block:: yaml
   :caption: EXT:some_extension/ContentBlocks/ContentElements/content-block-name/config.yaml

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

..  card::
    :class: mb-4

    ..  directory-tree::
        :level: 4

            *   :path:`my_extension`

                *   :path:`Classes`

                *   :path:`Configuration`

                *   :path:`ContentBlocks`

                    *   :path:`ContentElements`

                        *   :path:`content-block-1`
                        *   :path:`content-block-2`

                    *   :path:`PageTypes`

                        *   :path:`content-block-3`
                        *   :path:`content-block-4`

                    *   :path:`RecordTypes`

                        *   :path:`content-block-5`
                        *   :path:`content-block-6`

                *   :file:`ext_emconf.php`
                *   :file:`composer.json`

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
