
.. include:: /Includes.rst.txt
.. _field_type_money:

=====
Money
=====

The "Money" type generates a simple `<input>` field, which converts the input
to a floating point with 2 decimal positions, using the “.” (period) as
the decimal delimited (accepts also “,” for the same).

It corresponds with the TCA `type='input'` (default) and `eval='double2'`.


Properties
==========

.. rst-class:: dl-parameters

size
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` integer
   :sep:`|` :aspect:`Default:` '20'
   :sep:`|`

   Abstract value for the width of the `<input>` field.

required
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` boolean
   :sep:`|` :aspect:`Default:` 'false'
   :sep:`|`

   If set, the field will become mandatory.

trim
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` boolean
   :sep:`|` :aspect:`Default:` 'false'
   :sep:`|`

   If set, the PHP trim function is applied on the field's content.

valuePicker
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` array
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

   Renders a select box with static values next to the input field. When
   a value is selected in the box, the value is transferred to the field. Keys:

   items (array)
      An array with selectable items. Each item is an array with the first being
      the value transferred to the input field, and the second being the label
      in the select drop-down (LLL reference possible).

   Example:

   .. code-block:: yaml

      valuePicker:
        items:
          100: 100
          250: 250
          500: 500

Example
=======

.. code-block:: yaml

    group: common
    fields:
      - identifier: money
        type: Money
        properties:
          size: 20
          required: true
          trim: true
          valuePicker:
            items:
              100: 100
              250: 250
              500: 500
