.. include:: /Includes.rst.txt
.. _field_type_file:

====
File
====

The "File" type generates a field for file relations.

It corresponds with the TCA `type='inline'` with FAL relation.


Properties
==========

.. rst-class:: dl-parameters

fileTypes
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` string
   :sep:`|` :aspect:`Default:` 'mixed'
   :sep:`|`

    Possible values: `image`, `video`, `audio`, `document` or `mixed` (all of
    the other file types).

allowedFileExtensions
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` string
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

    Comma separated list of file extensions that are allowed for this field.
    If no list is specified, system defaults are used.

enableImageManipulation
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` boolean
   :sep:`|` :aspect:`Default:` 'false'
   :sep:`|`

    This option can be used for file types `image` and `mixed`.

maxItems
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` integer
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

   Maximum number of child items. Defaults to a high value. JavaScript record
   validation prevents the record from being saved if the limit is not satisfied.

minItems
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` integer
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

   Minimum number of child items. Defaults to 0. JavaScript record validation
   prevents the record from being saved if the limit is not satisfied.
   The field can be set as required by setting `minItems` to at least 1.

Example
=======

.. code-block:: yaml

    group: common
    fields:
      - identifier: image
        type: File
        properties:
            fileType: image
            enableImageManipulation: true
            maxItems: 2
            minItems: 1
