.. include:: /Includes.rst.txt
.. _field_type_text:

====
Text
====

The "Text" type generates a simple `<input>` field, possibly with additional
features applied.

It corresponds with the TCA `type='input'` (default), however special variants
are defined as own field types.


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

max
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` integer
   :sep:`|` :aspect:`Default:` '700'
   :sep:`|`

   Value for the “maxlength” attribute of the `<input>` field. Javascript
   prevents adding more than the given number of characters.

placeholder
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` string
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

   Placeholder text for the field.

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
          'Want to join our team? Take the initiative!': Job offer general
          'We are looking for ...': Job offer specific


Example
=======

.. code-block:: yaml

    group: common
    fields:
      - identifier: text
        type: Text
        properties:
          autocomplete: false
          default: 'Default value'
          max: 15
          placeholder: 'Placeholder text'
          required: false
          size: 20
          trim: true
          valuePicker:
            items:
              'Want to join our team? Take the initiative!': Job offer general
              'We are looking for ...': Job offer specific
