.. include:: /Includes.rst.txt
.. _field_type_color:

=====
Color
=====

The :yaml:`Color` type provides a simple color picker.

Settings
========

..  confval-menu::
    :name: confval-color-options
    :display: table
    :type:
    :default:
    :required:

.. confval:: default
   :name: color-default
   :required: false
   :type: string
   :default: ''

   Default value set if a new record is created.

.. confval:: required
   :name: color-required
   :required: false
   :type: boolean
   :default: false

   If set, the field will become mandatory.

.. confval:: searchable
   :name: color-searchable
   :required: false
   :type: boolean
   :default: true

   If set to false, the field will not be considered in backend search.

.. confval:: opacity
   :name: color-opacity
   :required: false
   :type: boolean
   :default: false

   Enables selection of opacity for the color.

.. confval:: valuePicker
   :name: color-valuePicker
   :required: false
   :type: array

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
          - label: 'Red'
            value: #FF0000
          - label: 'Green'
            value: #008000
          - label: 'Blue'
            value: #0000FF

Example
=======

Minimal
-------

.. code-block:: yaml

    name: example/color
    fields:
      - identifier: color
        type: Color

Advanced / use case
-------------------

.. code-block:: yaml

    name: example/color
    fields:
      - identifier: color
        type: Color
        autocomplete: true
        default: '#0000FF'
        valuePicker:
          items:
            - label: 'Red'
              value: #FF0000
            - label: 'Green'
              value: #008000
            - label: 'Blue'
              value: #0000FF
