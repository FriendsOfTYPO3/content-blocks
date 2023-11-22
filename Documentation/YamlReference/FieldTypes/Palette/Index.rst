.. include:: /Includes.rst.txt
.. _field_type_palette:

=======
Palette
=======

The :yaml:`Palette` field is used to group other fields. Grouped fields will be
displayed next to each other rather than below each other.

Labels
======

XLF translation keys for Palettes have the following convention:

.. code-block:: xml

    <body>
        <trans-unit id="palettes.PALETTE_IDENTIFIER.label">
            <source>Label for Palette</source>
        </trans-unit>
        <trans-unit id="palettes.PALETTE_IDENTIFIER.description">
            <source>Description for Palette</source>
        </trans-unit>
        <trans-unit id="COLLECTION_IDENTIFIER.palettes.PALETTE_IDENTIFIER.label">
            <source>Label for Palette in Collection</source>
        </trans-unit>
        <trans-unit id="COLLECTION_IDENTIFIER1.COLLECTION_IDENTIFIER2.palettes.PALETTE_IDENTIFIER.label">
            <source>Label for Palette in nested Collection</source>
        </trans-unit>
    </body>

Examples
========

Minimal
-------

.. code-block:: yaml

    name: example/palette
    fields:
      - identifier: palette_1
        type: Palette
        label: Palette 1
        description: My palette description
        fields:
          - identifier: number
            type: Number
          - identifier: text
            type: Text


For in-depth information about palettes refer to the :ref:`TCA documentation <t3tca:palettes>`.
