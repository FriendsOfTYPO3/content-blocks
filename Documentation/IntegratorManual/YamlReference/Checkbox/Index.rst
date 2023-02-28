.. include:: /Includes.rst.txt
.. _field_type_checkbox:
.. rst-class:: dl-parameters

========
Checkbox
========

The `Checkbox` type generates one or more checkbox fields.

It corresponds with the TCA :ref:`type='check' <t3tca:columns-check>` (default).

SQL overrides via `alternativeSql` allowed: yes.

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
   the label in the select drop-down (LLL reference possible), and the second
   being the value transferred to the input field.

   Example:

   .. code-block:: yaml

      items:
        [
          [ 'The first', 'one' ],
          [ 'The second', 'two' ],
          [ 'The third', 'three' ],
        ]

invertStateDisplay
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` boolean
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

   Inverts the display state (onf/off) of the toggle items.

   Example:

   .. code-block:: yaml

      invertStateDisplay: true

Example
=======

.. code-block:: yaml

    group: common
    fields:
      - identifier: checkboxes
        type: Checkbox
        properties:
          items:
            [
              [ 'The first', 'one' ],
              [ 'The second', 'two' ],
              [ 'The third', 'three' ],
            ]
          default: 2
