.. include:: /Includes.rst.txt
.. _field_type_text:

====
Text
====

:php:`type => 'input' // TCA`

The :yaml:`Text` type generates a simple input field, possibly with additional
features applied.

Settings
========

.. confval:: default

   :Required: false
   :Type: string

   Default value set if a new record is created.

.. confval:: max

   :Required: false
   :Type: integer

   Value for the `maxlength` attribute of the `<input>` field. Javascript
   prevents adding more than the given number of characters.

.. confval:: min

   :Required: false
   :Type: integer

   Value for the `minlength` attribute of the `<input>` field. Javascript
   prevents adding less than the given number of characters. Note: Empty values
   are still allowed. Use in combination with :yaml:`required` if this should be
   a non-empty value.

.. confval:: placeholder

   :Required: false
   :Type: string

   Placeholder text for the field.

.. confval:: required

   :Required: false
   :Type: boolean
   :Default: false

   If set, the field becomes mandatory.

.. confval:: size

   :Required: false
   :Type: integer

   Abstract value for the width of the `<input>` field.

.. confval:: valuePicker

   :Required: false
   :Type: array

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
          - [ 'Want to join our team? Take the initiative!', 'Job offer general' ]
          - [ 'We are looking for ...', 'Job offer specific' ]

For more advanced configuration refer to the :ref:`TCA documentation <t3tca:columns-input>`.

Examples
========

Minimal
-------

.. code-block:: yaml

    name: example/text
    fields:
      - identifier: text
        type: Text

Advanced / use case
-------------------

.. code-block:: yaml

    name: example/text
    fields:
      - identifier: text
        type: Text
        default: 'Default value'
        min: 4
        max: 15
        required: true
