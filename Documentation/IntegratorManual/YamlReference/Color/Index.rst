.. include:: /Includes.rst.txt
.. _field_type_color:

=====
Color
=====

The `Color` type provides a simple color picker.

It corresponds with the TCA :php:`type => 'color'`.

SQL overrides via `alternativeSql` allowed: yes.

Properties
==========

.. rst-class:: dl-parameters

default
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` string
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

   Default value set if a new record is created.

required
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` boolean
   :sep:`|` :aspect:`Default:` 'false'
   :sep:`|`

   If set, the field will become mandatory.

valuePicker
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` array
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

   Renders a select box with static values next to the input field. When
   a value is selected in the box, the value is transferred to the field. Keys:

   items (array)
      An array with selectable items. Each item is an array with the first being
      the label in the select drop-down (LLL reference possible), and the second
      being the value transferred to the input field.

   Example:

   .. code-block:: yaml

      valuePicker:
        items:
          [
            ['Red', '#FF0000'],
            ['Green', '#008000'],
            ['Blue', '#0000FF'],
          ]

For more advanced configuration refer to the :ref:`TCA documentation <t3tca:columns-color>`.

Example
=======

Minimal
-------

.. code-block:: yaml

    group: common
    fields:
      - identifier: color
        type: Color

Advanced / use case
-------------------

.. code-block:: yaml

    group: common
    fields:
      - identifier: color
        type: Color
        properties:
          autocomplete: true
          default: '#0000FF'
          valuePicker:
            items:
              [
                ['Red', '#FF0000'],
                ['Green', '#008000'],
                ['Blue', '#0000FF'],
              ]
