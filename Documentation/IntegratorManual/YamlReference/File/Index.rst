.. include:: /Includes.rst.txt
.. _field_type_file:

====
File
====

The `File` type generates a field for file relations.

It corresponds with the TCA :php:`type => 'file'`.

SQL overrides via `alternativeSql` allowed: no.

Properties
==========

.. rst-class:: dl-parameters

allowed
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` string|array
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

    Possible values: `common-image-types`, `common-media-types` or your custom
    list of file types.

enableImageManipulation
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` boolean
   :sep:`|` :aspect:`Default:` 'false'
   :sep:`|`

   If enabled, an additional image palette will be rendered, which consists of
   the fields `crop`, `title`, `alternative`, `link` and `description`.

maxitems
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` integer
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

   Maximum number of child items. Defaults to a high value. JavaScript record
   validation prevents the record from being saved if the limit is not satisfied.

minitems
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` integer
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

   Minimum number of child items. Defaults to 0. JavaScript record validation
   prevents the record from being saved if the limit is not satisfied.
   The field can be set as required by setting `minitems` to at least 1.

For more advanced configuration refer to the :ref:`TCA documentation <t3tca:columns-file>`.

Example
=======

Minimal
-------

.. code-block:: yaml

    group: common
    fields:
      - identifier: image
        type: File

Advanced / use case
-------------------

.. code-block:: yaml

    group: common
    fields:
      - identifier: image
        type: File
        properties:
            maxitems: 10
            minitems: 1
            enableImageManipulation: true
            allowed: common-image-types
