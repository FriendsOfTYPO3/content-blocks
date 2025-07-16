.. include:: /Includes.rst.txt
.. _field_type_file:

====
File
====

The :yaml:`File` type generates a field for file relations.

Settings
========

..  confval-menu::
    :name: confval-file-options
    :display: table
    :type:
    :default:
    :required:

.. confval:: extendedPalette
   :name: file-extendedPalette
   :required: false
   :type: boolean
   :default: true

   If enabled, an additional image or media palette will be rendered. For
   image files it consists of the additional fields `crop`, `alternative` and
   `link`. For audio and media files an additional `autoplay` field is added.
   For other file types, like plain text, this option has no effect. Disable
   this option, if you don't need these additional fields.

.. confval:: allowed
   :name: file-allowed
   :required: false
   :type: string|array
   :default: ''

    Possible values: `common-image-types`, `common-media-types` or your custom
    list of file types.

.. confval:: maxitems
   :name: file-maxitems
   :required: false
   :type: integer
   :default: 99999

   Maximum number of child items. Defaults to a high value. JavaScript record
   validation prevents the record from being saved if the limit is not satisfied.

.. confval:: minitems
   :name: file-minitems
   :required: false
   :type: integer
   :default: "0"

   Minimum number of child items. Defaults to 0. JavaScript record validation
   prevents the record from being saved if the limit is not satisfied.
   The field can be set as required by setting `minitems` to at least 1.

.. confval:: relationship
   :name: file-relationship
   :required: false
   :type: string
   :default: oneToMany

   The relationship defines the cardinality between the relations. Possible
   values are :yaml:`oneToMany` (default), :yaml:`manyToOne` and
   :yaml:`oneToOne`. In case of a [x]toOne relation, the processed field will
   be filled directly with the file reference instead of a collection of file
   references. In addition, :yaml:`maxitems` will be automatically set to
   :yaml:`1`.

.. confval:: cropVariants
   :name: file-cropVariants
   :required: false
   :type: array
   :default: []

   It is possible to define crop variants for this specific field and Content
   Block. This documentation only covers the most basic configuration. Refer to
   the :ref:`TCA documentation <t3tca:columns-imageManipulation-properties-cropVariants>`
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

   Use the new crop variant in your frontend template:

   .. code-block:: html

    <f:for each="{data.image}" as="image">
        <f:image image="{image}" cropVariant="teaser" width="800" />
    </f:for>

.. confval:: overrideType
   :name: file-overrideType
   :required: false
   :type: array
   :default: []

   Type Overrides can be used to override the File Definition in the context of
   as single field. Refer to the :ref:`API documentation <api_type_overrides>`
   if you want to learn more.

   .. code-block:: yaml

      overrideType:
        image:
          - identifier: image_overlay_palette
            type: Palette
            label: 'LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette'
            fields:
              - identifier: alternative
                useExistingField: true
              - identifier: description
                useExistingField: true
              - type: Linebreak
              - identifier: link
                useExistingField: true
              - identifier: title
                useExistingField: true
              - type: Linebreak
              - identifier: example_custom_field
                type: Text
                label: 'My custom Field'
              - type: Linebreak
              - identifier: crop
                useExistingField: true

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

Set specific crop variants for an image field.

.. code-block:: yaml

    name: example/image
    fields:
      - identifier: image
        type: File
        allowed: common-image-types
        cropVariants:
          desktop:
            title: Desktop
            allowedAspectRatios:
              portrait:
                title: Portrait
                value: 0.75
              landscape:
                title: Landscape
                value: 4 / 3
            focusArea:
              x: 0.3
              y: 0.3
              width: 0.4
              height: 0.4
            coverAreas:
              - x: 0.1
                y: 0.8
                width: 0.8
                height: 0.1
          tablet:
            title: Tablet
            allowedAspectRatios:
              square:
                title: Square
                value: 0.75
          smartphone:
            title: Smartphone
            allowedAspectRatios:
              landscape:
                title: Landscape
                value: 4 / 3

Usage in Fluid
==============

.. code-block:: html

    <f:for each="{data.image}" as="image">
        <f:image image="{image}" width="120" maxHeight="100"/>
    </f:for>
