.. include:: /Includes.rst.txt
.. _field_type_radiobox:

========
Radiobox
========

The "Radiobox" type generates a number of radio fields.

It corresponds with the TCA `type='radio'`.


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

   Contains the checkbox elements. Each item is an array with the first being
   the value transferred to the input field, and the second being the label in
   the select drop-down (LLL reference possible).

   Example:

   .. code-block:: yaml

      items:
        'one': 'The first'
        'two': 'The second'
        'three': 'The third'

Example
=======

.. code-block:: yaml

    group: common
    fields:
      - identifier: radioboxes
        type: Radiobox
        properties:
          default: 'two'
          items:
            'one': 'The first'
            'two': 'The second'
            'three': 'The third'
