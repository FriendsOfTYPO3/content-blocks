.. include:: /Includes.rst.txt
.. _field_type_palette:

=======
Palette
=======

The `Palette` field is used to group other fields. Grouped fields will be
displayed next to each other rather than below each other.

Labels.xlf
==========

The naming convention is `palettes.<identifier>`

If inside Collections, each Collection needs to be prepended:

`<collection1>.<collection2>.palettes.<identifier>`

Examples
========

Minimal
-------

.. code-block:: yaml

    name: example/palette
    group: common
    fields:
      - identifier: palette_1
        type: Palette
        fields:
          - identifier: number
            type: Number
          - identifier: text
            type: Text


For in-depth information about palettes refer to the :ref:`TCA documentation <t3tca:palettes>`.
