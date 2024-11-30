.. include:: /Includes.rst.txt
.. _field_type_color:

=====
Color
=====

:php:`type => 'color' // TCA`

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
          - [ 'Red', '#FF0000' ]
          - [ 'Green', '#008000' ]
          - [ 'Blue', '#0000FF' ]

For more advanced configuration refer to the :ref:`TCA documentation <t3tca:columns-color>`.

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
            - [ 'Red', '#FF0000' ]
            - [ 'Green', '#008000' ]
            - [ 'Blue', '#0000FF' ]
