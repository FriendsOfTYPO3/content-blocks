.. include:: /Includes.rst.txt
.. _field_type_text:

====
Text
====

The :yaml:`Text` type generates a simple input field, possibly with additional
features applied.

Settings
========

..  confval-menu::
    :name: confval-text-options
    :display: table
    :type:
    :default:
    :required:

.. confval:: default
   :name: text-default
   :required: false
   :type: string

   Default value set if a new record is created.

.. confval:: max
   :name: text-max
   :required: false
   :type: integer

   Value for the `maxlength` attribute of the `<input>` field. Javascript
   prevents adding more than the given number of characters.

.. confval:: min
   :name: text-min
   :required: false
   :type: integer

   Value for the `minlength` attribute of the `<input>` field. Javascript
   prevents adding less than the given number of characters. Note: Empty values
   are still allowed. Use in combination with :yaml:`required` if this should be
   a non-empty value.

.. confval:: placeholder
   :name: text-placeholder
   :required: false
   :type: string

   Placeholder text for the field.

.. confval:: required
   :name: text-required
   :required: false
   :type: boolean
   :default: false

   If set, the field becomes mandatory.

.. confval:: size
   :name: text-size
   :required: false
   :type: integer

   Abstract value for the width of the `<input>` field.

.. confval:: valuePicker
   :name: text-valuePicker
   :required: false
   :type: array

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
