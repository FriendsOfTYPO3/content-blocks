.. include:: /Includes.rst.txt
.. _field_type_file:

====
File
====

:php:`type => 'file' // TCA`

The :yaml:`File` type generates a field for file relations.

Settings
========

.. confval:: extendedPalette

   :Required: false
   :Type: boolean
   :Default: true

   If enabled, an additional image or media palette will be rendered. For
   image files it consists of the additional fields `crop`, `alternative` and
   `link`. For audio and media files an additional `autoplay` field is added.
   For other file types, like plain text, this option has no effect. Disable
   this option, if you don't need these additional fields.

.. confval:: allowed

   :Required: false
   :Type: string|array
   :Default: ''

    Possible values: `common-image-types`, `common-media-types` or your custom
    list of file types.

.. confval:: maxitems

   :Required: false
   :Type: integer
   :Default: 99999

   Maximum number of child items. Defaults to a high value. JavaScript record
   validation prevents the record from being saved if the limit is not satisfied.

.. confval:: minitems

   :Required: false
   :Type: integer
   :Default: 0

   Minimum number of child items. Defaults to 0. JavaScript record validation
   prevents the record from being saved if the limit is not satisfied.
   The field can be set as required by setting `minitems` to at least 1.

.. confval:: cropVariants

   :Required: false
   :Type: array
   :Default: []

   It is possible to define crop variants for this specific field and Content
   Block. This documentation only covers the most basic configuration. Refer to
   the :ref:`TCA documentation <columns-imageManipulation-properties-cropVariants>`
   for a complete overview of possibilities.

   Example configuration below. The aspect ratios can be defined as a float
   value or a fraction. Only the simple division operation `a / b` is allowed.

   .. code-block:: yaml

    cropVariants:
      teaser:
        title: Teaser
        allowedAspectRatios:
          portrait:
            title: Portrait
            value: 0.75
          landscape:
            title: Landscape
            value: 4 / 3

For more advanced configuration refer to the :ref:`TCA documentation <t3tca:columns-file>`.

Example
=======

Minimal
-------

All file types allowed, no restrictions.

.. code-block:: yaml

    name: example/file
    fields:
      - identifier: my_file_field
        type: File

Advanced / use case
-------------------

Allow only image types, disable extended palette (no cropping field), require
at least one image and set limit to 10 images.

.. code-block:: yaml

    name: example/image
    fields:
      - identifier: image
        type: File
        extendedPalette: false
        minitems: 1
        maxitems: 10
        allowed: common-image-types

Allow media types like audio, video and youtube (or vimeo).

.. code-block:: yaml

    name: example/media
    fields:
      - identifier: media
        type: File
        allowed: common-media-types
