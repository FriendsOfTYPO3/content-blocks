.. include:: /Includes.rst.txt
.. _field_type_file:

====
File
====

The `File` type generates a field for file relations.

It corresponds with the TCA :php:`type => 'file'`.

SQL overrides via `alternativeSql` allowed: no.

Settings
========

.. confval:: extendedPalette

   :Required: false
   :Type: boolean
   :Default: true

   If enabled, an additional image or media palette will be rendered. For
   image files it consists of the fields `crop`, `title`, `alternative`, `link`
   and `description`. For audio and media files it consists of `title`,
   `description` and `autoplay`. For other file types, like plain text, this
   option has no effect. Disable this option, if you don't need these additional
   fields.

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

For more advanced configuration refer to the :ref:`TCA documentation <t3tca:columns-file>`.

Example
=======

Minimal
-------

.. code-block:: yaml

    name: example/file
    group: common
    fields:
      - identifier: image
        type: File

Advanced / use case
-------------------

.. code-block:: yaml

    name: example/file
    group: common
    fields:
      - identifier: image
        type: File
        extendedPalette: false
        maxitems: 10
        minitems: 1
        allowed: common-image-types
