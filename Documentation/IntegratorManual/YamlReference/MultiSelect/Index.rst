.. include:: /Includes.rst.txt
.. _field_type_multiselect:

===========
MultiSelect
===========

The "MultiSelect" type generates a select field where multiple items can be chosen.

It corresponds with the TCA `renderType='selectMultipleSideBySide'`.


Properties
==========

.. rst-class:: dl-parameters

default
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` string
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

   Default value set if a new record is created.

items
   :sep:`|` :aspect:`Required:` true
   :sep:`|` :aspect:`Type:` array
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

   Contains the elements for the selector box. Each item is an array with
   the first being the value transferred to the input field, and the second
   being the label in the select drop-down (LLL reference possible).

   Example:

   .. code-block:: yaml

      items:
        'one': 'The first'
        'two': 'The second'
        'three': 'The third'

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

size
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` integer
   :sep:`|` :aspect:`Default:` '20'
   :sep:`|`

   Abstract value for the width of the `<input>` field.

Example
=======

.. code-block:: yaml

    group: common
    fields:
      - identifier: selectSideBySide
        type: MultiSelect
        properties:
          default: 'one'
          items:
            'one': 'The first'
            'two': 'The second'
            'three': 'The third'
          maxItems: 2
          minItems: 1
          required: true
          size: 5
