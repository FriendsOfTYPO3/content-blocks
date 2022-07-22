.. include:: /Includes.rst.txt
.. _field_type_image:

=====
Image
=====

The "Image" type generates a field for image relations.

It corresponds with the TCA `type='inline'` with FAL relation.


Properties
==========

.. rst-class:: dl-parameters

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

required
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` boolean
   :sep:`|` :aspect:`Default:` 'false'
   :sep:`|`

   If set, the field will become mandatory.

Example
=======

.. code-block:: yaml

    group: common
    fields:
      - identifier: image
        type: Image
        properties:
            maxItems: 2
            minItems: 1
            required: true
