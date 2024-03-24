.. include:: /Includes.rst.txt
.. _field_type_linebreak:

=========
Linebreak
=========

The :yaml:`Linebreak` field is used inside `Palette` fields to add a manual
linebreak. Otherwise all fields within a Palette are displayed next to each
other. Note: Contrary to all other field types, Linebreaks don't need an
:yaml:`identifier`.

Settings
========

.. confval:: ignoreIfNotInPalette
   :name: ignoreIfNotInPalette

   :Required: false
   :Type: boolean
   :Default: false

   Normally, linebreaks can only be defined inside of a palette. With this flag
   set to true, linebreaks can also appear outside of palettes (but do nothing).
   This is especially useful in combination with
   :ref:`Basics <field_type_basic>` when you want a set of fields to be used
   both in palettes and on root level.

Examples
========

Minimal
-------

.. code-block:: yaml

    name: example/linebreak
    fields:
      - identifier: palette_1
        type: Palette
        fields:
          - identifier: number
            type: Number
          - type: Linebreak
          - identifier: text
            type: Text


For in-depth information about palettes and linebreaks refer to the :ref:`TCA documentation <t3tca:palettes>`.
