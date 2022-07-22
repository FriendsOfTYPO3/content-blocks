.. include:: /Includes.rst.txt
.. _field_type_percent:

=======
Percent
=======

The "Percent" type generates a simple `<input>` field, which provides a slider
for value picking.

It corresponds with the TCA `type='input'` (default) with `range` and `slider`
properties.


Properties
==========

.. rst-class:: dl-parameters

default
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` integer
   :sep:`|` :aspect:`Default:` 0
   :sep:`|`

   Default value set if a new record is created.

range
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` array
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

   An array which defines an integer range within which the value must be. Keys:

   lower (integer/ float)
      Defines the lower integer value. Default: 0.

   upper (integer/ float)
      Defines the upper integer value. Default: 100.

   It is allowed to specify only one of both of them.

   Example:

   .. code-block:: yaml

      range:
        lower: 0
        upper: 100

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

slider
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` array
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

   Render a value slider next to the field. Available keys:

   step (integer / float)
      Set the step size the slider will use. For floating point values this can
      itself be a floating point value. Default: 1.

   width (integer, pixels)
      Define the width of the slider. Default: 100.

   Example:

   .. code-block:: yaml

      range:
        step: 1
        width: 100

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
          25: 25
          50: 50
          100: 100

Example
=======

.. code-block:: yaml

    group: common
    fields:
      - identifier: percent
        type: Percent
        properties:
          default: 0
          range:
            lower: 0
            upper: 100
          required: true
          size: 20
          slider:
            step: 1
            width: 100
          trim: true
          valuePicker:
            items:
              25: 25
              50: 50
              100: 100
