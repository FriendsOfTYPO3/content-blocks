.. include:: /Includes.rst.txt
.. _field_type_number:

======
Number
======

The "Number" type generates a simple `<input>` field, which allows only 0-9
characters in the field.

It corresponds with the TCA `type='input'` (default) and `eval='int'`,
`eval='float'` or `eval='double'` - depending on the format.


Properties
==========

.. rst-class:: dl-parameters

format
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` string
   :sep:`|` :aspect:`Default:` 'int'
   :sep:`|`

    Possible values: `int`, `float`, `double`.

default
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` double
   :sep:`|` :aspect:`Default:` 0
   :sep:`|`

   Default value set if a new record is created.

size
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` integer
   :sep:`|` :aspect:`Default:` '20'
   :sep:`|`

   Abstract value for the width of the `<input>` field.

range
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` array
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

   An array which defines an integer range within which the value must be. Keys:

   lower (integer)
      Defines the lower integer value. Default: 0.

   upper (integer)
      Defines the upper integer value. Default: none.

   It is allowed to specify only one of both of them.

   Example:

   .. code-block:: yaml

      range:
        lower: 10
        upper: 999

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
            [ 100, '100'],
            [ 250, '250'],
            [ 500, '500'],
          ]

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

Example
=======

.. code-block:: yaml

    group: common
    fields:
      - identifier: number
        type: Number
        properties:
          default: 10
          size: 20
          range:
            lower: 10
            upper: 999
          required: true
          valuePicker:
            items:
              [
                [ 100, '100'],
                [ 250, '250'],
                [ 500, '500'],
              ]
