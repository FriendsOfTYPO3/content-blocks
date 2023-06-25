.. include:: /Includes.rst.txt
.. _field_type_text:

====
Text
====

The `Text` type generates a simple input field, possibly with additional
features applied.

It corresponds with the TCA :php:`type => 'input'`.

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

max
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` integer
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

   Value for the `maxlength` attribute of the `<input>` field. Javascript
   prevents adding more than the given number of characters.

min
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` integer
   :sep:`|` :aspect:`Default:` '700'
   :sep:`|`

   Value for the `minlength` attribute of the `<input>` field. Javascript
   prevents adding less than the given number of characters. Note: Empty values
   are still allowed. Use in combination with :yaml:`required` if this should be
   a non-empty value.

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

valuePicker
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` array
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

   Renders a select box with static values next to the input field. When
   a value is selected in the box, the value is transferred to the field. Keys:

   items (array)
      An array with selectable items. Each item is an array with the first being
      the label in the select drop-down (LLL reference possible) and the second
      being the value transferred to the input field.

   Example:

   .. code-block:: yaml

      valuePicker:
        items:
          [
            [ 'Want to join our team? Take the initiative!', 'Job offer general' ],
            [ 'We are looking for ...', 'Job offer specific' ],
          ]

For more advanced configuration refer to the :ref:`TCA documentation <t3tca:columns-input>`.

Examples
========

Minimal
-------

.. code-block:: yaml

    name: example/text
    group: common
    fields:
      - identifier: text
        type: Text

Advanced / use case
-------------------

.. code-block:: yaml

    name: example/text
    group: common
    fields:
      - identifier: text
        type: Text
        default: 'Default value'
        max: 15
        min: 4
        required: true
