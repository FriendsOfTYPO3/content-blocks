.. include:: /Includes.rst.txt
.. _crop-variants:

=============
Crop Variants
=============

Crop Variants are normally defined for each :ref:`File <field_type_file>` field
separately. If the same Crop Variants are used in many Content Blocks, they can
be defined once in a central place and referenced by their identifier.

.. note::

    Just like :ref:`Basics <basics>`, Crop Variant references are a simple
    "search & replace" kind of mechanic. Once resolved, they act like they
    were defined inline in the File field.

Define Crop Variants
====================

Crop Variants are defined by placing one or more YAML files into
**ContentBlocks/CropVariants**. The name of the YAML file can be chosen freely.
It is also possible to create sub-folders in order to structure your Crop
Variants. Each file defines exactly one Crop Variant.

.. code-block:: yaml
   :caption: EXT:your_extension/ContentBlocks/CropVariants/DesktopWide.yaml

    identifier: vendor/desktop-wide
    title: Desktop
    allowedAspectRatios:
      landscape:
        title: Landscape
        value: 16 / 9
      portrait:
        title: Portrait
        value: 3 / 4

Apart from the mandatory :yaml:`identifier`, the options are exactly the same as
for inline Crop Variants in the File field option
:confval:`cropVariants <file-cropVariants>`.

.. tip::

    It is not mandatory to provide a vendor name for your Crop Variant
    identifier. However, it is recommended to avoid using too generic names to
    avoid conflicts. Identifiers must be unique across all extensions.

Reference Crop Variants
=======================

Inside the :yaml:`cropVariants` option of a File field, use the identifier of a
central Crop Variant instead of the inline configuration. The key, here
:yaml:`desktop`, is still defined per field. References and inline Crop Variants
can be mixed freely.

.. code-block:: yaml
   :caption: EXT:your_extension/ContentBlocks/ContentElements/teaser/config.yaml

    name: example/teaser
    fields:
      - identifier: image
        type: File
        allowed: common-image-types
        cropVariants:
          desktop: vendor/desktop-wide
          mobile:
            title: Mobile
            allowedAspectRatios:
              square:
                title: Square
                value: 1

References are resolved on every nesting level: in Palettes, Collections,
FlexForm sheets, :ref:`Type Overrides <api_type_overrides>` and
:ref:`Basics <basics>`. Referencing an identifier which does not exist results
in an exception.
