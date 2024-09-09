.. include:: /Includes.rst.txt
.. _basics:

===============
Basics (Mixins)
===============

**Basics**, also known as partials or mixins, are used to have a pre-defined set
of fields that can be used to better organize your EditorInterface.yaml file and
to reduce redundancy by sharing them between multiple Content Blocks.

.. note::

    It is important to understand that Basics are a very simple "search & replace"
    kind of mechanic. Once included in your Content Block they act like they were
    defined there directly. This also means that it is normally required to
    re-define the label in every Content Block. Hence it is recommended to reference
    labels with the full **LLL:EXT** path.

.. warning::

    Be cautious when using Basics in multiple Content Blocks with
    :ref:`prefixing <cb_reuse_existing_cb_fields>` enabled. This may lead to the
    database schema creating one new column for each included field. It is
    recommended to either use fixed prefixes with :yaml:`prefixType: vendor` or
    to disable the prefix for each field in the Basic with
    :yaml:`prefixField: false`.

There are **two** different ways of using Basics.

Basics as additional fields
===========================

The first way of using Basics is to have them added **after** the :yaml:`fields`
array of your Content Block. This is useful if you want to have a set of fields
that are always available for your Content Block.

This is an example on how to add the classic Fluid Styled Content **Appearance**
Tab and the additional **Links** palette.

.. code-block:: yaml
   :caption: EXT:your_extension/ContentBlocks/ContentElements/basics/EditorInterface.yaml

    name: example/basics
    basics:
        - TYPO3/Appearance
        - TYPO3/Links

You can add as many Basics as you need. Note, that all Basics are simply
concatenated onto each other. Be careful, not to create an invalid state by
gluing incompatible Basics together.

Basics as field type
====================

The second way is to use Basics directly in the :yaml:`fields` array. This can
be done by using the according Basic :yaml:`identifier` and the type
:yaml:`Basic`.

.. code-block:: yaml
   :caption: EXT:your_extension/ContentBlocks/ContentElements/basics/EditorInterface.yaml

    name: example/basics
    fields:
        - identifier: TYPO3/Header
          type: Basic

.. note::

   It's not possible to override Basics once they are included as a field type,
   because they can contain multiple fields, palettes or tabs. If a use case
   arises, where you need a new "base field", then you probably want to create
   a new :ref:`custom field type <extending-field-types>`. This requires PHP
   code, however.

Pre-defined Basics
==================

List of the standard Basics shipped with Content Blocks. Feel free to copy them
into your own project and adjust them as you need.

*  :yaml:`TYPO3/Header`
*  :yaml:`TYPO3/Appearance`
*  :yaml:`TYPO3/Links`
*  :yaml:`TYPO3/Categories`

Define own Basics
=================

You can define your own Basics by placing one or more YAML files into
**ContentBlocks/Basics**. The name of the YAML file can be chosen freely. It is
also possible to create sub-folders in order to structure your Basics.

Example on how to create a single Basic:

.. code-block:: yaml
   :caption: EXT:your_extension/ContentBlocks/Basics/YourBasic.yaml

    identifier: Vendor/YourBasic
    fields:
      - identifier: a_basic_field
        type: Text
        label: LLL:EXT:sitepackage/Resources/Private/Language/locallang.xlf:a_basic_field

The :yaml:`fields` part is exactly the same as in the EditorInterface.yaml. Here
you can define a Tab, a Palette or simply a set of fields.

The most practical way to use Basics is to use pre-defined tabs as the global
:yaml:`basics` option, so they are always added at the end. The field type
:yaml:`Basic` is used best as a palette. There you can define a set of fields,
which you always need e.g. various header fields.

.. tip::

    Unlike Content Block names, it is not mandatory to provide a vendor name for
    your Basic identifier. However, it is recommended to avoid using too generic
    names to avoid conflicts.


Nested Basics
=============

It is also possible to nest Basics. So if Basic A refers to Basic B in the
fields list, then this will be resolved, too. Be careful to not create an
infinite loop by circular or self-references. This will be detected
automatically, if a high nesting level is reached.

.. code-block:: yaml
   :caption: EXT:your_extension/ContentBlocks/Basics/YourBasic.yaml

    identifier: Vendor/YourBasic
    fields:
      - identifier: a_basic_field
        type: Text
        label: LLL:EXT:sitepackage/Resources/Private/Language/locallang.xlf:a_basic_field
      - identifier: Vendor/AnotherBasic
        type: Basic
