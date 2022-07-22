.. include:: /Includes.rst.txt
.. _field_type_color:

=====
Color
=====

The "Color" type generates a simple `<input>` field, which provides a color picker.

It corresponds with the TCA `type='input'` and `renderType='colorPicker'`.


Properties
==========

.. rst-class:: dl-parameters

autocomplete
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` boolean
   :sep:`|` :aspect:`Default:` 'false'
   :sep:`|`

   Controls the autocomplete attribute of a given input field. If set to true
   (default false), adds attribute autocomplete="on" to the input field allowing
   browser auto filling the field.

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

size
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` integer
   :sep:`|` :aspect:`Default:` '20'
   :sep:`|`

   Abstract value for the width of the `<input>` field.

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
          '#FF0000': Red
          '#008000': Green
          '#0000FF': Blue

Example
=======

.. code-block:: yaml

    group: common
    fields:
      - identifier: color
        type: Color
        properties:
          autocomplete: true
          default: '#0000aa'
          required: false
          size: 5
          trim: true
          valuePicker:
            items:
              '#FF0000': Red
              '#008000': Green
              '#0000FF': Blue
