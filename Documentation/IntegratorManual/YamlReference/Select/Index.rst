.. include:: /Includes.rst.txt
.. _field_type_select:

======
Select
======

The "Select" type generates a simple select field.

It corresponds with the TCA `renderType='selectSingle'`.


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

prependLabel
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` string
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

   Label that is prepended before the select items, e.g. "Please choose ..."

   Example:

   .. code-block:: yaml

      prependLabel: 'Please choose'

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
      - identifier: select
        type: Select
        properties:
          default: 'one'
          items:
            'one': 'The first'
            'two': 'The second'
            'three': 'The third'
          prependLabel: 'Please choose'
          required: true
