.. include:: /Includes.rst.txt
.. _field_type_checkbox:
.. rst-class:: dl-parameters

========
Checkbox
========

The "Checkbox" type generates a number of checkbox fields. Selection of multiple items is enabled by default.

It corresponds with the TCA `type='check'` (default).


Properties
==========

default
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` string
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

   Default value set if a new record is created. As example, value 5 enabled
   first and third checkbox.

   Each bit corresponds to a check box. This is true even if there is only one
   checkbox which which then maps to bit-0.

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
      - identifier: checkboxes
        type: Checkbox
        properties:
          items:
            'one': 'The first'
            'two': 'The second'
            'three': 'The third'
          default: 2
