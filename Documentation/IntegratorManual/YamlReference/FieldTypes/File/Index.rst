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

.. confval:: enableImageManipulation

   :Required: false
   :Type: boolean
   :Default: true

   If enabled, an additional image palette will be rendered, which consists of
   the fields `crop`, `title`, `alternative`, `link` and `description`. It also
   depends on the file type, whether this palette is rendered. For plain text
   files, this won't be shown for example.

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
        enableImageManipulation: true
        maxitems: 10
        minitems: 1
        allowed: common-image-types
