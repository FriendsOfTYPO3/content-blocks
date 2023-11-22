.. include:: /Includes.rst.txt
.. _field_type_linebreak:

=========
Linebreak
=========

The :yaml:`Linebreak` field is used inside `Palette` fields to add a manual
linebreak. Otherwise all fields within a Palette are displayed next to each
other. Note: Contrary to all other field types, Linebreaks don't need an
:yaml:`identifier`.

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
